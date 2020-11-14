<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Validator;
use Illuminate\Support\Facades\Auth;

class HomeController extends ApiController
{
    public function __construct()
    {

    }

    public function test()
    {
        dd(Auth::user()->token()->id);
        return $this->sendResponse(0, 'Success');
    }

}
