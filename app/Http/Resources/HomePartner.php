<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomePartner extends JsonResource
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
            'ads_cart_code' => $this->ads_cart_code,
            'ads_cart_name' => $this->ads_cart_name,
            'ads_cart_start_date' => $this->ads_cart_start_date,
            'ads_cart_end_date' => $this->ads_cart_end_date,
            'ads_cart_amount' => $this->ads_cart_amount
        ];
    }
}

class UserSearchResult extends JsonResource
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
            'users_code' => $this->users_code,
            'users_name' => $this->name,
            'users_email' => $this->email,
            'users_hp' => $this->users_hp,
            'users_company' => $this->users_company,
            'users_company' => $this->users_company,
            'users_referral_code' => $this->users_referral_code,
            'users_npwp' => $this->users_npwp
        ];
    }
}
