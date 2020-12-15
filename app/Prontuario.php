<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prontuario extends Model
{
    protected $table ='prontuario';
    protected $fillable = ['idProntuario','idLote', 'codigoProntuario', 'descricao', 'statusRecebimento',
        'statusEnvio'];
    protected $primaryKey = 'idProntuario';
    public $timestamps = false;
}
