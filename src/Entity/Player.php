<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is one of the 2 players in a game.
 */
#[ORM\Entity(repositoryClass: PlayerRepository::class)]
#[ORM\Table('players')]
class Player
{
    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\Column(length: 255)]
    private ?string $userId = null;

    /**
     * @var ArrayCollection<int, Card>
     */
    #[ORM\OneToMany(mappedBy: 'player', targetEntity: Card::class, orphanRemoval: true)]
    private Collection $cards;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Deck $deck = null;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->cards = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return Collection<int, Card>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): static
    {
        if (! $this->cards->contains($card)) {
            $this->cards->add($card);
            $card->setPlayer($this);
        }

        return $this;
    }

    public function removeCard(Card $card): static
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getPlayer() === $this) {
                $card->setPlayer(null);
            }
        }

        return $this;
    }

    public function getDeck(): ?Deck
    {
        return $this->deck;
    }

    public function setDeck(?Deck $deck): static
    {
        $this->deck = $deck;

        return $this;
    }
}
