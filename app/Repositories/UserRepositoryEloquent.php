<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepository;
use App\Models\User;
use Prettus\Repository\Eloquent\BaseRepository;

class UserRepositoryEloquent extends BaseRepository implements UserRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return User::class;
    }
}
