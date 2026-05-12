<?php
namespace App\Http\Controllers\UserCustomer;

use App\Http\Controllers\Controller;

class UserDashboardController extends Controller
{
    //
    public function index()
    {

        return view('user_customer.home');
    }
}
