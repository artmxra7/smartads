<?php

namespace App\Http\Repositories;

use App\Partner;
use App\Http\Resources\Partner as PartnerResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PartnerRepository
{
    protected $partner;

    public function __construct(Partner $partner)
    {
        $this->partner = $partner;

    }

    public function getDetails($partner_code)
    {
        if (empty($partner_code)) {

            return false;
        }

        $partners = DB::table('partners')->select('partners.*', 'mc.city_name', 'mp.province_name', 'md.district_name', 'mk.kelurahan_name')
        ->leftJoin('master_city as mc', DB::raw('BINARY partners.city_code'), '=', DB::raw('BINARY mc.city_code'))
        ->leftJoin('master_province as mp', DB::raw('BINARY partners.province_code'), '=', DB::raw('BINARY mp.province_code'))
        ->leftJoin('master_district as md', DB::raw('BINARY partners.district_code'), '=', DB::raw('BINARY md.district_code'))
        ->leftJoin('master_kelurahan as mk', DB::raw('BINARY partners.kelurahan_code'), '=', DB::raw('BINARY mk.kelurahan_code'))
        ->where('partners.id', '!=', 2)
        ->where('partner_status', 1)
        ->where('partners.partner_code', $partner_code)
        ->limit(1)
        ->first();


        $partner = (collect($partners)->count()) ? new PartnerResource($partners) : false;
        return $partner;
    }

    public function updateProfile($request, $partner_code)
    {
        if (empty($partner_code)){
            return false;
        }

        $email = DB::table('partners')->select('email')->where('partner_status', 1)->where('email', $request->partner_email)->get();
        $pc = DB::table('partners')->select('email')->where('partner_status', 1)->where('partner_code', $partner_code)->first();

        $data = [
            'name' => $request->partner_name,
            'partner_hp' => $request->partner_hp,
            'partner_company' => $request->partner_company,
            'partner_address' => $request->partner_address,
            'provinsi_code' => $request->provinsi_code,
            'kota_code' => $request->kota_code,
            'kec_code' => $request->kec_code,
            'kel_code' => $request->kel_code,
            'email' => $request->partner_email,
            'partner_date_updated' => Carbon::now()
        ];

        if ($request->partner_email) {

            if (count($email) > 0) {
                if ($request->partner_email != $pc->email) {
                    return 'ALREADY_EXISTS';
                }
            }else{
                $data['email'] = $request->partner_email;
            }

        }

        $update = Partner::where('partner_status', 1)
            ->where('partner_code', $partner_code)
            ->update($data);

        return $update;
    }


}
