<?php

declare(strict_types=1);

namespace App\Service;

use App\Config;
use App\Dto\LoginDto;
use App\Dto\SignupDto;
use App\Exception\UserAlreadyRegisteredException;
use App\Exception\InvalidEmailOrPasswordException;
use App\Model\User;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;

class UserService
{
    private Config $config;
    private UserRepository $userRepository;

    public function __construct(Config $config, UserRepository $userRepository)
    {
        $this->config = $config;
        $this->userRepository = $userRepository;
    }

    public function findOneById(int $id): ?User
    {
        return $this->userRepository->findOneBy("id", $id);
    }

    /** @return User[] */
    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * @throws UserAlreadyRegisteredException
     */
    public function create(SignupDto $body): ?User
    {
        if ($this->userRepository->findOneBy("email", $body->email)) {
            throw new UserAlreadyRegisteredException();
        }

        $password = password_hash($body->password, PASSWORD_DEFAULT);

        return $this->userRepository->create($body->username, $body->email, $password);
    }

    private function generateAccessToken(int $userId): string
    {
        return JWT::encode([
            "exp" => time() + 60 * 3, // 3 minutes
            "uid" => $userId,
        ], $this->config->appSecretKey, "HS256");
    }

    /**
     * @throws InvalidEmailOrPasswordException
     */
    public function login(LoginDto $body): string
    {
        $user = $this->userRepository->findOneBy("email", $body->email);

        if (is_null($user)) {
            throw new InvalidEmailOrPasswordException();
        }

        if (!password_verify($body->password, $user->password)) {
            throw new InvalidEmailOrPasswordException();
        }

        $this->userRepository->updateLastLoginAt($user->id);

        return $this->generateAccessToken($user->id);
    }
}
