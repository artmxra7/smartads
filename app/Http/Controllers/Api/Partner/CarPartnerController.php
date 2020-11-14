<?php

namespace App\Http\Controllers\Api\Partner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
use App\Http\Repositories\PartnerCarRepository;

class CarPartnerController extends ApiController
{
    //
    protected $partnerCarRepo;
    public function __construct(PartnerCarRepository $partnerCarRepo)
    {
        $this->partnerCarRepo = $partnerCarRepo;
    }

    public function allCarPartner(Request $request)
    {
        $partner_code = Auth::user()->partner_code;

        $limit = $request->limit;
        $offset = $request->offset;

        $car_list = $this->partnerCarRepo->getCarPartner($partner_code, $limit, $offset);

        if ($car_list->count() > 0) {
            $result = $this->sendResponse(0, 'Sukses', $car_list);
        } elseif ($car_list->count() == 0) {
            $result = $this->sendResponse(0, 'Data kosong');
        } else {
            $result = $this->sendError(2, 'Error');
        }

        return $result;
    }

    public function createCarPartner(Request $request)
    {
        $partner_code = Auth::user()->partner_code;

        $save_car = $this->partnerCarRepo->addNewCar($partner_code, $request);

        if ($save_car) {
            return $this->sendResponse(0, "Berhasil");
        } else {
            return $this->sendError(2, 'Error');
        }
    }

    public function editCarPartner(Request $request)
    {
        $partner_code = Auth::user()->partner_code;

        $save_car = $this->partnerCarRepo->editPartnerCar($partner_code, $request);

        if ($save_car) {
            return $this->sendResponse(0, "Berhasil");
        } else {
            return $this->sendError(2, 'Error');
        }
    }

    public function partnerCarDetail(Request $request)
    {
        $partner_code = Auth::user()->partner_code;
        $result = $this->partnerCarRepo->getDetailCar($request);

        if ($result) {
            return $this->sendResponse(0, 'Sukses', $result);
        } else {
            return $this->sendError(2, 'Data tidak ditemukan');
        }
    }
}
