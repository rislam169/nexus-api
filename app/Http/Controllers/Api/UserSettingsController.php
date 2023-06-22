<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Service\UserSettingContact;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserSettingsRequest;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserSettingsController extends Controller
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

    /** Save user settings */
    public function save(UserSettingsRequest $request)
    {
        $userSettings = $request->validated();
        $userId = $request->user()->id;
        try {
            DB::beginTransaction();

            $settings = $this->userSettingService->updateOrCreateUserSetting(
                $userId,
                [
                    "source" => $userSettings["sources"],
                    "category" => $userSettings["categories"],
                    "author" => $userSettings["authors"]
                ]
            );

            DB::commit();

            return $this->success($settings, "Your settings saved successfully!");
        } catch (\Exception $e) {

            DB::rollback();
            Log::error($e);
            return $this->error(['message' => "Settings not saved! Please try again."], null, 500);
        }
    }
}
