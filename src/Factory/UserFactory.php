<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    private string $password;

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->password = $this->passwordHasher->hashPassword(new User(), '1234');
    }

    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'email'    => self::faker()->unique()->email(),
            'password' => $this->password,
            'roles'    => ["admin"],
        ];
    }
}
