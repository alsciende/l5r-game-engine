<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * This is a authenticated user of the platform, a person.
 */
class User implements UserInterface
{
    public string $id;

    #[\Override]
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    #[\Override]
    public function eraseCredentials(): void
    {
    }

    #[\Override]
    public function getUserIdentifier(): string
    {
        return $this->id;
    }
}
