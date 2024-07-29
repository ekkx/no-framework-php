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

    /**
     * Finds a user by their ID.
     *
     * @param int $id
     *
     * @return ?User
     */
    public function findOneById(int $id): ?User
    {
        return $this->userRepository->findOneBy("id", $id);
    }

    /**
     * Returns all users.
     *
     * @return User[]
     */
    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * Creates a new user.
     *
     * @param SignupDto $body
     *
     * @return ?User
     * @throws UserAlreadyRegisteredException
     */
    public function create(SignupDto $body): ?User
    {
        // Check if a user with the same email already exists.
        // If so, throw an exception to prevent duplicate registrations.
        if ($this->userRepository->findOneBy("email", $body->email)) {
            throw new UserAlreadyRegisteredException();
        }

        // Note: In a real-world application, consider hashing passwords client-side for added security.
        // Here, we're hashing server-side for simplicity and demonstration purposes.
        $password = password_hash($body->password, PASSWORD_DEFAULT);

        // Create a new user with the provided username, email, and hashed password.
        return $this->userRepository->create($body->username, $body->email, $password);
    }

    /**
     * Generates an access token for a user.
     *
     * @param int $id
     *
     * @return string
     */
    private function generateAccessToken(int $id): string
    {
        // The token expires after 3 minutes and contains the user's ID.
        return JWT::encode([
            "exp" => time() + 60 * 3,
            "uid" => $id,
        ], $this->config->appSecretKey, "HS256");
    }

    /**
     * Logs in a user.
     *
     * @param LoginDto $body
     *
     * @return string
     * @throws InvalidEmailOrPasswordException
     */
    public function login(LoginDto $body): string
    {
        // Find the user by their email.
        $user = $this->userRepository->findOneBy("email", $body->email);

        // If the user doesn't exist or the password is incorrect, throw an exception.
        if (is_null($user) || !password_verify($body->password, $user->password)) {
            throw new InvalidEmailOrPasswordException();
        }

        // Update the user's last login time.
        $this->userRepository->updateLastLoginAt($user->id);

        // Generate and return an access token for the user.
        return $this->generateAccessToken($user->id);
    }
}
