<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    protected $table ='disposisi';
    protected $dates=['tgl_disposisi'];
    public function user(){
        return $this->belongsTo('App\User','id_user','id');
    }
}
