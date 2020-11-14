<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Partner extends JsonResource
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
            'partner_code' => $this->partner_code,
            'name' => $this->name,
            'partner_ktp' => $this->partner_ktp,
            'partner_email' => $this->email,
            'partner_hp' => $this->partner_hp,
            'partner_company' => $this->partner_company,
            'partner_address' => $this->partner_address,
            'provinsi_name' => $this->province_name,
            'kota_name' => $this->city_name,
            'kec_name' => $this->district_name,
            'kel_name' => $this->kelurahan_name

        ];
    }
}
