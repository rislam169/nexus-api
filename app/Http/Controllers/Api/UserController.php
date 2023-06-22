<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Service\UserSettingContact;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSetting;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use HttpResponses;

    /**
     * @var UserSettingContact
     */
    private $userSettingService;

    public function __construct(UserSettingContact $userSettingService)
    {
        $this->userSettingService = $userSettingService;
    }

    /** Return user user information including user setting as a response */
    public function details(Request $request)
    {
        $user = $request->user();
        $userSetting = $this->userSettingService->getSettingByUserId($user->id);

        return $this->success(compact("user", "userSetting"));
    }
}
