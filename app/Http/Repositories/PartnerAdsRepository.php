<?php

namespace App\Http\Repositories;

use App\Http\Resources\daftarAdsPartner as adsPartner;
use App\Http\Resources\daftarAdsOnbid as adsBid;

use Illuminate\Support\Facades\DB;
use App\Http\Resources\ambilAdsPartner;
use App\Http\Resources\ambilIklan;
use App\Http\Resources\CarPartnerResource;
use App\Http\Resources\getAdsForStep2;

class PartnerAdsRepository
{
    public function getAdsList($limit = '5', $offset = '0')
    {
        $partner = DB::table('ads_cart')
        ->select(
            'ads_cart.ads_cart_code',
            'ads_cart.ads_cart_name',
            'ads_cart.ads_cart_start_date',
            'ads_cart.ads_cart_end_date',
            'ads_cart.ads_cart_amount',
            'p.product_code',
            'p.product_name',
            'procloc.product_car_loc_name',
            'proc.product_car_name',
            'proc.product_car_filename',
            'ads_cart.ads_cart_status',
            'ads_cart.product_code',
            'ads_cart.product_car_code',
            'ads_cart.product_car_loc_code',
            'ads_cart.ads_cart_order_code',
            'aco.ads_cart_order_payment_status'

        )

        ->leftJoin('ads as ads', DB::raw('BINARY ads_cart.ads_cart_code'), '=', DB::raw('BINARY ads.ads_cart_code'))
        ->leftJoin('product as p', DB::raw('BINARY p.product_code'), '=', DB::raw('BINARY ads_cart.product_code'))
        ->leftJoin('product_car as proc', DB::raw('BINARY ads_cart.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))
        ->leftJoin('product_car_loc as procloc', DB::raw('BINARY ads_cart.product_car_loc_code'), '=', DB::raw('BINARY procloc.product_car_loc_code'))
        ->leftJoin('ads_cart_order as aco', DB::raw('BINARY ads_cart.ads_cart_order_code'), '=', DB::raw('BINARY aco.ads_cart_order_code'))
        ->where('ads_cart.ads_cart_status', '=', 1)
        ->where(function($cc)
        {
            $cc->where('aco.ads_cart_order_payment_status', '=', 1)
            ->orWhere('aco.ads_cart_order_payment_status', '=', 2);
        });

        if ($limit == null) {
            $resource = $partner->get();



        } else{
            $resource = $partner->limit($limit)
            ->offset($offset)
            ->get();
        }
       // dd($partner);
        $partners = adsPartner::collection($resource);

        return $partners;
    }

    public function getAdsListBid($partner_code, $limit = '5', $offset = '0')
    {
        $partner = DB::table('ads')
        ->select(

            'thisAdsCart.ads_cart_name',
            'thisAdsCart.ads_cart_start_date',
            'thisAdsCart.ads_cart_end_date',
            'thisAdsCart.ads_cart_amount',
            'ads.ads_code',
            'ads.product_code',
            'p.product_code',
            'p.product_name',
            'procloc.product_car_loc_name',
            'proc.product_car_name',
            'proc.product_car_filename',
            'thisAdsCart.ads_cart_status',
            'thisAdsCart.product_code',
            'thisAdsCart.product_car_code',
            'thisAdsCart.product_car_loc_code',
            'thisAdsCart.ads_cart_order_code',
            'aco.ads_cart_order_payment_status',
            'ads.ads_status',
            'ads.ads_cart_code',
            'aco.ads_cart_order_payment_status'

        )

        ->leftJoin('ads_cart as thisAdsCart', DB::raw('BINARY thisAdsCart.ads_cart_code'), '=', DB::raw('BINARY ads.ads_cart_code'))
        ->leftJoin('product as p', DB::raw('BINARY p.product_code'), '=', DB::raw('BINARY ads.product_code'))
        ->leftJoin('product_car as proc', DB::raw('BINARY ads.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))
        ->leftJoin('product_car_loc as procloc', DB::raw('BINARY ads.product_car_loc_code'), '=', DB::raw('BINARY procloc.product_car_loc_code'))
        ->leftJoin('ads_cart_order as aco', DB::raw('BINARY ads.ads_cart_order_code'), '=', DB::raw('BINARY aco.ads_cart_order_code'))
        ->where('ads.ads_status', '=', 1)
        ->where('thisAdsCart.ads_cart_status', '=', 4)
        ->where('ads.partner_code', '=', $partner_code)
        ->where(function($cc)
        {
            $cc->where('aco.ads_cart_order_payment_status', '=', 1)
            ->orWhere('aco.ads_cart_order_payment_status', '=', 2);
        });

        if ($limit == null) {
            $resource = $partner->get();
        } else{
            $resource = $partner->limit($limit)
            ->offset($offset)
            ->get();
        }

        $partners = adsBid::collection($resource);

        return $partners;
    }

    public function getAds($request, $partner_code)
    {
        $ads_cart = $this->getDataToAds($request,$partner_code);
        $getDataToCarPartner = $this->getDataToCarPartner($request,$partner_code);
        $checkStatusCart = $this->checkCartStatus($request->ads_cart_code);
    
        if (!$checkStatusCart) return 'ADS_NOT_AVAILABLE';

        DB::beginTransaction();
        try {
            $insert_ads = DB::table('ads')
                ->insert([
                    'partner_code' => $partner_code,
                    'ads_cart_code' => $request->ads_cart_code,
                    'ads_code' => generateFiledCode('ADS'),
                    'users_code' => $ads_cart->users_code,
                    'ads_cart_order_code' => $ads_cart->ads_cart_order_code,
                    'product_code' => $ads_cart->product_code,
                    'product_type' => $ads_cart->product_type,
                    'product_car_code' => $getDataToCarPartner->product_car_code,
                    'product_car_loc_code' => $ads_cart->product_car_loc_code,
                    'partner_car_code' => $getDataToCarPartner->partner_car_code,
                    'ads_name' => $ads_cart->ads_cart_name,
                    'ads_url' => $ads_cart->ads_cart_url,
                    'ads_amount' => $ads_cart->ads_cart_amount,
                    'ads_status' => $ads_cart->ads_cart_status,
                    'ads_start_date' => $ads_cart->ads_cart_start_date,
                    'ads_end_date' => $ads_cart->ads_cart_end_date,
                    'ads_status' => 1
                ]);

            $insert_ads_cart = DB::table('ads_cart')
                ->where('ads_cart.ads_cart_code', '=', $request->ads_cart_code)
                ->update([
                    'ads_cart_status' => 4
                ]);

            $thisAds = DB::table('ads_cart')
                ->select(
                    'ads_cart.*',
                    'p.product_name',
                    'p.product_code'
                    )
                ->leftJoin('product as p', DB::raw('BINARY p.product_code'), '=', DB::raw('BINARY ads_cart.product_code'))
                ->where('ads_cart_code', '=', $request->ads_cart_code)
                ->first();
            
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            logger('getAds(): catch ->', ['partner' => $partner_code, 'Exception' => $ex]);
            return false;
        }

        $insert_this_ads = (collect($thisAds)->count()) ? new ambilIklan($thisAds) : false;

        return $insert_this_ads;
    }

    public function getDataToAds($request, $partner_code)
    {
        $ads_cart = DB::table('ads_cart')
        ->select('ads_cart.*')
        ->where('ads_cart_code', '=', $request->ads_cart_code)
        ->first();

        return $ads_cart;
    }

    public function getDataToCarPartner($request, $partner_code)
    {
        $ads_cart = DB::table('partner_car')
        ->select('partner_car.*')
        ->where('partner_code', '=', $partner_code)
        ->first();

        return $ads_cart;
    }

    public function checkCartStatus($cart_code)
    {
        $cart_code = DB::table('ads_cart')
            ->select('ads_cart_status')
            ->where('ads_cart_code', '=', $cart_code)
            ->first();

        return ($cart_code->ads_cart_status == 1) ? true : false;
    }

    public function getCarPartnerForstep1($partner_code, $request, $limit = '5', $offset = '0')
    {
        $thisCar = DB::table('partner_car')
            ->select(
                'partner_car.partner_code',
                'partner_car.partner_car_code',
                'partner_car.partner_car_nopol',
                'partner_car.product_code',
                'partner_car.product_car_code',
                'p.product_name',
                'p.product_type',
                'proc.product_car_code',
                'proc.product_car_name'
            )

            ->leftJoin('product as p', DB::raw('BINARY partner_car.product_code'), '=', DB::raw('BINARY p.product_code'))
            ->leftJoin('product_car as proc', DB::raw('BINARY partner_car.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))
            ->where('partner_car.partner_code', '=', $partner_code)
            ->where('proc.product_car_code', '=', $request->product_car_code);

        if ($limit == null) {

            $resource = $thisCar->get();
        } else {

            $resource = $thisCar->limit($limit)
                ->offset($offset)
                ->get();
        }

        $getThisCar = CarPartnerResource::collection($resource);

        return $getThisCar;

    }

    public function detailAdsForstep2($request)
    {
        $thisAds = DB::table('ads_cart')
            ->select(
                'ads_cart.ads_cart_code',
                'ads_cart.ads_cart_name',
                'ads_cart.ads_cart_start_date',
                'ads_cart.ads_cart_end_date',
                'ads_cart.ads_cart_amount',
                'p.product_code',
                'p.product_name',
                'proc.product_car_name',
                'proc.product_car_filename',
                'pc.partner_car_nopol',
                'pc.partner_car_name'
            )
            ->leftJoin('product as p', DB::raw('BINARY p.product_code'), '=', DB::raw('BINARY ads_cart.product_code'))
            ->leftJoin('product_car as proc', DB::raw('BINARY ads_cart.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))
            ->leftJoin('partner_car as pc', DB::raw('BINARY pc.partner_car_code'), '=', DB::raw('BINARY pc.partner_car_code'))
            ->where('ads_cart.ads_cart_status', '=', 1)
            ->where('ads_cart.ads_cart_code', '=', $request->ads_cart_code)
            ->where('pc.partner_car_code', '=', $request->partner_car_code)
            ->first();

        $thisCar = DB::table('partner_car')
        ->select('partner_car.*')
        ->where('partner_car_code', '=', $request->partner_car_code)
        ->toSql();

        return ($thisAds) ? new getAdsForStep2($thisAds) : false;
        // return new getAdsForStep2($thisAds);
        // return getAdsForStep2::collection($thisAds);
    }
}
