<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepository;
use App\Contracts\Service\UserContact;

class UserService implements UserContact
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Create a user 
     * 
     * @param user Array of user details
     * @return user Collection of newly created user
     */
    public function createUser($user)
    {
        return $this->userRepository->create($user);
    }
}
