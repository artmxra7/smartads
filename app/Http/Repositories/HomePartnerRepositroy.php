<?php

namespace App\Http\Repositories;

use App\User;
use App\Partner;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Users;
use App\Http\Repositories\UserService;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\HomePartner;

class HomePartnerRepositroy
{
    function __construct()
    {

    }

    public function getAllAds()
    {
        $ads = DB::table('ads_cart')->select(
            'ads_cart.ads_cart_code',
            'ads_cart.ads_cart_name',
            'ads_cart.ads_cart_start_date',
            'ads_cart.ads_cart_end_date',
            'ads_cart.ads_cart_amount'
        )
        ->where('ads_cart.ads_cart_status', 1)
        ->where(function ($query) {
            $query->where('aco.ads_cart_order_payment_status', 1)
                ->orWhere('aco.ads_cart_order_payment_status', 2);
         })
        ->leftjoin('ads_cart_order as aco', 'aco.ads_cart_order_code', '=', 'ads_cart.ads_cart_order_code')->get();

        return HomePartner::collection($ads);
    }
}
