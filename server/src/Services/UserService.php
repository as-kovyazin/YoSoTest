<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Throwable;

class UserService
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function createUser(): ?User
    {
        try {
            $user = new User();
            $user->setApiKey(bin2hex(random_bytes(10)));

            $this->userRepository->add($user, true);
        } catch (Throwable $err) {
            return null;
        }
        return $user;
    }
}