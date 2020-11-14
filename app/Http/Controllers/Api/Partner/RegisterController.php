<?php

namespace App\Http\Controllers\Api\Partner;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends ApiController
{
    public function __construct()
    {

    }

    public function step1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'partner_ktp' => 'required|unique:partners',
        ]);

        if ($validator->fails()) {
            return $this->sendError(2, "KTP Sudah terpakai", (object) array());
        }

        $name = $request->name;
        $partner_ktp = $request->partner_ktp;
        $partner_hp = $request->partner_hp;
        $partner_npwp = $request->partner_npwp;


        return $this->sendResponse(0, "Success");
    }

    public function step2(Request $request)
    {

        $partner_company = $request->partner_company;
        $provinsi_code = $request->provinsi_code;
        $kota_code = $request->kota_code;
        $kec_code = $request->kec_code;
        $kel_code = $request->kel_code;
        $partner_address = $request->partner_address;

        return $this->sendResponse(0, "Success");
    }

    public function step3(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:partners',
        ]);

        if ($validator->fails()) {
            return $this->sendError(2, "Email Sudah terpakai", (object) array());
        }

        $email = $request->email;
        $password = $request->password;
        $partner_verification_type = $request->partner_verification_type;

        if ($request->partner_verification_type == "sms") {
            $phone = $request->partner_hp;
            $pattern = '/^0/';
            $replacement = '+62';
            $phone = preg_replace($pattern, $replacement, $phone);
            $result = SMSverifyRequest($phone);

            if ($result->getData()->data->status == 10) {
                return $this->sendError(2, "Verifikasi serentak ke nomor yang sama tidak diizinkan", (object) array());
            }

            if ($result->getData()->data->status == 3) {
                return $this->sendError(2, "Nomor handphone tidak valid", (object) array());
            }

            $data = ['request_id' => @$result->getData()->data->request_id];

            return $this->sendResponse(0, "Kode sudah terkirim via sms", $data);

        }else {

            $random_code = rand(1000,9999);
            $token = bin2hex(openssl_random_pseudo_bytes(16));

            $data = ['code' => $random_code, 'token' => $token, 'expires' => now()->addMinutes(5), 'email' => $request->email];

            $result = DB::table('verification_email')->insert($data);

            if (!$result) {
                return $this->sendError(2, "Gagal Verifikasi", (object) array());
            }

            Mail::to($request->email)->send(new VerificationEmail($data));
            $data = ['request_id' => $token];

            return $this->sendResponse(0, "Kode sudah terkirim via email", $data);
        }

    }

    public function finish(Request $request)
    {
        $request_id = $request->request_id;
        $code = $request->code;
        $partner_verification_type = $request->partner_verification_type;
        $email = $request->email;

        if ($partner_verification_type == "sms") {
            $result = SMSverifyCheck($request_id, $code);

            if ($result->getData()->data->status == 16) {
                return $this->sendError(2, "Kode Tidak cocok", (object) array());
            }
            if ($result->getData()->data->status == 3) {
                return $this->sendError(2, "Invalid value request id", (object) array());
            }

            if ($result->getData()->data->status == 6) {
                return $this->sendError(2, "Token tidak ditemukan", (object) array());
            }

        }else {
            $checkVerificationEmail = checkVerificationEmail($request_id, $code, $email);

            if ($checkVerificationEmail === "TOKEN_EXPIRED") {
                return $this->sendError(2, 'Token sudah kadaluarsa', (object) array());
            }

            if ($checkVerificationEmail === "CODE_NOT_MATCH") {
                return $this->sendError(0, 'Kode yg dimasukkan tidak sesuai', (object) array());
            }
        }


        $data = [
            'partner_code' => generateFiledCode('PARTNER'),
            'name' => $request->name,
            'partner_ktp' => $request->partner_ktp,
            'partner_hp' => $request->partner_hp,
            'partner_npwp' => $request->partner_npwp,
            'partner_company' => $request->partner_company,
            'provinsi_code' => $request->provinsi_code,
            'kota_code' => $request->kota_code,
            'kec_code' => $request->kec_code,
            'kel_code' => $request->kel_code,
            'partner_address' => $request->partner_address,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'partner_verification_type' => ($request->partner_verification_type == 'email' ? 1 : 0),
        ];

        try {

            $insertRegis = DB::table('partners')->insert($data);
        } catch (\Exception $e) {

            return $this->sendError(2, "Gagal Registrasi", (object) array());
        }

        return $this->sendResponse(0, "Berhasil Registrasi");

    }


}
