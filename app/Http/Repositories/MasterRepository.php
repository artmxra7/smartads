<?php

namespace App\Http\Repositories;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Users;
use App\Http\Repositories\UserRepository;

class MasterRepository
{
    function __construct()
    {

    }

    public function provinsi($exists = FALSE)
    {
        $param = array(
            'table' => 'master_province',
            'select' => array('province_code as id', 'province_name as name', 'province_status as status'),
        );

        return $this->getTable($param, $exists);
    }

    public function kota($exists = FALSE, $province_code = NULL)
    {
        $param = array(
            'table' => 'master_city',
            'select' => array('city_code','province_code','city_name'),
            'condition' => array(
                'key' => 'province_code',
                'value' => $province_code,
            ),
        );

        return $this->getTable($param, $exists);
    }

    public function district($exists = FALSE, $city_code = NULL)
    {
        $param = array(
            'table' => 'master_district',
            'select' => array('district_code','city_code','district_name'),
            'condition' => array(
                'key' => 'city_code',
                'value' => $city_code,
            ),
        );

        return $this->getTable($param, $exists);
    }

    public function kelurahan($exists = FALSE, $district_code = NULL)
    {
        $param = array(
            'table' => 'master_kelurahan',
            'select' => array('kelurahan_code','district_code','kelurahan_name'),
            'condition' => array(
                'key' => 'district_code',
                'value' => $district_code,
            ),
        );

        return $this->getTable($param, $exists);
    }

    public function getTable($param = array(), $exists = FALSE)
    {
        $result = DB::table($param['table'])
            ->select($param['select']);

        if (isset($param['condition']['key']) && isset($param['condition']['value'])) {
            $result->where($param['condition']['key'], $param['condition']['value']);
        }

        if ($exists === TRUE) {
            return $result->exists();
        }else {
            return $result->get();
        }
    }

    public function getProductList()
    {
        return DB::table('product')->select(
                'product_code',
                'product_name',
                'product_type'
            )
            ->where('product_status', 1)
            ->get();
    }

    public function getProductCarList()
    {
        return DB::table('product_car')->select(
                'product_car_code',
                'product_car_name'
            )
            ->where('product_car_status', 1)
            ->get();
    }

    public function getProductCarLocList($product_car_code)
    {
        return DB::table('product_car_loc')->select(
                'product_car_loc_code',
                'product_car_loc_name'
            )
            ->where('product_car_loc_status', 1)
            ->where('product_car_code', $product_car_code)
            ->get();
    }
}
