<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'SIS_CALENDARIO_DOLE';
    protected $primaryKey = 'idcalendar';
}
