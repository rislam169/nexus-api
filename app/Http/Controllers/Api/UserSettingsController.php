<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserSettingsRequest;
use App\Models\UserSetting;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserSettingsController extends Controller
{
    use HttpResponses;

    /** Save user settings */
    public function save(UserSettingsRequest $request)
    {
        $userSettings = $request->validated();
        $userId = $request->user()->id;
        try {
            DB::beginTransaction();

            $settings = UserSetting::updateOrCreate(
                [
                    "user_id" => $userId
                ],
                [
                    "source" => $userSettings["sources"],
                    "category" => $userSettings["categories"],
                    "author" => $userSettings["authors"]
                ]
            );

            DB::commit();

            return $this->success($settings, "Your settings saved successfully!");
        } catch (\Exception $e) {
            dd($e->getMessage());

            DB::rollback();
            Log::error($e);
            return $this->error(['message' => "Settings not saved! Please try again."], null, 500);
        }
    }
}
