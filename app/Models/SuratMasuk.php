<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    protected $table = 'surat_masuk';
    protected $dates = ['tgl_terima','tgl_surat','tgl_kirim'];

    public function scopeFillters($q, $request,$jenis)
    {
        return $q->where(function($q) use($request,$jenis){
            $q->where('nomor_surat','LIKE',"%$request->keyword%");
            $q->where('jenis_surat',$jenis->id);
        })
        ->orWhere(function($q) use($request,$jenis){
            $q->where('asal_surat','LIKE',"%$request->keyword%");
            $q->where('jenis_surat',$jenis->id);
        })
        ->orWhere(function($q) use($request,$jenis){
            $q->whereYear('tgl_terima',$request->keyword);
            $q->where('jenis_surat',$jenis->id);
        })
        ->orWhere(function($q) use($request,$jenis){
            $q->where('perihal','LIKE',"%$request->keyword%");
            $q->where('jenis_surat',$jenis->id);
        });
    }

}
