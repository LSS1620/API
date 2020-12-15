<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItensProntuario extends Model
{
    protected $table ='itensProntuario';
    protected $fillable = ['idItemProntuario','cpf', 'pagina', 'idProntuarioFk'];
    protected $primaryKey = 'idItemProntuario';
    public $timestamps = false;
}
