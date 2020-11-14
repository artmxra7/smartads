<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Resources\Ads as AdsResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AdsRequestLists;
use App\Http\Resources\daftarAds as daftarResource;
use App\Http\Resources\daftarAdsCart as daftarCart;
use App\Http\Resources\daftarAdsOnbid as OnBid;
use App\Http\Resources\OnBidDetail;
use App\Http\Resources\daftarHistoryAds;
use App\Http\Resources\adsCount;
use Carbon\Carbon;
use function GuzzleHttp\json_decode;

class UserAdsRepository
{

    public function getAdsDetail($ads_code)
    {
        $list_details = DB::table('ads')
        ->select(
            'ads.ads_code',
            'ads.ads_name',
            'ads.product_code',
            'ads.partner_car_code',
            'ads.product_car_code',
            'ads.product_car_loc_code',
            'ads.ads_amount',
            'ads.ads_start_date',
            'ads.ads_end_date',
            'p.product_name',
            'partc.partner_car_code',
            'partc.partner_car_nopol',
            'procloc.product_car_loc_name',
            'proc.product_car_name',
            'proc.product_car_filename'

        )
        ->leftJoin('product as p', DB::raw('BINARY ads.product_code'), '=', DB::raw('BINARY p.product_code'))
        ->leftJoin('partner_car as partc', DB::raw('BINARY ads.partner_car_code'), '=', DB::raw('BINARY partc.partner_car_code'))
        ->leftJoin('product_car as proc', DB::raw('BINARY ads.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))
        ->leftJoin('product_car_loc as procloc', DB::raw('BINARY ads.product_car_loc_code'), '=', DB::raw('BINARY procloc.product_car_loc_code'))


        ->where('ads.ads_code', '=', $ads_code)
        ->where('ads.ads_status', '=', 1)
        ->first();

        // $list_detail = new AdsResource($list_details);
        $list_detail = (collect($list_details)->count()) ? new AdsResource($list_details) : false;

        return $list_detail;
    }

    public function getTable($param = array(), $exists = false)
    {
        $result = DB::table($param['table'])
            ->select($param['select']);

        if (isset($param['condition']['key']) && isset($param['condition']['value'])) {
            $result->where($param['condition']['key'], $param['condition']['value']);
        }

        if ($exists === true) {
            return $result->exists();
        } else {
            return $result->get();
        }
    }

    public function getAdsList($users_code, $limit ='5', $offset = '0')
    {
        $users = DB::table('users')
            ->select(
                'users.users_code',
                'ads.ads_code',
                'ads.ads_name',
                'ads.ads_cart_code',
                'ads.users_code',
                'ads.product_code',
                'ads.partner_car_code',
                'ads.product_car_code',
                'ads.product_car_loc_code',
                'ads.ads_amount',
                'ads.ads_start_date',
                'ads.ads_end_date',
                'p.product_name',
                'partc.partner_car_code',
                'partc.partner_car_nopol',
                'procloc.product_car_loc_name',
                'proc.product_car_name',
                'proc.product_car_filename',
                'adc.ads_cart_status',
                'adc.users_code'

            )
            ->leftJoin('ads as ads', DB::raw('BINARY ads.users_code'), '=', DB::raw('BINARY users.users_code'))
            ->leftJoin('ads_cart as adc', DB::raw('BINARY users.users_code'), '=', DB::raw('BINARY adc.users_code'))
            ->leftJoin('product as p', DB::raw('BINARY ads.product_code'), '=', DB::raw('BINARY p.product_code'))
            ->leftJoin('partner_car as partc', DB::raw('BINARY ads.partner_car_code'), '=', DB::raw('BINARY partc.partner_car_code'))
            ->leftJoin('product_car as proc', DB::raw('BINARY ads.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))
            ->leftJoin('product_car_loc as procloc', DB::raw('BINARY ads.product_car_loc_code'), '=', DB::raw('BINARY procloc.product_car_loc_code'))
            ->where('users.users_code', '=', $users_code)
            ->where('adc.ads_cart_status', '=', 1);

        if ($limit == null) {
            $resource = $users->get();
        } else{
            $resource = $users->limit($limit)
            ->offset($offset)
            ->get();
        }
        $user = daftarResource::collection($resource);

        return $user;
    }

    public function getOnBidList($users_code,$limit ='5',$offset = '0')
    {
        $users = DB::table('users')
        ->select(
            'users.users_code',
            'adscart.ads_cart_order_code',
            'adscart.ads_cart_order_code',
            'adscart.ads_cart_name',
            'ads.users_code',
            'ads.ads_cart_order_code',
            'adscart.ads_cart_code',
            'adscart.users_code',
            'adscart.ads_cart_start_date',
            'adscart.ads_cart_end_date',
            'aco.ads_cart_order_payment_status',
            'p.product_name',
            'partc.partner_car_code',
            'partc.partner_car_nopol',
            'procloc.product_car_loc_name',
            'proc.product_car_name',
            'proc.product_car_filename'

        )

        ->leftJoin('ads as ads', DB::raw('BINARY users.users_code'), '=', DB::raw('BINARY ads.users_code'))

        ->leftJoin('ads_cart as adscart', DB::raw('BINARY adscart.users_code'), '=', DB::raw('BINARY users.users_code'))

        ->leftJoin('product as p', DB::raw('BINARY ads.product_code'), '=', DB::raw('BINARY p.product_code'))

        ->leftJoin('partner_car as partc', DB::raw('BINARY ads.partner_car_code'), '=', DB::raw('BINARY partc.partner_car_code'))

        ->leftJoin('product_car as proc', DB::raw('BINARY ads.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))

        ->leftJoin('ads_cart_order as aco', DB::raw('BINARY adscart.ads_cart_order_code'), '=', DB::raw('BINARY aco.ads_cart_order_code'))

        ->leftJoin('product_car_loc as procloc', DB::raw('BINARY ads.product_car_loc_code'), '=', DB::raw('BINARY procloc.product_car_loc_code'))


        ->where('users.users_code', '=', $users_code)
        ->where('adscart.ads_cart_status', '=', 1)
        ->where(function($cc)
        {
            $cc->where('aco.ads_cart_order_payment_status', '=', 1)
            ->orWhere('aco.ads_cart_order_payment_status', '=', 2);
        });
        if ($limit == null) {
            $resource = $users->get();
        } else{
            $resource = $users->limit($limit)
            ->offset($offset)
            ->get();

        }
        $user = OnBid::collection($resource);
        return $user;
    }

    public function getOnBidDetail($ads_cart_code)
    {
        $list_details = DB::table('ads_cart')
        ->select(
            'ads_cart.ads_cart_code',
            'ads_cart.ads_cart_name',
            'ads_cart.product_car_code',
            'ads_cart.ads_cart_amount',
            'ads_cart.product_car_loc_code',
            'ads_cart.ads_cart_start_date',
            'ads_cart.ads_cart_end_date',
            'ads_cart.product_code',
            'p.product_name',
            'procloc.product_car_loc_name',
            'p.product_name',
            'proc.product_car_name',
            'proc.product_car_filename'
            )

        ->leftJoin('product as p', DB::raw('BINARY ads_cart.product_code'), '=', DB::raw('BINARY p.product_code'))

        ->leftJoin('product_car_loc as procloc', DB::raw('BINARY ads_cart.product_car_loc_code'), '=', DB::raw('BINARY procloc.product_car_loc_code'))

        ->leftJoin('product_car as proc', DB::raw('BINARY ads_cart.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))

        ->where('ads_cart.ads_cart_code', '=', $ads_cart_code)

        ->first();

        // $list_detail = OnBidDetail::collection($list_details);
        //dd((collect($list_details)->count()));
        $list_detail = (collect($list_details)->count() ? new OnBidDetail($list_details) : false);

        return $list_detail;
    }


    public function getCartList($users_code, $limit ='5', $offset = '0')
    {
        $users = DB::table('users')
            ->select(
                'users.users_code',
                'ads.users_code',
                'ads.ads_cart_order_code',
                'ads.ads_cart_code',
                'ads.ads_cart_name',
                'ads.ads_cart_amount',
                'ads.product_code',
                'ads.product_car_code',
                'ads.ads_cart_status',
                'aco.ads_cart_order_code',
                'aco.ads_cart_order_grand_total',
                'p.product_name',
                'procloc.product_car_loc_name',
                'proc.product_car_name',
                'proc.product_car_filename'

            )


            ->leftJoin('ads_cart as ads', DB::raw('BINARY users.users_code'), '=', DB::raw('BINARY ads.users_code'))

            ->leftJoin('product as p', DB::raw('BINARY ads.product_code'), '=', DB::raw('BINARY p.product_code'))

            ->leftJoin('product_car as proc', DB::raw('BINARY ads.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))

            ->leftJoin('product_car_loc as procloc', DB::raw('BINARY ads.product_car_loc_code'), '=', DB::raw('BINARY procloc.product_car_loc_code'))

            ->leftJoin('ads_cart_order as aco', DB::raw('BINARY aco.ads_cart_order_code'), '=', DB::raw('BINARY ads.ads_cart_order_code'))

            ->where('ads.users_code', '=', $users_code)

            ->where('ads.ads_cart_status', '=', 0)

            ->where(function($adschart){

            $adschart->where('aco.ads_cart_order_payment_status', '=', 0)
            ->orWhere('aco.ads_cart_order_payment_status', '=', 2);
            });



        if ($limit == null) {

            $resource = $users->get();

            //dd($resource);
        } else{
            $resource = $users->limit($limit)
            ->offset($offset)
            ->get();

           //dd($resource);
        }
        $user = daftarCart::collection($resource);

        return $user;
    }

    public function createAds($user_code, $thisData)
    {
        $cartCounts = (int) $thisData['ads_cart_count'];

        DB::beginTransaction();
        try {

            $ads_cart_order = DB::table('ads_cart_order')->insert(
                            [
                            'ads_cart_order_code' => $thisData['ads_cart_order_code'],
                            'ads_cart_order_invoice' => generateFiledCode('INV'),
                            'ads_cart_order_grand_total' => calculateTotalPriceAds($cartCounts),
                            'ads_cart_order_date_created' => Carbon::now(),
                            'ads_cart_order_payment_status' => 0
                            ]
                        );

            for ($i = 1; $i < $cartCounts+1; $i++) {
                $ads_cart[$i] = DB::table('ads_cart')->insert(
                            [
                                'users_code' => $user_code,
                                'ads_cart_name' => $thisData['ads_cart_name'],
                                'ads_cart_code' => generateFiledCode('CART'),
                                'ads_cart_start_date' => $thisData['ads_cart_start_date'],
                                'ads_cart_end_date' => $thisData['ads_cart_end_date'],
                                'product_code' => $thisData['product_code'],
                                'product_type' => $thisData['product_type'],
                                'product_car_code' => $thisData['product_car_code'],
                                'product_car_loc_code' => $thisData['product_car_loc_code'],
                                'ads_cart_order_code' => $thisData['ads_cart_order_code'],
                                'ads_cart_url' => $thisData['ads_cart_url'],
                                'ads_cart_status' => 0,
                                'ads_cart_amount' => 10000000
                            ]
                        );

            }
            // dd($ads_cart);

            DB::commit();

        } catch (\Exception $ex) {
            DB::rollBack();
            logger('createAds(): catch ->', ['user' => $user_code, 'Exception' => $ex]);
            return false;
        }

        return true;
    }

    public function setPayment($users_code, $request)
    {
        $carts_code_selected_tmp = '{"0": "CART-4219550701309778003","1": "CART-4219550701302592086"}';
        $ads_cart_order_code = $request->ads_cart_order_code;
        $carts_code = json_decode($request->carts_code_selected, true);

        DB::beginTransaction();
        try {
            $ads_cart_order = DB::table('ads_cart_order')
                ->where('ads_cart_order_code', $ads_cart_order_code)
                ->update([
                    'ads_cart_order_payment_status' => 1,
                    'ads_cart_order_payment_type' => $request->payment_type == 'ipg' ? 1 : 0,
                    'ads_cart_order_date_updated' => Carbon::now()
                ]);

            for ($i=0; $i < count($carts_code); $i++) {
                $ads_cart = DB::table('ads_cart')
                    ->where('ads_cart_code', $carts_code[$i])
                    ->where('ads_cart_order_code', $ads_cart_order_code)
                    ->update([
                        'ads_cart_status' => 1,
                        'ads_cart_date_updated' => Carbon::now()
                    ]);
            }

            $change_status_cart_not_selected = DB::table('ads_cart')
                ->where(function ($query) use ($ads_cart_order_code, $carts_code){
                    $query->whereNotIn('ads_cart_code', $carts_code)
                        ->where('ads_cart_order_code', $ads_cart_order_code);
                })
                ->update([
                    'ads_cart_status' => 3
                ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            logger('setPayment(): catch ->', ['user' => $users_code, 'Exception' => $ex]);
            return false;
        }
        return true;

    }

    public function getAdsHistoryList($users_code, $limit ='5', $offset = '0')
    {
        $users = DB::table('users')
            ->select(
                'users.users_code',
                'ads.ads_code',
                'ads.ads_name',
                'ads.ads_status',
                'ads.users_code',
                'ads.product_code',
                'ads.partner_car_code',
                'ads.product_car_code',
                'ads.product_car_loc_code',
                'ads.ads_amount',
                'ads.ads_start_date',
                'ads.ads_end_date',
                'p.product_name',
                'partc.partner_car_code',
                'partc.partner_car_nopol',
                'procloc.product_car_loc_name',
                'proc.product_car_name',
                'proc.product_car_filename'

            )
            ->leftJoin('ads as ads', DB::raw('BINARY ads.users_code'), '=', DB::raw('BINARY users.users_code'))
            ->leftJoin('product as p', DB::raw('BINARY ads.product_code'), '=', DB::raw('BINARY p.product_code'))
            ->leftJoin('partner_car as partc', DB::raw('BINARY ads.partner_car_code'), '=', DB::raw('BINARY partc.partner_car_code'))
            ->leftJoin('product_car as proc', DB::raw('BINARY ads.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))
            ->leftJoin('product_car_loc as procloc', DB::raw('BINARY ads.product_car_loc_code'), '=', DB::raw('BINARY procloc.product_car_loc_code'))
            ->where('users.users_code', '=', $users_code)
            ->where('ads.ads_status', '=', 3);

        if ($limit == null) {
            $resource = $users->get();
        } else{
            $resource = $users->limit($limit)
            ->offset($offset)
            ->get();
        }

        $user = daftarHistoryAds::collection($resource);

        return $user;
    }


    public function getAdsCount($users_code, $ads_cart_order_code)
    {
        $cart_count = DB::table('ads_cart')
        ->select(
                'ads_cart.users_code',
                'ads_cart.ads_cart_order_code',
                'act.ads_cart_order_code',
                'act.ads_cart_order_payment_status',
                'ads.ads_cart_status'
        )
        ->leftJoin('ads_cart_order as act', DB::raw('BINARY ads_cart.ads_cart_order_code'), '=', DB::raw('BINARY act.ads_cart_order_code'))
        ->select(DB::raw("COUNT(*) as ads_cart_count"))
            ->where('ads_cart.users_code', '=',  $users_code)
            ->where('ads_cart.ads_cart_status', '=', 0)
            ->where(function($cc)
            {
                $cc->where('act.ads_cart_order_payment_status', '=', 0)
                ->orWhere('act.ads_cart_order_payment_status', '=', 2);
            });

        $resource = $cart_count->first();

        //dd( $users_code);

        $cart_counts = (collect($resource)->count() ? new adsCount($resource) : false);

        return $cart_counts;
    }

    public function getAdsHistoryDetail($ads_code)
    {
        $list_details = DB::table('ads')
        ->select(
            'ads.ads_code',
            'ads.ads_name',
            'ads.product_code',
            'ads.partner_car_code',
            'ads.product_car_code',
            'ads.product_car_loc_code',
            'ads.ads_amount',
            'ads.ads_start_date',
            'ads.ads_end_date',
            'p.product_name',
            'partc.partner_car_code',
            'partc.partner_car_nopol',
            'procloc.product_car_loc_name',
            'proc.product_car_name',
            'proc.product_car_filename'

        )
        ->leftJoin('product as p', DB::raw('BINARY ads.product_code'), '=', DB::raw('BINARY p.product_code'))
        ->leftJoin('partner_car as partc', DB::raw('BINARY ads.partner_car_code'), '=', DB::raw('BINARY partc.partner_car_code'))
        ->leftJoin('product_car as proc', DB::raw('BINARY ads.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))
        ->leftJoin('product_car_loc as procloc', DB::raw('BINARY ads.product_car_loc_code'), '=', DB::raw('BINARY procloc.product_car_loc_code'))


        ->where('ads.ads_code', '=', $ads_code)
        ->first();

        $list_detail = (collect($list_details)->count()) ? new AdsResource($list_details) : false;

        return $list_detail;
    }
}
