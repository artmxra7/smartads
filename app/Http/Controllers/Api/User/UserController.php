<?php

namespace App\Http\Controllers\Api\User;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
use App\Http\Repositories\UserRepository;

class UserController extends ApiController
{


    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function ViewUserLastLogin(Request $request)

    {
        $result = true;
        if ($result) {
            $result = $this->sendResponse(0, 31);
        } else {
            $result = $this->sendError(2, 32);
        }
    }

    public function viewUserStatus()
    {
        $result = \App\User::select('users_status')
        ->where('users_status',1)
        ->where('users_code', Auth::user()->users_code);
        $data = $result->first();



        if ($result->exists()) {
            $result = $this->sendResponse(0, "Sukses", $data);
        } else {
            $result = $this->sendError(2, 4);
        }

        return $result;
    }

    public function viewUserExist()
    {
        $result = \App\User::select('users_status')
            ->where('users_status', 1)
            ->where('users_code', Auth::user()->users_code);

        if ($result->exists()) {
            $result = $this->sendResponse(0, "Sukses");
        } else {
            $result = $this->sendError(2, 4);
        }

        return $result;
    }

    public function details()
    {
        $result = $this->userRepository->getDetail(Auth::user()->users_code);

        if (!empty($result)) {
            $result = $this->sendResponse(0, 'Sukses', $result);

        } elseif ($result === false) {
            $result = $this->sendError(2, 4);
        } else {
            $result = $this->sendError(2, 4);
        }

        return $result;
    }

    public function update(Request $request)
    {
        $update = $this->userRepository->updateProfile($request, Auth::user()->users_code);

        if ($update == "ALREADY_EXISTS") {

            return $this->sendError(2, 'Email sudah terdaftar');
        }
        else if ($update) {

            return $this->sendResponse(0, 'Sukses');
        } else {
            return $this->sendError(2, 'Error');
        }
    }
}
