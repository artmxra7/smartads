<?php

namespace App\Http\Controllers\Api\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Http\Repositories\UserAdsRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdsController extends ApiController
{
    //

    protected $UserAdsRepo;

    public function __construct(UserAdsRepository $UserAdsRepo)
    {
        $this->UserAdsRepo = $UserAdsRepo;
    }


    public function detailAdsList(Request $request)
    {
        $ads_code = $request->input('ads_code');

        $result = $this->UserAdsRepo->getAdsDetail($ads_code);

        if ($result) {
            return $this->sendResponse(0, 'Sukses', $result);
        } else {
            return $this->sendError(2, 'Data tidak ditemukan');
        }
    }

    public function allAdsList(Request $request)
    {

        $users_code = Auth::user()->users_code;
        $limit = $request->limit;
        $offset = $request->offset;

        $ads_list = $this->UserAdsRepo->getAdsList($users_code, $limit, $offset);

        if ($ads_list->count() > 0) {
            $result = $this->sendResponse(0, 'Sukses', $ads_list);
        } elseif ($ads_list->count() == 0) {
            $result = $this->sendResponse(0, 'Data kosong');
        } else {
            $result = $this->sendError(2, 'Error');
        }

        return $result;
    }

    public function allOnBidList(Request $request)
    {

        $users_code = Auth::user()->users_code;
        $limit = $request->limit;
        $offset = $request->offset;

        $onbid_list = $this->UserAdsRepo->getOnBidList($users_code, $limit, $offset);

        if ($onbid_list->count() > 0) {
            $result = $this->sendResponse(0, 'Sukses', $onbid_list);
        } elseif ($onbid_list->count() == 0) {
            $result = $this->sendResponse(0, 'Data kosong');
        } else {
            $result = $this->sendError(2, 'Error');
        }

        return $result;
    }

    public function detailOnBidList(Request $request)
    {
        $ads_cart_code= $request->input('ads_cart_code');
        $result = $this->UserAdsRepo->getOnBidDetail($ads_cart_code);

        if ($result) {
            return $this->sendResponse(0, 'Sukses', $result);
        } else {
            return $this->sendError(2, 'Data tidak ditemukan');
        }
    }


    public function allCartList(Request $request)
    {

        $users_code = Auth::user()->users_code;
        $limit = $request->limit;
        $offset = $request->offset;

        $onbid_list = $this->UserAdsRepo->getCartList($users_code, $limit, $offset);

        if ($onbid_list->count() > 0) {
            $result = $this->sendResponse(0, 'Sukses', $onbid_list);
        } elseif ($onbid_list->count() == 0) {
            $result = $this->sendResponse(0, 'Data kosong');
        } else {
            $result = $this->sendError(2, 'Error');
        }

        return $result;
    }


    public function createCartStep1(Request $request)
    {

        $validator = Validator::make($request->all(),
        [
            'ads_cart_name' => 'required',
            'product_code' => 'required',
            'product_type' => 'required',
            'product_car_code' => 'required',
            'product_car_loc_code' => 'required'
        ]);

        if ($validator->fails()) {
            $validate = validationMessage($validator->errors());
            return $this->sendError(1, 1, $validate);
        }

        $ads_cart_name = $request->ads_cart_name;
        $product_code = $request->product_code;
        $product_type = $request->product_type;
        $product_car_code = $request->product_car_code;
        $product_car_loc_code = $request->product_car_loc_code;

        return $this->sendResponse(0, "Success");
    }

    public function createCartStep2(Request $request)
    {
        $code = $request->session()->get('ads_cart_order_code', function () {
            return $this->sendError(2, "Terjadi kesalahan sistem.");
        });

        $validator = Validator::make($request->all(),
        [
            'ads_cart_count' => 'required',
            'ads_cart_start_date' => 'required',
            'ads_cart_end_date' => 'required',
            'ads_cart_url' => 'required'
        ]);

        if ($validator->fails()) {
            $validate = validationMessage($validator->errors());
            return $this->sendError(1, 1, $validate);
        }

        $ads_cart_count = (int) $request->ads_cart_count;
        $ads_cart_start_date = $request->ads_cart_start_date;
        $ads_cart_end_date = $request->ads_cart_end_date;
        $ads_cart_url = $request->ads_cart_url;
        $product_car_code = $request->product_car_code;
        $product_code = $request->product_code;

        $product_car_filename = $this->getImageCar($product_car_code);
        $product = $this->getTypeProduct($product_code);

        $thisData = [
            'ads_cart_name' => @$request->ads_cart_name,
            'product_code'  => @$request->product_code,
            'product_type'  => @$request->product_type,
            'product_car_code'  => @$request->product_car_code,
            'product_car_loc_code'  => @$request->product_car_loc_code,
            'ads_cart_count'  => @$request->ads_cart_count,
            'ads_cart_amount' => calculateTotalPriceAds($ads_cart_count),
            'ads_cart_start_date'  => @$request->ads_cart_start_date,
            'ads_cart_end_date'  => @$request->ads_cart_end_date,
            'ads_cart_url'  => @$request->ads_cart_url
        ];

        $thisData['product_car_filename'] = asset('storage/'.$product_car_filename->product_car_filename);

        return $this->sendResponse(0, "Success", $thisData);
    }

    public function finish(Request $request, $ads_cart_order_code=NULL)
    {
        $users_code = Auth::user()->users_code;
        $ads_cart_order_code = $request->ads_cart_order_code;

        if ($ads_cart_order_code == NULL){

            $ads_cart_order_code = generateFiledCode('ORDERCODE');
        }

        $thisData = [
            'ads_cart_name' => @$request->ads_cart_name,
            'ads_cart_order_code' => $ads_cart_order_code,
            'product_car_filename' => @$request->product_car_filename,
            'ads_cart_amount' => "9000000",
            'product_code'  => @$request->product_code,
            'product_type'  => @$request->product_type,
            'product_car_code'  => @$request->product_car_code,
            'product_car_loc_code'  => @$request->product_car_loc_code,
            'ads_cart_count'  => @$request->ads_cart_count,
            'ads_cart_start_date'  => @$request->ads_cart_start_date,
            'ads_cart_end_date'  => @$request->ads_cart_end_date,
            'ads_cart_url'  => @$request->ads_cart_url
        ];

        $save_ads = $this->UserAdsRepo->createAds($users_code, $thisData);

        if ($save_ads) {
            return $this->sendResponse(0, "Berhasil", $thisData);
        } else {
            return $this->sendError(2, 'Error');
        }
    }

    public function historyAdsList(Request $request)
    {
        $users_code = Auth::user()->users_code;
        $limit = $request->limit;
        $offset = $request->offset;

        $hystory_list = $this->UserAdsRepo->getAdsHistoryList($users_code, $limit, $offset);

        if ($hystory_list->count() > 0) {
            $result = $this->sendResponse(0, 'Sukses', $hystory_list);
        } elseif ($hystory_list->count() == 0) {
            $result = $this->sendResponse(0, 'Data kosong');
        } else {
            $result = $this->sendError(2, 'Error');
        }

        return $result;
    }

    public function historyAdsDetail(Request $request)
    {
        $users_code = Auth::user()->users_code;
        $ads_code = $request->input('ads_code');

        $result = $this->UserAdsRepo->getAdsHistoryDetail($ads_code);

        if ($result) {
            return $this->sendResponse(0, 'Sukses', $result);
        } else {
            return $this->sendError(2, 'Data tidak ditemukan');
        }
    }


    public function countCart(Request $request)
    {
        $users_code = Auth::user()->users_code;
        $ads_cart_order_code = $request->acd;

        $hystory_count = $this->UserAdsRepo->getAdsCount($users_code, $ads_cart_order_code);

        if ($hystory_count) {
            return $this->sendResponse(0, "Berhasil", $hystory_count);
        } else {
            return $this->sendError(2, 'Error');
        }
    }

    public function paymentAdsv1(Request $request)
    {
        $users_code = Auth::user()->users_code;

        $setPayment = $this->UserAdsRepo->setPayment($users_code, $request);

        if ($setPayment) {
            return $this->sendResponse(0, "Berhasil", []);
        } else {
            return $this->sendError(2, 'Error');
        }

    }

    public function getImageCar($img)
    {
       $carimg =  DB::table('product_car')
        ->select('product_car_filename')
        ->where('product_car_code', $img)
        ->first();
        return $carimg;
    }

    public function getTypeProduct($product_code)
    {
        $carimg =  DB::table('product')
        ->select('product_name')
        ->where('product_code', $product_code)
        ->get();
        return $carimg;
    }


}
