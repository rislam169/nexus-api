<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSetting;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use HttpResponses;

    /** Return user user information including user setting as a response */
    public function details(Request $request)
    {
        $user = $request->user();
        $userSetting = UserSetting::where("user_id", $user->id)->first();
        return $this->success(compact("user", "userSetting"));
    }
}
