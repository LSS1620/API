<?php

namespace App\Http\Controllers;
use App\Prontuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests;
class ProntuarioController extends RestController
{
    /**
     * @var Prontuario
     */
    private $prontuario;

    /**
     * TerminaisController constructor.
     * @param Terminal $terminal
     */
    public function __construct( Prontuario $prontuario )
    {
        $this->prontuario = $prontuario;
    }
    /**
     * @return JsonResponse
     */
    public function index(){

        return $this->all($this->prontuario);
    }
    /**
     * @return JsonResponse
     * @param $id
     */
    public function findId($id){
        return $this->find($this->prontuario, $id);
    }
    public function store( Request $request ){
        //return 'teste';
        return $this->create($this->prontuario, $request);
    }
    public function update( Request $request, $id){
        return $this->edit($this->prontuario, $request, $id);
    }
    public function delete($id){
        return $this->remove($this->prontuario, $id);
    }
}
