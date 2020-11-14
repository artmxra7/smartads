<?php

namespace App\Http\Repositories;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Users;
use App\Http\Resources\UserSearchResult;
use Carbon\Carbon;

class UserRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getDetail($users_code)
    {
        if (empty($users_code)) {
            return false;
        }

        $users = DB::table('users')->select('*')
            ->where('users.id', '!=', 2)
            ->where('users_status', 1)
            ->where('users.users_code', $users_code)
            ->limit(1)
            ->first();
        $user = (collect($users)->count()) ? new UserResource($users) : false;

        return $user;
    }

    public function updateProfile($request, $user_code)
    {
        if (empty($user_code)) {
            return false;
        }

        $email = DB::table('users')->select('email')->where('users_status', 1)->where('email', $request->users_email)->get();
        $code = DB::table('users')->select('email')->where('users_status', 1)->where('users_code', $user_code)->first();

        $data = [
            'name' => $request->users_name,
            'users_hp' => $request->users_hp,
            'users_company' => $request->users_company,
            'users_npwp' => $request->users_npwp,
            'email' => $request->users_email,
            'users_date_updated' => Carbon::now()
        ];

        if ($request->users_email) {

                if (count($email) > 0) {
                    if ($request->users_email != $code->email) {
                        return 'ALREADY_EXISTS';
                    }
                }else{
                    $data['email'] = $request->users_email;
                }

        }
        $update = User::where('users_status', 1)
            ->where('users_code', $user_code)
            ->update($data);

        return $update;
    }


}
