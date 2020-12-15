<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use App\Http\Requests;
use Psy\Util\Json;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Http\Controllers\Storage;
use ZanySoft\Zip\Zip;
use \ZipArchive;
class RestController extends Controller
{
     /**
     * @param Model $model
     * @param array $where
     * @return JsonResponse
     */
    protected function all( Model $model, $where=null ){
        try {

            if($where <> null)
                $all = $model->where($where['field'], $where['value'])->get();
            else
                $all = $model->all();

            if ($all->count())
                return $this->encodeJsonResponse(true, $all->toArray());
            else
                return $this->encodeJsonResponse(false, null, 404);
        }
        catch( \Exception $e ){
            return $this->encodeError( $e->getMessage(), $e->getCode() );
        }
    }
    
    /**
     * @param Model $model
     * @param $id
     * @return JsonResponse
     */
    protected function find( Model $model, $id ){
        try {
            /**
             * @var Model $obj
             */
            if(is_array($id))
                $obj = $model->where($id)->first();
            else
                $obj = $model->find($id);

            if($obj != null)
                return $this->encodeJsonResponse(true, $obj->toArray());
            else
                return $this->encodeJsonResponse(false, null, 404);
        }
        catch( \Exception $e ){
            return $this->encodeError( $e->getMessage(), $e->getCode() );
        }
    }

    /**
     * @param Model $model
     * @param $params
     * @return JsonResponse
     */
    protected function search( Model $model, $params ){
        try {

            $where = $this->makeWheres($params);

            if(count($where) > 0)
                $result = $model->where($where)->paginate(env('DB_PAGESIZE',20));
            else
                $result = $model->paginate(env('DB_PAGESIZE', 20));

            if($result->total() > 0)
                return $this->encodePagination($result);
            else
                return $this->encodeJsonResponse(false, null, 404);

        }
        catch( \Exception $e ){
            return $this->encodeError( $e->getMessage(), $e->getCode() );
        }
    }

    /**
     * @param Model $model
     * @param Request $request
     * @return JsonResponse
     */
    protected function create( Model $model, Request $request ){
        try {
            /**
             * @var Model $obj
             */
            $obj = $model->create($request->all());
            return $this->encodeJsonResponse(true,$obj->toArray());
        }
        catch( \Exception $e ){
            return $this->encodeError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param Model $model
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    protected function edit( Model $model, Request $request, $id ){
        try {
            /**
             * @var Model $obj
             */
            if(is_array($id))
                $obj    = $model->where($id)->first();
            else
                $obj    = $model->find($id);

            if($obj != null) {
                $obj->update($request->all());

                if(is_array($id))
                    $obj    = $model->where($id)->first();
                else
                    $obj    = $model->find($id);
            }
            else
                return $this->encodeJsonResponse(false,null,404);

            return $this->encodeJsonResponse(true, $obj->toArray());
        }
        catch( \Exception $e ){
            return $this->encodeError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param Model $model
     * @param $id
     * @return JsonResponse
     */
    protected function remove( Model $model, $id ){
        try {

            /**
             * @var Model $obj
             */
            if(is_array($id))
                $obj    = $model->where($id)->first();
            else
                $obj    = $model->find($id);

            if($obj != null)
                $obj->delete();
            else
                return $this->encodeJsonResponse(false,null,404);

            return $this->encodeJsonResponse(true,null);
        }
        catch( \Exception $e ){
            return $this->encodeError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $result
     * @param array $embeded
     * @param int $httpcode
     * @return JsonResponse
     */
    protected function encodeJsonResponse( $result ,$data=null, $httpcode=200 ){
         return new JsonResponse( array( 'return'=>$result, 'data'=>$data ), $httpcode );        
        
    }

    /**
     * @param $result
     * @return JsonResponse
     */
    protected function encodePagination( $result ){

        /**
         * instancia array
         */
        $json = array();

        /**
         * @var Model $r
         */
        foreach( $result as $r ){
            // adiciona todos elementos no array em formato de array
            $json[] = $r->toArray();
        }

        return new JsonResponse(array(
            'return'=>true,
            'data'=>$json,
            'next'=>$result->hasMorePages() ? ($result->currentPage() + 1) : null,
            'back'=>($result->currentPage() > 1) ? ($result->currentPage() - 1) : null ,
            'pageCount'=>$result->lastPage(),
            'pageSize'=>env('DB_PAGESIZE', 20),
            'total'=>$result->total()
        ));
    }

    /**
     * @param $message
     * @param $code
     * @return JsonResponse
     */
    protected function encodeError( $message, $code, $httpErrorCode = 500 ){
        return new JsonResponse( array( 'return'=>false, 'message'=>$message, 'code'=>$code ), $httpErrorCode);
    }

    protected function makeWheres( Array $params ){

        $result = [];

        /**
         * Exemplo de uso
         * $param['nome_do_campo'] = array('value'=>'%termo para consulta%', 'operador'=>'like');
         * $param['id'] = array('value'=>'valor', 'operador'=>'=');
         */
        foreach( $params as $p ) {
            if ($p['value'] <> '' && $p['value'] <> 'null' && $p['value'] <> "%null%" && $p['value'] <> "(null)") {
                if ($p['operador'] == 'like' || $p['operador'] == '=' || $p['operador'] == '<>') {
                    $result[] = [key($params), $p['operador'], $p['value']];
                } else if ($p['operador'] == 'between') {
                    /**
                     * Para passagem de periodos o parametro deve ser passado com duas data separadas pelo delimitador "|"
                     */
                    $values = explode('|', $p['value']);
                    $result[] = [key($params), '>=', $values[0]];
                    $result[] = [key($params), '<=', $values[1]];

                }
            }
            next($params);
        }

        return $result;
    }
    /**
     *@param $request
     *@return JsonResponse
     */
    protected function authentication(Request $request, $endPoint){
        $http = new Client();
            $response = $http->post($endPoint,[
                'form_params'=>[
                    'grant_type' => 'authorization_code',
                    'usuario' => 'sismais',
                    'password' => 'sis@2020',
                    //'code' => $request->code    
                ]
            ]);
            $request->session()->put('token',json_decode((string) $response->getBody(), true)['token']);
    }

    /**
     * metódo GET
     */
    protected function GET(Request $request,$endPoint){
        
        $http = new Client(); 
        $response = $http->get( $endPoint,[
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token=$request->session()->get('token'),
        ],
        ]); 
        $body = $response->getBody();
        $content =$body->getContents();
        //$arr = json_decode($content,TRUE);
        return json_decode($content,TRUE);
    }
    /**
     * função responsável por verificar se o Download foi realizado
     */
    protected function verifyDownloadVersao($vPastaVersao){
        if(!\file_exists($vPastaVersao.'end.ini') AND !\file_exists($vPastaVersao.'versao.zip')){
            $datetime =(new \DateTime())->format('Y-m-d h:i:s');
            $texto = 'Baixado'." ".'DATA'." ".$datetime;
            $file = \fopen($vPastaVersao.'/end.ini','w');
            \fwrite($file,$texto);
            fclose($file);
            return true;
        }
        return false;
    }
    /**
     * função responsável para realizar o dowload do arquivo do para o atualizador
     */
    protected function downloadFile(Request $request,$endPoint, $vPastaVersao){
        $path = $vPastaVersao;
        $file_path = \fopen($path,'w');
        $http = new Client();
        $response = $http->get( $endPoint,['save_to' =>$file_path],[
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token=$request->session()->get('token'),
            ],
        ]);
    }
    /**
     * metódo PUT
     */
    protected function PUT(Request $request,$endPoint){
        // try{
        //     $headers = array( 'Accept' => 'application/json',
        //         'Authorization' => 'Bearer '.$token=$request->session()->get('token'),
        //         'Content-Type' => 'application/x-www-form-urlencoded',
        //         'Content-Type' => 'application/json');
        //     $http = new Client();
        //     $response = $http->put( $endPoint,null,['query'=>$request],['headers'=>$headers])->send->json(); 
        //     return json_decode($response->getBody()->getContents());
        // }catch (RequestException $e){
        //     $response = $this->StatusCodeHandling($e);return $response;
        // }
        $client = new Client();

        $response = $client->put($endPoint, [
            'form_params'            => [
                'id_versao_atual'=> $request->id_versao_atual,
                'id_versao_anterior'=>$request->id_versao_anterior,
                'data_ult_atualizacao' => $request->data_ult_atualizacao    
            ],
            'headers'         => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token=$request->session()->get('token')
            ]
                
            
        ]);
        $teste = $response->getBody();
        $content =$teste->getContents();
        return json_decode($content, true);
    }
}
