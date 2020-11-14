<?php

use Illuminate\Support\Facades\Storage;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use \FCM as firebase;
use LaravelFCM\Message\Topics;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

if (!function_exists('getAuthenticateUser')) {
    function getAuthenticateUser()
    {
        return Auth::user();
    }
}

if (! function_exists('insertNotification')) {
    function insertNotification($param)
    {
        return DB::table('notification')->insert($param);;
    }
}

if (!function_exists('sendNotifToFirebase')) {
    function sendNotifToFirebase($token, $title, $message, $data)
    {
        $optionBuilder = new OptionsBuilder();

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($message)
        				    ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(array_merge($data,[
            'title' => $title,
            'body' => $message,
        ]));

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens = $token;

        $downstreamResponse = firebase::sendTo($tokens, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        return $downstreamResponse;
    }
}

if (!function_exists('getUserDevice')) {
    function getUserDevice($userCode)
    {
        $result = DB::table('user_device')
            ->where('user_code', $userCode)
            ->get();

        return $result;
    }
}

if (!function_exists('generateFiledCode')) {
    function generateFiledCode($code)
    {
        $result = $code.'-'.date('s').date('y').date('i').date('m').date('h').date('d').mt_rand(1000000, 9999999);

        return $result;
    }
}

if (!function_exists('generateInvoice')) {
    function generateInvoice($inv)
    {
        $result = $inv.'#'.date('d').date('m').date('y').date('m').date('h').date('d').mt_rand(1000000, 9999999);

        return $result;
    }
}

if (!function_exists('generateBreadcrumb')) {
    function generateBreadcrumb($data)
    {
        if (empty($data)) {
            return null;
        }

        $result = '<ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
					<li class="m-nav__item m-nav__item--home">
						<a href="'.url('/').'" class="m-nav__link m-nav__link--icon">
							<i class="m-nav__link-icon la la-home"></i>
						</a>
					</li>';

        foreach ($data as $uri => $item) {
            if ($uri == '!end!' || $uri == '#') {
                $result .= '<li class="m-nav__separator">-</li>
                            <a class="m-nav__link">
                                <span class="m-nav__link-text">'.$item.'</span>
                            </a>';

                continue;
            }
            $result .= '<li class="m-nav__separator">-</li>
						<li class="m-nav__item">
							<a href="'.url($uri).'" class="m-nav__link">
								<span class="m-nav__link-text">'.$item.'</span>
							</a>
						</li>';
        }

        $result .= '</ul>';

        return $result;
    }
}

// if (!function_exists('translate_message')) {
//     /**
//      * translate message return API.
//      *
//      * @param string $code
//      * @param string $lang
//      */
//     function translate_message($code = '0', $lang = 'indonesian')
//     {
//         if ($lang == 'indonesian') {
//             $message = array(
//                 '0' => 'sukses',
//                 '1' => 'Parameter error',
//                 '2' => 'Username atau password salah',
//                 '3' => 'Pencarian tidak ditemukan',
//                 '4' => 'Data tidak ditemukan',
//                 '5' => 'Register error',
//                 '6' => 'Forgot password error',
//                 '7' => 'Change password error',
//                 '8' => 'Update data error',
//                 '9' => 'Save data error',
//                 '10' => 'Delete data error',
//                 '11' => 'Invalid access token',
//                 '12' => 'Failed to send email',
//                 '13' => 'Invalid forgot token',
//                 '14' => 'Invalid Code',
//                 '15' => 'Failed to upload photo',
//                 '16' => 'Akun anda belum aktif',
//                 '17' => 'Akun Anda di-suspend',
//                 '18' => 'Session Anda telah habis',
//                 '19' => 'Data ditemukan',
//                 '20' => 'Data berhasil di buat',
//                 '21' => 'Data gagal di buat',
//                 '22' => 'Login berhasil',
//                 '23' => 'Logout berhasil',
//                 '24' => 'Logout gagal',
//                 '25' => 'Update data berhasil',
//                 '26' => 'exist',
//                 '27' => 'not exist',
//                 '28' => 'Update profile berhasil',
//                 '29' => 'Update profile gagal',
//                 '30' => 'Delete data berhasil',
//                 '31' => 'Valid Token',
//                 '32' => 'Invalid Token',
//                 '33' => 'Email tidak terdaftar',
//                 '34' => 'Link atur ulang kata sandi telah dikirim',
//                 '35' => 'Password lama tidak cocok',
//                 '36' => 'Email sudah terdaftar',
//                 '37' => 'Gagal',
//                 '38' => 'Akun sudah terdaftar, dan menunggu persetujuan Admin',
//                 '39' => 'Member Suspend',
//                 '40' => 'Member sudah terdaftar',
//                 '41' => 'Kuota Event telah penuh',
//                 '42' => 'Pendaftaran telah melebihi batas waktu',
//                 '43' => 'User yang sudah terdaftar melebihi batas maksimal',
//                 '44' => 'QR Generate required',
//                 '45' => 'Anda telah bergabung di event ini.',
//                 '46' => 'Anda belum bergabung di event ini.',
//             );
//
//             return isset($message[$code]) ? $message[$code] : $code.' - Kode tersebut belum terdefinisi di dalam sistem kami.';
//         }
//     }
// }

/*
 *  Encode base64 image and save to Storage
 */
if (!function_exists('uploadFotoWithFileName')) {
    function uploadFotoWithFileName($base64Data, $file_prefix_name)
    {
        $file_name = generateFiledCode($file_prefix_name).'.png';
        $insert_image = Storage::disk('public')->put($file_name, normalizeAndDecodeBase64Photo($base64Data));

        if ($insert_image) {
            return $file_name;
        }

        return false;
    }

    function normalizeAndDecodeBase64Photo($base64Data)
    {
        $replaceList = array(
            'data:image/jpeg;base64,',
            'data:image/jpg;base64,',
            'data:image/png;base64,',
            '[protected]',
            '[removed]',
        );
        $base64Data = str_replace($replaceList, '', $base64Data);

        return base64_decode($base64Data);
    }
}

if (!function_exists('validationMessage')) {
    function validationMessage($validation)
    {
        $validate = collect($validation)->flatten();

        return $validate->values()->all();
    }
}

/**
 * Normalize date input
 */
if (! function_exists('normalizeDateInput')) {
	function normalizeDateInput($date) {
		$invalidList = array(
			'0000-00-00'
		);

		if (in_array($date, $invalidList)) {
			return null;
		}

		return $date;
	}
}


/**
 * Easy to read date for user
 */
if (! function_exists('dateForUser')) {
	function dateForUser($date) {
		$date = normalizeDateInput($date);

		if ($date) {
			return date('d F Y', strtotime($date));
		}
		return $date;
	}
}

if (! function_exists('SMSverifyRequest')) {
    function SMSverifyRequest($phone){
        $url = 'https://api.nexmo.com/verify/json?' . http_build_query([
            'api_key' => env('SMS_API_KEY',null),
            'api_secret' => env('SMS_API_SECRET',null),
            'number' => $phone,
            'brand' => 'ON-VERIFY'
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        return response()->json(['data' => json_decode($response)]);
    }
}

if (! function_exists('SMSverifyCheck')) {
	function SMSverifyCheck($request_id, $code) {
        $url = 'https://api.nexmo.com/verify/check/json?' . http_build_query([
			'api_key' => env('SMS_API_KEY',null),
			'api_secret' => env('SMS_API_SECRET',null),
			'request_id' => $request_id,
			'code' => $code
		]);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

		return response()->json(['data' => json_decode($response)]);
	}
}

if (! function_exists('checkVerificationEmail')) {
    function checkVerificationEmail($token, $code, $email)
    {
        $where = ['token' => $token, 'email' => $email];
        $verify = DB::table('verification_email')->where($where)->first();

        if ($verify) {
            $checkExpires = ($verify->expires <= now() ? FALSE : TRUE );

            if (! $checkExpires) {

                return "TOKEN_EXPIRED";
            }else if ($code != $verify->code) {
                return "CODE_NOT_MATCH";
            }else {
                DB::table('verification_email')->where($where)->update(['verified_at' => now()]);
            }
        }
    }
}

if (!function_exists('calculateTotalPriceAds')) {
    function calculateTotalPriceAds($quantity)
    {
        return 10000000 * $quantity;
    }
}
