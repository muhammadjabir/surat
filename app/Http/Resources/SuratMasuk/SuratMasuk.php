<?php

namespace App\Http\Resources\SuratMasuk;

use Illuminate\Http\Resources\Json\JsonResource;

class SuratMasuk extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $data = [
            'id'=>$this->id,
            'dikirim' => $this->tgl_kirim ? $this->tgl_kirim->format('Y-m-d H:i:s') : '',
            'tanggal_terima'=> $this->tgl_terima->format('Y-m-d'),
            'nomor_surat' => $this->nomor_surat,
            'tanggal_surat' =>  $this->tgl_surat->format('Y-m-d'),
            'asal_surat' => $this->asal_surat,
            'perihal' => $this->perihal,
            'status' => $this->status,
            'file_surat' => asset('storage/'.$this->file_surat),
            'keterangan' =>$this->keterangan,
            'tindak_lanjut' => $this->tindak_lanjut
        ];

        return $data;
    }
}
