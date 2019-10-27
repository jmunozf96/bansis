<?php

namespace App\Sisban\Enfunde;

use Illuminate\Database\Eloquent\Model;

class ENF_SEC_LOTERO extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ENF_SEC_LOTEROS';
    protected $primaryKey = 'id';
    protected $dateFormat = 'M j Y h:i:s';

    public function lotero()
    {
        return $this->hasOne('App\Sisban\Enfunde\ENF_LOTERO', 'idlotero', 'id');
    }

    public function lote()
    {
        return $this->hasOne('App\Sisban\Hacienda\SIS_LOTE', 'id', 'idlote');
    }
}
