<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Repositories\MasterRepository;
use App\Http\Controllers\ApiController;

class MasterController extends ApiController
{
    protected $masterRepo;

    function __construct(MasterRepository $masterRepo)
    {
        $this->masterRepo = $masterRepo;
    }

    public function provinsi()
    {
        $data = $this->masterRepo->provinsi();

        if ($this->masterRepo->provinsi(TRUE)) {
            return $this->sendResponse(0, 'Sukses', $data);
        }else {
            return $this->sendError(2, 'Error');
        }
    }

    public function kota(Request $request)
    {
        $param = $request->input('province_code');
        $data = $this->masterRepo->kota(FALSE, $param);

        if ($this->masterRepo->kota(TRUE, $param)) {
            return $this->sendResponse(0, 'Sukses', $data);
        }else {
            return $this->sendError(2, 'Error');
        }
    }

    public function district(Request $request)
    {
        $param = $request->input('city_code');
        $data = $this->masterRepo->district(FALSE, $param);

        if ($this->masterRepo->district(TRUE, $param)) {
            return $this->sendResponse(0, 'Sukses', $data);
        }else {
            return $this->sendError(2, 'Error');
        }
    }

    public function kelurahan(Request $request)
    {
        $param = $request->input('district_code');
        $data = $this->masterRepo->kelurahan(FALSE, $param);

        if ($this->masterRepo->kelurahan(TRUE, $param)) {
            return $this->sendResponse(0, 'Sukses', $data);
        }else {
            return $this->sendError(2, 'Error');
        }
    }

    public function getMasterProduct(Request $request)
    {
        $data = $this->masterRepo->getProductList();

        if ($data->count() > 0) {
            $result = $this->sendResponse(0, 'Sukses', $data);
        } elseif ($data->count() == 0) {
            $result = $this->sendResponse(0, 'Data kosong');
        } else {
            $result = $this->sendError(2, 'Error');
        }

        return $result;
    }

    public function getMasterProductCar(Request $request)
    {
        $data = $this->masterRepo->getProductCarList();

        if ($data->count() > 0) {
            $result = $this->sendResponse(0, 'Sukses', $data);
        } elseif ($data->count() == 0) {
            $result = $this->sendResponse(0, 'Data kosong');
        } else {
            $result = $this->sendError(2, 'Error');
        }

        return $result;
    }

    public function getMasterProductCarLoc(Request $request)
    {
        $data = $this->masterRepo->getProductCarLocList($request->product_car_code);

        if ($data->count() > 0) {
            $result = $this->sendResponse(0, 'Sukses', $data);
        } elseif ($data->count() == 0) {
            $result = $this->sendResponse(0, 'Data kosong');
        } else {
            $result = $this->sendError(2, 'Error');
        }

        return $result;
    }
}
