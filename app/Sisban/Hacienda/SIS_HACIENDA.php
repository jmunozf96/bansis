<?php

namespace App\Sisban\Hacienda;

use Illuminate\Database\Eloquent\Model;

class SIS_HACIENDA extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'SIS_HACIENDA';
    protected $primaryKey = 'id_hac';
}
