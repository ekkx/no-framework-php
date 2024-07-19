<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\UserAlreadyRegisteredException;
use App\Exception\InvalidEmailOrPasswordException;
use App\Model\User;
use App\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /** @return User[] */
    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * @throws UserAlreadyRegisteredException
     */
    public function create(string $username, string $email, string $password): ?User
    {
        if ($this->userRepository->findOneByEmail($email)) {
            throw new UserAlreadyRegisteredException();
        }

        // TODO: hash password

        return $this->userRepository->create($username, $email, $password);
    }

    /**
     * @throws InvalidEmailOrPasswordException
     */
    public function login(string $email, string $password): void
    {
        $user = $this->userRepository->findOneByEmail($email);

        if (is_null($user)) {
            throw new InvalidEmailOrPasswordException();
        }

        if (!password_verify($password, $user->password)) {
            throw new InvalidEmailOrPasswordException();
        }
    }
}
