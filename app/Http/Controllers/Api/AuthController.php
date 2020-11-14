<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Repositories\AuthRepository;
use App\Http\Controllers\ApiController;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Repositories\UserService;
use App\User;
use App\Partner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    protected $authRepository;
    protected $userService;

    public function __construct(AuthRepository $authRepository, UserService $userService)
    {
        $this->authRepository = $authRepository;
        $this->userService = $userService;
    }

    public function login(Request $request)
    {
        $rules = array(
    	    'email'    => 'required|email',
    	    'password' => 'required',
	    );

        $input = array(
            'email' => $request->email,
            'password' => $request->password
        );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return $this->sendError(1, "Error params", $validator->errors());
        }

        $getUserType = @$this->checkUserType($request->email);

        $credentials = array(
            'email' => $input['email'],
            'password' => $input['password'],
        );

        if (! Auth::guard($getUserType)->attempt($credentials)) {
            return $this->sendError(2, "Email atau Password Salah", (object) array());
        }

        $userType = ($getUserType == 'web' ? 'web' : 'partner' );

        $user = Auth::guard($userType)->user();
        $device = $request->except(['email', 'password']);

        $insertUserDevice = $this->authRepository->SaveOrUpdateDeviceData($device, $user, $userType);

        $success['token'] = Auth::guard($userType)->user()->createToken(Auth::guard($userType)->user()->email)->accessToken;
        $success['user_type'] = ($getUserType == 'web' ? 'user' : 'partner' );

        return $this->sendResponse(0, "Login Berhasil", $success);

    }

    public function checkUserType($email)
    {
        if (User::where('email',$email)->exists()) {
            return 'web';
        } elseif (Partner::where('email', $email)->exists()) {
            return 'partner';
        } else {
            return FALSE;
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->token()->delete();

            return $this->sendResponse(0, "Logout berhasil.", (object) array());
        } else {
            return $this->sendError(2, "Logout gagal.", (object) array());
        }
    }

    public function registerAsUserStepOne(Request $request)
    {
        $validator = $this->authRepository->getValidationRegisterAsUserStepOne($request);


        if ($validator->fails()) {
            $validate = validationMessage($validator->errors());
            return $this->sendError(1, 1, $validate);
        }


        $userCode = generateFiledCode('USERS');
        $name = $request->name;
        $users_hp = $request->users_hp;
        $users_company = $request->users_company;
        $users_referral_code = $request->users_referral_code;
        $users_npwp = $request->users_npwp;





       return $this->sendResponse(0, "Success");
    }

    public function registerAsUserStepTwo(Request $request)
    {

        $requc = $request->session()->get('users_code', function () {
            return $this->sendError(2, "Terjadi kesalahan sistem.");
        });

        $validator = $this->authRepository->getValidationRegisterAsUser($request);

        if ($validator->fails()) {
            $validate = validationMessage($validator->errors());
            return $this->sendError(2, "Email Sudah terpakai", (object) array());
        }

        $email = $request->session()->put('email'.$requc, $request->input('email'));
        $password = $request->session()->put('password'.$requc, $request->input('password'));
        $user_verification_type = $request->session()->put('users_verification_type'.$requc, $request->input('users_verification_type'));

        //dd( $request->session()->get('users_verification_type'.$requc));

        if($request->users_verification_type == "sms"){

            $userphone = $request->session()->get('users_hp'.$requc);
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
        $requc = $request->session()->get('users_code', function () {
            return $this->sendError(2, "Terjadi kesalahan sistem.");
        });

        $request_id = $request->request_id;
        $code = $request->code;
        $users_verification_type = $request->session()->get('users_verification_type'.$requc);
        $email = $request->session()->get('email'.$requc);

        //dd( $request->session()->get('users_verification_type'.$requc));

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
            'users_code' => $requc,
            'name' => $request->session()->get('name'.$requc),
            'users_hp' => $request->session()->get('users_hp'.$requc),
            'users_company' => $request->session()->get('users_company'.$requc),
            'users_referral_code' => $request->session()->get('users_referral_code'.$requc),
            'users_npwp' => $request->session()->get('users_npwp'.$requc),
            'email' => $request->session()->get('email'.$requc),
            'password' => Hash::make($request->session()->get('password'.$requc)),
            'users_verification_type' => ($request->session()->get('users_verification_type'.$requc) == 'email' ? 1 : 0),
        ];

        try {

            $insertRegis = DB::table('users')->insert($thisData);

            //dd($insertRegis);
        } catch (\Exception $e) {

            return $this->sendError(2, "Gagal Registrasi", (object) array());
        }

        return $this->sendResponse(0, "Berhasil Registrasi");

    }


    public function tokencek()
    {
        dd(Auth::user());
    }

    public function registerPartner(Request $request)
    {
        $validator = $this->getValidationRegisterPartner($request);

        if ($validator->fails()) {
            $validate = validationMessage($validator->errors());

            return $this->sendError(1, 1, $validate);
        }

        $result = $this->authRepository->insertRegisterPartner($request);

        if ($result) {
            return $this->sendResponse(0, 'Registrasi Berhasil');
        } else {
            return $this->sendError(2, 5);
        }
    }

    public function getValidationRegisterPartner($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'ktp' => 'required',
            'hp' => 'required',
            'npwp' => 'required',
            'company' => 'required',
            'provinsi_code' => 'required',
            'kota_code' => 'required',
            'kec_code' => 'required',
            'kel_code' => 'required',
            'address' => 'required',
            'email' => 'required|string|email|unique:partners',
            'password' => 'required|min:8',
            'password_confirmation' => 'required_with:password|same:password|min:8',
            'verification_type' => 'required',
        ]);

        return $validator;
    }

    public function ViewUserTokenExpired()
    {
        $result = $this->authRepository->getExpiredToken(Auth::user()->token()->id);

        if ($result) {
            $result = $this->sendResponse(0, 'Valid Token');
        } else {
            $result = $this->sendError(2, 'Invalid Token');
        }

        return $result;
    }

}
