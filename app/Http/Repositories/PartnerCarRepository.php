<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;
use App\Http\Resources\CarPartnerResource;
use Carbon\Carbon;
use App\Http\Resources\carPartnerDetail;

class PartnerCarRepository
{
    public function getCarPartner($partner_code, $limit = '5', $offset = '0')
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
        ->where('partner_car.partner_code', '=', $partner_code);

        if ($limit == null) {
            $resource = $thisCar->get();


        } else{
            $resource = $thisCar->limit($limit)
            ->offset($offset)
            ->get();
        }
       // dd($partner);
        $getThisCar = CarPartnerResource::collection($resource);

        return $getThisCar;

    }

    public function addNewCar($partner_code, $request)
    {
        $new_car = DB::table('partner_car')
        ->insert(
            [
            'partner_car_name' => $request->partner_car_name,
            'partner_code' => $partner_code,
            'partner_car_nopol' => $request->partner_car_nopol,
            'product_code' => $request->product_code,
            'product_type' => $request->product_type,
            'product_car_code' => $request->product_car_code,
            'partner_car_code' => generateFiledCode('PARTCODE'),
            'partner_car_name' => $request->partner_car_name,
            'partner_car_merk' => $request->partner_car_merk,
            'partner_car_tahun' => $request->partner_car_tahun,
            'partner_car_nostnk' => $request->partner_car_nostnk,
            'partner_car_filename' => uploadFotoWithFileName($request->input('partner_car_stnk'), 'STNK'),
            'partner_car_date_created' => Carbon::now(),
            'partner_car_status' => 1
            ]
        );

        return $new_car;
    }

    public function editPartnerCar($partner_code, $request)
    {
        $dataUpdatePartnerCar = [
            'partner_car_name' => $request->partner_car_name,
            'partner_car_nopol' => $request->partner_car_nopol,
            'product_code' => $request->product_code,
            'product_type' => $request->product_type,
            'product_car_code' => $request->product_car_code,
            'partner_car_merk' => $request->partner_car_merk,
            'partner_car_tahun' => $request->partner_car_tahun,
            'partner_car_nostnk' => $request->partner_car_nostnk,
            'partner_car_filename' => uploadFotoWithFileName($request->input('partner_car_stnk'), 'STNK'),
            'partner_car_date_updated' => Carbon::now()
        ];

       // dd($request->partner_car_name);
        $data_car = DB::table('partner_car')
        ->where('partner_car_code', '=', $request->partner_car_code)
        ->update($dataUpdatePartnerCar);

        return $data_car;
    }

    public function getDetailCar($request)
    {

        // dd($request);
        $car_details = DB::table('partner_car')
        ->select(
            'partner_car.*',
            'p.product_code',
            'p.product_name',
            'proc.product_car_code',
            'proc.product_car_name'

            )
        ->leftJoin('product as p', DB::raw('BINARY partner_car.product_code'), '=', DB::raw('BINARY p.product_code'))
        ->leftJoin('product_car as proc', DB::raw('BINARY partner_car.product_car_code'), '=', DB::raw('BINARY proc.product_car_code'))
        ->where('partner_car.partner_car_code', '=', $request->partner_car_code)
        ->first();

        $car_detail = (collect($car_details)->count()) ? new carPartnerDetail($car_details) : false;

        return $car_detail;
    }
}
