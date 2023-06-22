<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserSettingRepository;
use App\Models\UserSetting;
use Prettus\Repository\Eloquent\BaseRepository;

class UserSettingRepositoryEloquent extends BaseRepository implements UserSettingRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return UserSetting::class;
    }
}
