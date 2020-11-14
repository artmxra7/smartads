<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerList extends JsonResource
{

    public function toArray($request)
    {

        return [
            'slider_code' => $this->slider_code,
            'slider_name' => $this->slider_name,
            'slider_desc' => $this->slider_desc,
            'slider_filename' => $this->slider_filename,
            'slider_image_url' => asset('storage/' . $this->slider_filename),
        ];
    }
}

