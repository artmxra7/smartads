<?php

namespace App\Http\Repositories;

use App\User;
use App\Partner;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthRepository
{
    public function insertRegisterPartner($request)
    {
        // dd($request);
        $partner = new Partner([
            'partner_code' => generateFiledCode('p'),
            'name' => $request->name,
            'partner_ktp' => $request->ktp,
            'partner_hp' => $request->hp,
            'partner_npwp' => $request->npwp,
            'partner_company' => $request->company,
            'provinsi_code' => $request->provinsi_code,
            'kota_code' => $request->kota_code,
            'kec_code' => $request->kec_code,
            'kel_code' => $request->kel_code,
            'partner_address' => $request->address,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'partner_verification_type' => $request->verification_type,
        ]);

        return $partner->save();
    }



    public function getValidationRegisterAsUserStepOne($request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'users_hp' => 'required',
            'users_company' => 'required'
            ]);

        return $validator;
    }


    public function getValidationRegisterAsUser($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'users_hp' => 'required',
            'users_company' => 'required',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:8',
            'password_confirmation' => 'required_with:password|same:password|min:8',
            'verification_type' => 'required'
            ]);


        return $validator;
    }

    public function getValidationRegisterAsUserFinish($request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'hp' => 'required',
            'company' => 'required',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:8',
            'password_confirmation' => 'required_with:password|same:password|min:8',
            'verification_type' => 'required'
            ]);

        return $validator;
    }

    public function insertRegisterAsUser($request)
    {
        $user = new User([
            'users_code' => generateFiledCode('USERS'),
            'name' => $request->name,
            'users_hp' => $request->users_hp,
            'users_company' => $request->users_company,
            'users_referral_code' => $request->refferalcode,
            'users_npwp' => $request->npwp,
            'users_email' => $request->email,
            'users_password' => bcrypt($request->password),
            'users_verification_type' => $request->verification_type
        ]);



        return $user->save();
    }

    public function SaveOrUpdateDeviceData($device, $user, $user_type)
    {
        $userDeviceNum = DB::table('user_device')->where('imei', @$device['imei'])->exists();

        $device['user_code'] = ($user_type == 'web' ? Auth::guard($user_type)->user()->users_code : Auth::guard($user_type)->user()->partner_code);
        if ($userDeviceNum) {
            $update = DB::table('user_device')->where('imei', @$device['imei'])->update(array_merge(@$device,array(
                'date_updated' => date('Y-m-d H:i:s'),
            )));

            return $update;
        }else{
        	$device['user_code'] = ($user_type == 'web' ? $user->users_code : $user->partner_code);
            $device['user_device_code'] = generateFiledCode('UDC');
            $insert = DB::table('user_device')->insert(@$device);

            return $insert;
        }
    }

    public function getExpiredToken($tokenId)
    {
        if (empty($tokenId)) {
            return false;
        }

        $users = DB::table('oauth_access_tokens')
            ->where('id', $tokenId)
            ->first();

        $expires = $users->expires_at;

        if ($expires >= date('Y-m-d H:i:s')) {

            return true;
        }

        return false;
    }
}
