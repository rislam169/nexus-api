<?php

namespace App\Services;

use App\Contracts\Repositories\UserSettingRepository;
use App\Contracts\Service\UserSettingContact;

class UserSettingService implements UserSettingContact
{
    /**
     * @var UserSettingRepository
     */
    private $userSettingRepository;

    public function __construct(UserSettingRepository $userSettingRepository)
    {
        $this->userSettingRepository = $userSettingRepository;
    }

    /** 
     * Return user settings finding by user id
     * 
     * @param userId Id of the user
     * @param columns If ask for specific column
     * @return userSetting Collection of userSetting 
     */
    public function getSettingByUserId($userId, $columns = ["*"])
    {
        return $this->userSettingRepository->where("user_id", $userId)->first($columns);
    }

    /**
     * Save user settings or update if already a setting available for the user
     * 
     * @param userId Id of the user
     * @param settings Array of user settings and preference
     * @return userSetting Collection of userSetting
     */
    public function  updateOrCreateUserSetting($userId, $settings)
    {
        return $this->userSettingRepository->updateOrCreate(["user_id" => $userId], $settings);
    }
}
