<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Banner;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\BannerList;

class BannerController extends ApiController
{
    public function __construct()
    {

    }

    public function get()
    {
        $userType = (Route::currentRouteName() === "user.banner" ? 2 : 1);

        $data = Banner::where('slider_type', $userType)
            ->select(
                'slider_code',
                'slider_name',
                'slider_desc',
                'slider_filename'
            )->get();

        if (! $data->count() > 0) {
            return $this->sendError(2, 'Data tidak ditemukan');
        }

        $data_banner = BannerList::collection($data);

        return $this->sendResponse(0, 'Data ditemukan', $data_banner);
    }

}
