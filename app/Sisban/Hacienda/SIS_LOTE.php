<?php

namespace App\Sisban\Hacienda;

use Illuminate\Database\Eloquent\Model;

class SIS_LOTE extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'SIS_LOTES';
    protected $primaryKey = 'id';
}
