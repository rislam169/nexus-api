<?php

namespace App\Contracts\Service;

interface UserSettingContact
{
    public function getSettingByUserId($userId);

    public function updateOrCreateUserSetting($userId, $settings);
}
