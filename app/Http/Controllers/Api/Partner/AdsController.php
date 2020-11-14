<?php

namespace App\Http\Controllers\Api\Partner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Http\Repositories\PartnerAdsRepository;
use Illuminate\Support\Facades\Auth;

class AdsController extends ApiController
{
    //

    protected $partnerAdsRepo;

    public function __construct(PartnerAdsRepository $partnerAdsRepo)
    {
        $this->partnerAdsRepo = $partnerAdsRepo;
    }

    public function allAdsListOnBid(Request $request)
    {
        $partner_code = Auth::user()->partner_code;
        $limit = $request->limit;
        $offset = $request->offset;

        $ads_list = $this->partnerAdsRepo->getAdsList($limit, $offset);

        if ($ads_list->count() > 0) {
            $result = $this->sendResponse(0, 'Sukses', $ads_list);
        } elseif ($ads_list->count() == 0) {
            $result = $this->sendResponse(0, 'Data kosong');
        } else {
            $result = $this->sendError(2, 'Error');
        }

        return $result;
    }

    public function allAdsListBid(Request $request)
    {
        $partner_code = Auth::user()->partner_code;
        $limit = $request->limit;
        $offset = $request->offset;

        $ads_list = $this->partnerAdsRepo->getAdsListBid($partner_code, $limit, $offset);

        if ($ads_list->count() > 0) {
            $result = $this->sendResponse(0, 'Sukses', $ads_list);
        } elseif ($ads_list->count() == 0) {
            $result = $this->sendResponse(0, 'Data kosong');
        } else {
            $result = $this->sendError(2, 'Error');
        }

        return $result;
    }

    public function addAdsStep1(Request $request)
    {
        $partner_code = Auth::user()->partner_code;

        $limit = $request->limit;
        $offset = $request->offset;

        $car_list = $this->partnerAdsRepo->getCarPartnerForstep1($partner_code, $request, $limit, $offset);

        if ($car_list->count() > 0) {
            $result = $this->sendResponse(0, 'Sukses', $car_list);
        } elseif ($car_list->count() == 0) {
            $result = $this->sendResponse(0, 'Tidak ada kendaraan yang sesuai');
        } else {
            $result = $this->sendError(2, 'Error');
        }

        return $result;
    }

    public function addAdsStep2(Request $request)
    {
        $partner_code = Auth::user()->partner_code;

        $car_list = $this->partnerAdsRepo->detailAdsForstep2($request);

        if ($car_list) {
            $result = $this->sendResponse(0, 'Sukses', $car_list);
        } else {
            $result = $this->sendError(2, 'Error');
        }

        return $result;
    }

    public function getAds(Request $request)
    {
        $partner_code = Auth::user()->partner_code;
        $pks_accept = (boolean) $request->pks_accept;

        if (!$pks_accept) {
            return $this->sendError(2, 'PKS belum disetujui');
        }

        $result = $this->partnerAdsRepo->getAds($request, $partner_code);

        if ($result == 'ADS_NOT_AVAILABLE') {
            return $this->sendError(2, 'Iklan yang anda pilih sudah tidak tersedia');
        } elseif($result == false) {
            return $this->sendError(2, 'Gagal mengambil iklan');
        } else {
            return $this->sendResponse(0, 'Sukses', $result);
        }
    }
}
