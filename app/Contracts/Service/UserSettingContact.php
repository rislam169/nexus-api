<?php

namespace App\Contracts\Service;

interface UserSettingContact
{
    public function getSettingByUserId($userId, $comumns = ["*"]);

    public function updateOrCreateUserSetting($userId, $settings);
}
