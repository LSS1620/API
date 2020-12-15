<?php

namespace App\Http\Controllers;
use App\ItensProntuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests;
class ItensProntuarioController extends RestController
{
    /**
     * @var ItensProntuario
     */
    private $itensprontuario;

    /**
     * TerminaisController constructor.
     * @param Terminal $terminal
     */
    public function __construct( ItensProntuario $itensprontuario )
    {
        $this->itensprontuario = $itensprontuario;
    }
    /**
     * @return JsonResponse
     */
    public function index(){

        return $this->all($this->itensprontuario);
    }
    /**
     * @return JsonResponse
     * @param $id
     */
    public function findId($id){
        return $this->find($this->itensprontuario, $id);
    }
    public function store( Request $request ){
        //return 'teste';
        return $this->create($this->itensprontuario, $request);
    }
    public function update( Request $request, $id){
        return $this->edit($this->itensprontuario, $request, $id);
    }
    public function delete($id){
        return $this->remove($this->itensprontuario, $id);
    }
}