<?php

namespace App\Http\Repositories;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\User as UserResource;

class UserService
{

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function insertRegisterAsUser($request)
    {
        $user = new User();
        $user->users_code = generateFiledCode('USER');
        $user->name = $request->name;
        $user->users_hp = $request->users_hp;
        $user->users_company = $request->users_company;
        $user->users_referral_code = $request->users_referral_code;
        $user->users_npwp = $request->users_npwp;
        $user->save();

        return $user;


    }


    public function updateRegister($request)
    {
        if (empty($request->chckRegist))
        {
            return false;
        }


        $data = [
            'email' => $request->email,
            'password' => $request->password,
            'users_verification_type' => $request->users_verification_type
        ];

        dd($data);


        $update = User::where('users_activation_status', 1)
            ->where('email', $request->email)
            ->update($data);

        return $update;
    }

}
