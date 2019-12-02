<?php

namespace App\Sisban\Enfunde;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ENF_ENFUNDE extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'ENF_ENFUNDE';
    protected $primaryKey = 'id';
    protected $dateFormat = 'd-m-Y H:i:s';

    public function detalle()
    {
        return $this->hasMany('App\Sisban\Enfunde\ENF_DET_ENFUNDE', 'idenfunde', 'id');
    }

    public function lotero()
    {
        return $this->hasOne('App\Sisban\Enfunde\ENF_LOTERO', 'id', 'idlotero');
    }

    public function scopeSearch($q)
    {
        $data = request()->search;
        return empty(request()->search) ? $q : $q->whereHas('lotero', function ($query) use ($data) {
            $query->where('nombres', 'like', '%' . $data . '%');
            $query->whereNotNull('nombres');
        });
    }
}
