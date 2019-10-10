<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $connection = 'mysql';
    protected $table = 'rh_mtrab';
}
