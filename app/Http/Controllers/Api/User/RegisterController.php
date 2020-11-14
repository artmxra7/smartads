<?php

namespace App\Http\Controllers\Api\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Repositories\AuthRepository;
use App\Http\Repositories\UserService;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Hash;

class RegisterController extends ApiController
{
    //

    protected $authRepository;
    protected $userService;

    public function __construct(AuthRepository $authRepository, UserService $userService)
    {
        $this->authRepository = $authRepository;
        $this->userService = $userService;
    }


    public function registerAsUserStepOne(Request $request)
    {
        $validator = $this->authRepository->getValidationRegisterAsUserStepOne($request);


        if ($validator->fails()) {
            $validate = validationMessage($validator->errors());
            return $this->sendError(1, 1, $validate);
        }

        $name = $request->name;
        $users_hp = $request->users_hp;
        $users_company = $request->users_company;
        $users_referral_code = $request->users_referral_code;
        $users_npwp = $request->users_npwp;

       return $this->sendResponse(0, "Success");
    }


    public function registerAsUserStepTwo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:8',
            'password_confirmation' => 'required_with:password|same:password|min:8',
            'users_verification_type' => 'required',
        ]);

        if ($validator->fails()) {
            $validate = validationMessage($validator->errors());
            return $this->sendError(1, 1, $validate);
        }

        $email = $request->email;
        $password = $request->password;
        $user_verification_type = $request->users_verification_type;

        if($user_verification_type == "sms"){

            $userphone = $request->users_hp;
            $pattern = '/^0/';
            $replacement = '+62';
            $userphone = preg_replace($pattern, $replacement, $userphone);
            $result = SMSverifyRequest($userphone);

            if ($result->getData()->data->status == 10) {
                return $this->sendError(2, "Verifikasi serentak ke nomor yang sama tidak diizinkan", (object) array());
            }

            if ($result->getData()->data->status == 3) {
                return $this->sendError(2, "Nomor handphone tidak valid", (object) array());
            }

            $data = ['request_id' => $result->getData()->data->request_id];

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

    public function registerAsUserFinish(Request $request)
    {
        $request_id = $request->request_id;
        $code = $request->code;
        $users_verification_type = $request->users_verification_type;
        $email = $request->email;

        if ($users_verification_type == "sms") {
            $result = SMSverifyCheck($request_id, $code);

            if ($result->getData()->data->status == 16) {
                return $this->sendError(2, "Kode Tidak cocok", (object) array());
            }
            if ($result->getData()->data->status == 3) {
                return $this->sendError(2, "Invalid value request id", (object) array());
            }

            if ($result->getData()->data->status == 6) {
                return $this->sendError(2, "Permintaan sudah diverifikasi", (object) array());
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


        $thisData = [
            'users_code' => generateFiledCode('USERS'),
            'name' => $request->name,
            'users_hp' => $request->users_hp,
            'users_company' => $request->users_company,
            'users_referral_code' => $request->users_referral_code,
            'users_npwp' => $request->users_npwp,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'users_verification_type' => ($request->users_verification_type == 'email' ? 1 : 0),
        ];



        try {

            $insertRegis = DB::table('users')->insert($thisData);

        } catch (\Exception $e) {

            return $this->sendError(2, "Gagal Registrasi", (object) array());

        }

        return $this->sendResponse(0, "Berhasil Registrasi");
    }



}
