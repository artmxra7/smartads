<?php

namespace App\Http\Controllers\Api\Partner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Http\Repositories\PartnerRepository;
use Illuminate\Support\Facades\Auth;
use App\Partner;

class PartnerController extends ApiController
{
    //
    protected $partnerRepository;

    public function __construct(PartnerRepository $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
    }

    public function viewPartnerLastLogin (Request $request)
    {
        $result = true;
        if ($result){
            $result = $this->sendResponse(0, 31);
        } else {
            $result = $this->sendError(2, 32);
        }
    }

    public function viewPartnerStatus()
    {
        $result = \App\Partner::select('partner_status')
        ->where('partner_code', Auth::user()->partner_code);
        $data = $result->first();

        if ($result->exists()) {
            $result = $this->sendResponse(0, "Sukses", $data);
        } else {
            $result = $this->sendError(2, 4);
        }

        return $result;

    }

    public function viewPartnerExist()
    {
        $result = \App\Partner::select('partner_status')
        ->where('partner_status', 1)
        ->where('partner_code', Auth::user()->partner_code);

        if ($result->exists()) {
            $result = $this->sendResponse(0, "Sukses");
        } else {
            $result = $this->sendError(2, 4);
        }

        return $result;
    }

    public function detail()
    {
        $result = $this->partnerRepository->getDetails(Auth::user()->partner_code);

        if (!empty($result)) {
            $result = $this->sendResponse(0, 'Sukses', $result);

        } elseif ($result === false) {
            $result = $this->sendError(2, 4);
        } else {
            $result = $this->sendError(2, 4);
        }

        return $result;
    }

    public function update(Request $request)
    {
        $update = $this->partnerRepository->updateProfile($request, Auth::user()->partner_code);


        if ($update == "ALREADY_EXISTS") {

            return $this->sendError(2, 'Email sudah terdaftar');
        }
        else if ($update) {

            return $this->sendResponse(0, 'Sukses');
        } else {
            return $this->sendError(2, 'Error');
        }
    }
}
