<?php

namespace App\Http\Controllers\Api\Partner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Repositories\HomePartnerRepositroy;

class HomeController extends ApiController
{
    protected $homepartnerRepo;

    public function __construct(HomePartnerRepositroy $homepartnerRepo)
    {
        $this->homepartnerRepo = $homepartnerRepo;
    }

    public function test()
    {
        dd(Auth::user()->token()->id);
        return $this->sendResponse(0, 'Success');
    }

    public function getAdsList(Request $request)
    {
        $ads_list = $this->homepartnerRepo->getAllAds();
        // dd($ads_list->count());
        if ($ads_list->count() > 0) {
            $result = $this->sendResponse(0, 0, $ads_list);
        } elseif ($ads_list->count() == 0) {
            $result = $this->sendResponse(0, 'Data kosong');
        } else {
            $result = $this->sendError(2, 9);
        }

        return $result;
    }

}
