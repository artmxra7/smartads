<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarPartnerResource extends JsonResource
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
        'partner_car_code' => $this->partner_car_code,
        'partner_car_nopol' => $this->partner_car_nopol,
        'product_name' => $this->product_name,
        'product_type' => $this->product_type,
        'product_car_name' => $this->product_car_name
        ];
    }
}

class carPartnerDetail extends JsonResource
{
    public function toArray($request)
    {
        return [
            'partner_car_nopol' => $this->partner_car_nopol,
            'partner_car_code' => $this->partner_car_code,
            'partner_car_name' => $this->partner_car_name,
            'partner_car_merk' => $this->partner_car_merk,
            'product_name' => $this->product_name,
            'product_car_name' => $this->product_car_name,
            'partner_car_tahun' => $this->partner_car_tahun,
            'partner_car_nostnk' => $this->partner_car_nostnk,
            'partner_car_stnk_url' => asset('storage/' . $this->partner_car_filename)
        ];
    }
}
