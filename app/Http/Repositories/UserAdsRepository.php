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
use App\Http\Resources\daftarUserAdsOnbid as OnBid;
use App\Http\Resources\OnBidDetail;
use App\Http\Resources\daftarHistoryAds;
use App\Http\Resources\adsCount;
use App\Http\Resources\AdsDetail;
use App\Http\Resources\PartnerInfo;
use App\Utility\WalletUtility;
use Carbon\Carbon;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class UserAdsRepository
{

    /**
     * 
     * @deprecated no longer in use
     */
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
            'ads.ads_status',
            'tr.harga_user',
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
        ->leftJoin('transaction as tr', DB::raw('BINARY tr.ads_cart_uuid'), '=', DB::raw('BINARY ads.ads_cart_uuid'))

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
            ->where('adc.ads_cart_status', '=', 4); // STATUS BID

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
            'adscart.ads_cart_code',
            'adscart.users_code',
            'adscart.ads_cart_start_date',
            'adscart.ads_cart_end_date',
            'aco.ads_cart_order_payment_status',
            'p.product_name',
            'procloc.product_car_loc_name',
            'proc.product_car_name',
            'proc.product_car_filename'

        )
        ->leftJoin('ads_cart as adscart', DB::raw('BINARY adscart.users_code'), '=', DB::raw('BINARY users.users_code'))
        ->leftJoin('product as p', DB::raw('BINARY adscart.product_code'), '=', DB::raw('BINARY p.product_code'))
        ->leftJoin('product_car as proc', DB::raw('BINARY adscart.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))
        ->leftJoin('ads_cart_order as aco', DB::raw('BINARY adscart.ads_cart_order_code'), '=', DB::raw('BINARY aco.ads_cart_order_code'))
        ->leftJoin('product_car_loc as procloc', DB::raw('BINARY adscart.product_car_loc_code'), '=', DB::raw('BINARY procloc.product_car_loc_code'))
        ->where('users.users_code', '=', $users_code)
        ->where('adscart.ads_cart_status', '=', 1)  // STATUS ONBID
        ->where(function($cc){
            $cc->where('aco.ads_cart_order_payment_status', '=', 1) // STATUS PAID
            ->orWhere('aco.ads_cart_order_payment_status', '=', 2); // STATUS SEBAGIAN
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

    public function getGlobalAdsDetail($ads_cart_code)
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
            'ads_cart.ads_cart_status_position',
            'tr.target',
            'tr.harga_user',
            'p.product_name',
            'procloc.product_car_loc_name',
            'p.product_name',
            'proc.product_car_name',
            'proc.product_car_filename'
            )

        ->leftJoin('product as p', DB::raw('BINARY ads_cart.product_code'), '=', DB::raw('BINARY p.product_code'))
        ->leftJoin('product_car_loc as procloc', DB::raw('BINARY ads_cart.product_car_loc_code'), '=', DB::raw('BINARY procloc.product_car_loc_code'))
        ->leftJoin('product_car as proc', DB::raw('BINARY ads_cart.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))
        ->leftJoin('transaction as tr', DB::raw('BINARY tr.ads_cart_uuid'), '=', DB::raw('BINARY ads_cart.ads_cart_uuid'))
        ->where('ads_cart.ads_cart_code', '=', $ads_cart_code)
        ->first();

        $list_detail = (collect($list_details)->count() ? new AdsDetail($list_details) : false);

        return $list_detail;
    }


    public function getCartList($users_code, $limit ='5', $offset = '0')
    {
        // $users = DB::table('users')
        //     ->select(
        //         'users.users_code',
        //         'ads.users_code',
        //         'ads.ads_cart_uuid',
        //         'ads.ads_cart_order_code',
        //         'ads.ads_cart_code',
        //         'ads.ads_cart_name',
        //         'ads.ads_cart_amount',
        //         'ads.product_code',
        //         'ads.product_car_code',
        //         'ads.ads_cart_status',
        //         'aco.ads_cart_order_code',
        //         'aco.ads_cart_order_grand_total',
        //         'p.product_name',
        //         'procloc.product_car_loc_name',
        //         'proc.product_car_name',
        //         'proc.product_car_filename'

        //     )

        //     ->leftJoin('ads_cart as ads', DB::raw('BINARY users.users_code'), '=', DB::raw('BINARY ads.users_code'))

        //     ->leftJoin('product as p', DB::raw('BINARY ads.product_code'), '=', DB::raw('BINARY p.product_code'))

        //     ->leftJoin('product_car as proc', DB::raw('BINARY ads.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))

        //     ->leftJoin('product_car_loc as procloc', DB::raw('BINARY ads.product_car_loc_code'), '=', DB::raw('BINARY procloc.product_car_loc_code'))

        //     ->leftJoin('ads_cart_order as aco', DB::raw('BINARY aco.ads_cart_order_code'), '=', DB::raw('BINARY ads.ads_cart_order_code'))

        //     ->where('ads.users_code', '=', $users_code)

        //     ->where('ads.ads_cart_status', '=', 0)

        //     ->where(function($adschart){

        //     $adschart->where('aco.ads_cart_order_payment_status', '=', 0)
        //     ->orWhere('aco.ads_cart_order_payment_status', '=', 2);
        //     })
        //     ->groupBy('ads.ads_cart_uuid');

        // 'ads_cart.ads_cart_uuid',
        //         'ads_cart.users_code',
        //         'ads_cart.ads_cart_order_code',
        //         'ads_cart.ads_cart_name',
        //         'ads_cart.ads_cart_amount',
        //         'ads_cart.product_code',
        //         'ads_cart.product_car_code',
        //         'ads_cart.ads_cart_status',
        //         'aco.ads_cart_order_grand_total',
        //         'p.product_name',
        //         'procloc.product_car_loc_name',
        //         'proc.product_car_name',
        //         'proc.product_car_filename'

        // ->select(DB::raw(
        //     'ads_cart.ads_cart_uuid,
        //     COUNT(ads_cart.ads_cart_uuid),
        //     ads_cart.users_code,
        //     ads_cart.ads_cart_order_code,
        //     ads_cart.ads_cart_name,
        //     ads_cart.ads_cart_amount,
        //     ads_cart.product_code,
        //     ads_cart.product_car_code,
        //     ads_cart.ads_cart_status,
        //     aco.ads_cart_order_grand_total,
        //     p.product_name,
        //     procloc.product_car_loc_name,
        //     proc.product_car_name,
        //     proc.product_car_filename'
        // ))

        $cartList = DB::table('ads_cart')
            ->distinct()
            ->select(
                'ads_cart.ads_cart_uuid',
                'ads_cart.users_code',
                'ads_cart.ads_cart_order_code',
                'ads_cart.ads_cart_name',
                'ads_cart.ads_cart_amount',
                'ads_cart.ads_cart_quantity',
                'ads_cart.product_code',
                'ads_cart.product_car_code',
                'ads_cart.ads_cart_status',
                'aco.ads_cart_order_grand_total',
                'p.product_name',
                'procloc.product_car_loc_name',
                'proc.product_car_name',
                'proc.product_car_filename'
            )
            ->leftJoin('product as p', DB::raw('BINARY ads_cart.product_code'), '=', DB::raw('BINARY p.product_code'))
            ->leftJoin('product_car as proc', DB::raw('BINARY ads_cart.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))
            ->leftJoin('product_car_loc as procloc', DB::raw('BINARY ads_cart.product_car_loc_code'), '=', DB::raw('BINARY procloc.product_car_loc_code'))
            ->leftJoin('ads_cart_order as aco', DB::raw('BINARY aco.ads_cart_order_code'), '=', DB::raw('BINARY ads_cart.ads_cart_order_code'))
            ->where('ads_cart.users_code', '=', $users_code)
            ->where('ads_cart.ads_cart_status', '=', 0) // STATUS ONCART
            ->where(function($adschart){
                $adschart->where('aco.ads_cart_order_payment_status', '=', 0)   // STATUS UNPAID
                    ->orWhere('aco.ads_cart_order_payment_status', '=', 2); // STATUS SEBAGIAN
            });

        if ($limit == null) {

            $resource = $cartList->get();
        } else{

            $resource = $cartList->limit($limit)
                ->offset($offset)
                ->get();
        }

        return daftarCart::collection($resource);
    }

    public function createAds($user_code, $thisData)
    {
        $cartCounts = (int) $thisData['ads_cart_count'];

        DB::beginTransaction();
        try {

            if ($thisData['order_code_available'] == 'NOT_AVAILABLE') {
                $ads_cart_order = DB::table('ads_cart_order')
                    ->insert([
                        'ads_cart_order_code' => $thisData['ads_cart_order_code'],
                        'ads_cart_order_invoice' => generateFiledCode('INV'),
                        'ads_cart_order_grand_total' => $thisData['ads_cart_amount'],
                        'ads_cart_order_payment_status' => 0, //STATUS UNPAID
                        'ads_cart_order_date_created' => Carbon::now()
                    ]);
            } else {
                $ads_cart_order = DB::table('ads_cart_order')
                    ->where('ads_cart_order_code', $thisData['ads_cart_order_code'])
                    ->update([
                        'ads_cart_order_date_updated' => Carbon::now(),
                        'ads_cart_order_grand_total' => $this->getTotalHargaForUpdate($thisData['ads_cart_order_code'], $cartCounts, $thisData['package_code'])
                    ]);
            }

            for ($i = 1; $i < $cartCounts+1; $i++) {
                $ads_cart[$i] = DB::table('ads_cart')
                    ->insert([
                        'users_code' => $user_code,
                        'ads_cart_name' => $thisData['ads_cart_name'],
                        'ads_cart_code' => generateFiledCode('CART'),
                        'ads_cart_uuid' => $thisData['ads_cart_uuid'],
                        'price_list_code' => $thisData['package_code'],
                        'ads_cart_quantity' => $thisData['ads_cart_count'],
                        'product_code' => $thisData['product_code'],
                        'product_type' => $thisData['product_type'],
                        'product_car_code' => $thisData['product_car_code'],
                        'product_car_loc_code' => $thisData['product_car_loc_code'],
                        'ads_cart_order_code' => $thisData['ads_cart_order_code'],
                        'ads_cart_url' => $thisData['ads_cart_url'],
                        'ads_cart_status' => 0, //STATUS ON-CART
                        'ads_cart_amount' => $thisData['ads_cart_amount']
                    ]);
            }

            $get_data_price = $this->getPricelistForTransactionV2($thisData['package_code']);

            $add_transaction = DB::table('transaction')
                ->insert([
                    'transaction_code' => generateFiledCode('TRX'),
                    'ads_cart_uuid' => $thisData['ads_cart_uuid'],
                    'transaction_quantity' => $thisData['ads_cart_count'],
                    'target' => @$get_data_price->target,
                    'harga_user' => @$get_data_price->harga_user,
                    'harga_partner' => @$get_data_price->harga_partner,
                    'extends_user' => @$get_data_price->extends_user,
                    'extends_partner' => @$get_data_price->extends_partner,
                    'create_at' => Carbon::now()
                ]);

            DB::commit();

        } catch (\Exception $ex) {
            DB::rollBack();
            logger('createAds(): catch ->', ['user' => $user_code, 'Exception' => $ex->getMessage()]);
            return false;
        }

        return true;
    }

    public function setPayment($users_code, $request, $payment_type = 'ipg')
    {
        $carts_code_selected_tmp = '{"0": "CART-4219550701309778003","1": "CART-4219550701302592086"}';
        $carts_uuid_code_selected_tmp = '{"0": "2d720106-a6bd-443b-b89c-8f48e6f0d30e","1": "2d720106-a6bd-443b-b89c-8f48e6f0d30e"}';
        $ads_cart_order_code = $request->ads_cart_order_code;
        $carts_uuid_code = json_decode($request->carts_uuid_selected, true);
        $grand_total = 0;
        $wallet_amount = (int) WalletUtility::getWalletAmount($users_code)->wallet_amount;

        DB::beginTransaction();
        try {
            // $ads_cart_order = DB::table('ads_cart_order')
            //     ->where('ads_cart_order_code', $ads_cart_order_code)
            //     ->update([
            //         'ads_cart_order_payment_status' => 1,
            //         'ads_cart_order_payment_type' => $request->payment_type == 'ipg' ? 1 : 0,
            //         'ads_cart_order_date_updated' => Carbon::now()
            //     ]);

            for ($i=0; $i < count($carts_uuid_code); $i++) {
                $ads_cart = DB::table('ads_cart')
                    ->where('ads_cart_uuid', $carts_uuid_code[$i])
                    ->where('ads_cart_order_code', $ads_cart_order_code)
                    ->update([
                        'ads_cart_status' => 5, //STATUS WAITING PAYMENT
                        'ads_cart_date_updated' => Carbon::now()
                    ]);

                $get_harga = $this->getTotalHargaForPayment($carts_uuid_code[$i]);
                $grand_total += @$get_harga->transaction_quantity * @$get_harga->harga_user;

                // list($ads_cart_quantity, $price_list) = $this->getPricelistForTransaction($carts_uuid_code[$i]);

                // $add_transaction = DB::table('transaction')
                //     ->insert([
                //         'transaction_code' => generateFiledCode('TRX'),
                //         'ads_cart_uuid' => $carts_uuid_code[$i][$i],
                //         'transaction_quantity' => @$ads_cart_quantity->ads_cart_quantity,
                //         'target' => @$price_list->target,
                //         'harga_user' => @$price_list->harga_user,
                //         'harga_partner' => @$price_list->harga_partner,
                //         'extends_user' => @$price_list->extends_user,
                //         'extends_partner' => @$price_list->extends_partner,
                //         'create_at' => Carbon::now()
                //     ]);
            }

            if ($payment_type == 'wallet') {
                if($grand_total > $wallet_amount) {
                    DB::rollBack();
                    return 'INSUFFICIENT_WALLET';
                }
            }

            $change_status_cart_not_selected = DB::table('ads_cart')
                ->where(function ($query) use ($ads_cart_order_code, $carts_uuid_code){
                    $query->whereNotIn('ads_cart_uuid', $carts_uuid_code)
                        ->where('ads_cart_order_code', $ads_cart_order_code);
                })
                ->update([
                    'ads_cart_status' => 3 //STATUS EXPIRED
                ]);

            $ads_cart_order = DB::table('ads_cart_order')
                ->where('ads_cart_order_code', $ads_cart_order_code)
                ->update([
                    'ads_cart_order_payment_type' => ($payment_type == 'ipg') ? 1 : 0, // IPG=1 / WALLET=0
                    'ads_cart_order_grand_total' => $grand_total
                ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            logger('setPayment(): catch ->', ['user' => $users_code, 'Exception' => $ex->getMessage()]);
            return 'FAILED';
        }
        return 'OK';

    }

    public function getPaymentInfo($users_code, $request)
    {
        return DB::table('users')
            ->selectRaw(
                'users.name,
                users.users_hp,
                users.email,
                aco.ads_cart_order_code AS order_code,
                aco.ads_cart_order_grand_total AS gross_amount'
            )
            ->leftJoin('ads_cart_order as aco', DB::raw('BINARY aco.ads_cart_order_code'), '=', DB::raw('BINARY aco.ads_cart_order_code'))
            ->where('users.users_code', '=', $users_code)
            ->where('aco.ads_cart_order_code', '=', $request->ads_cart_order_code)
            ->first();
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

    public function getMitraInfoAtLacak($cart_code)
    {
        $getDetail = DB::table('ads_cart')
            ->select(
                'pc.partner_car_nopol',
                'ads.ads_code',
                'ads.ads_start_date',
                'ads.current_distance',
                'ads.last_long',
                'ads.last_lat',
                'tr.target',
                'tr.label'
            )
            ->leftJoin('ads', DB::raw('BINARY ads.ads_cart_code'), '=', DB::raw('BINARY ads_cart.ads_cart_code'))
            ->leftJoin('partner_car AS pc', DB::raw('BINARY ads.partner_car_code'), '=', DB::raw('BINARY pc.partner_car_code'))
            ->leftJoin('transaction AS tr', DB::raw('BINARY ads.ads_cart_uuid'), '=', DB::raw('BINARY tr.ads_cart_uuid'))
            ->where('ads_cart.ads_cart_code', '=', $cart_code)
            ->first();

            return (collect($getDetail)->count()) ? new PartnerInfo($getDetail) : false;
    }

    public function getOrdercodeAvailable($user_code)
    {
        $check = DB::table('ads_cart')
            ->select('ads_cart_order_code')
            ->where('users_code', $user_code)
            ->where('ads_cart_status', 0)
            ->first();

        return (!$check) ? 'NOT_AVAILABLE' : $check->ads_cart_order_code;
    }

    public function getTypeProduct($product_code)
    {
        $carimg =  DB::table('product')
            ->select('product_name')
            ->where('product_code', $product_code)
            ->get();

        return $carimg;
    }

    public function getImageCar($img)
    {
        $carimg =  DB::table('product_car')
            ->select('product_car_filename')
            ->where('product_car_code', $img)
            ->first();
        
        return $carimg;
    }

    public function getPricelistForTransaction($ads_cart_uuid)
    {
        $get_price_code = DB::table('ads_cart')
            ->select('price_list_code','ads_cart_quantity')
            ->where('ads_cart_uuid', $ads_cart_uuid)
            ->first();

        $get_pricelist = DB::table('price_list')
            ->select('*')
            ->where('price_list_code', $get_price_code->price_list_code)
            ->first();

        return array($get_price_code, $get_pricelist);
    }

    public function getPricelistForTransactionV2($price_list_code)
    {
        return DB::table('price_list')
            ->select('*')
            ->where('price_list_code', $price_list_code)
            ->first();
    }

    public function getTotalHargaForPayment($ads_cart_uuid)
    {
        return DB::table('transaction')
            ->select('*')
            ->where('ads_cart_uuid', $ads_cart_uuid)
            ->first();
    }

    public function getTotalHargaForUpdate($ads_cart_order_code, $cart_count, $package_code)
    {
        $getHarga = DB::table('ads_cart_order')
            ->select('ads_cart_order_grand_total')
            ->where('ads_cart_order_code', $ads_cart_order_code)
            ->first();

        if (!$getHarga) return 0;

        return ((int) $getHarga->ads_cart_order_grand_total) + calculateTotalPriceAds($cart_count, $package_code);
    }

    public function getWalletAmount($users_code)
    {
        return DB::table('users')
            ->selectRaw('users_wallet AS wallet_amount')
            ->where('users_code', $users_code)
            ->first();
    }
}
