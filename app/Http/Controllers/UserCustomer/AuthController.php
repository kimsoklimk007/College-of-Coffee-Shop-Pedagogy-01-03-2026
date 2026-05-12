<?php

namespace App\Http\Controllers\UserCustomer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //direct register page
    public function registerPage(){
        return view('user_customer.authentication.register');
    }

    public function loginPage(){
        return view('user_customer.authentication.login');
    }
}
