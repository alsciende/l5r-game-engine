<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DeckRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * A deck is a collection of card codes
 * A deck has no existence inside a Game
 * A deck is used to generate the list of Cards linked to a Game.
 */
#[ORM\Entity(repositoryClass: DeckRepository::class)]
#[ORM\Table('decks')]
class Deck
{
    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var array<int>
     */
    #[ORM\Column(type: 'integer[]')]
    private array $content = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @param array<int> $content
     *
     * @return $this
     */
    public function setContent(array $content): static
    {
        $this->content = $content;

        return $this;
    }
}
