<?php

namespace App\Http\Resources\JenisSurat;

use Illuminate\Http\Resources\Json\JsonResource;

class JenisSurat extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'jenis_surat' => "{$this->nama_surat} ({$this->inisial})",
            'slug'=>$this->slug
        ];
    }
}
