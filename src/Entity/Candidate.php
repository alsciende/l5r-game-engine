<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CandidateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * A user waiting to be matched in a game.
 */
#[ORM\Entity(repositoryClass: CandidateRepository::class)]
#[ORM\Table('candidates')]
class Candidate
{
    #[ORM\Id]
    #[ORM\GeneratedValue('SEQUENCE')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $userId;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Deck $deck;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getDeck(): Deck
    {
        return $this->deck;
    }

    public function setDeck(Deck $deck): static
    {
        $this->deck = $deck;

        return $this;
    }
}
