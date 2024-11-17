<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * An ongoing game with players.
 */
#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\Table('games')]
class Game
{
    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $currentPlace = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $state = '{}';

    /**
     * @var ArrayCollection<int, Player>
     */
    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Player::class, cascade: ['persist', 'remove'], fetch: 'EAGER', orphanRemoval: true)]
    private Collection $players;

    /**
     * @var ArrayCollection<int, PhysicalCard>
     */
    #[ORM\OneToMany(mappedBy: 'game', targetEntity: PhysicalCard::class, cascade: ['persist', 'remove'], fetch: 'EAGER', orphanRemoval: true)]
    private Collection $physicalCards;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->players = new ArrayCollection();
        $this->physicalCards = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCurrentPlace(): ?string
    {
        return $this->currentPlace;
    }

    public function setCurrentPlace(?string $currentPlace): static
    {
        $this->currentPlace = $currentPlace;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): static
    {
        if (! $this->players->contains($player)) {
            $this->players->add($player);
            $player->setGame($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): static
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getGame() === $this) {
                $player->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PhysicalCard>
     */
    public function getPhysicalCards(): Collection
    {
        return $this->physicalCards;
    }

    public function addCard(PhysicalCard $card): static
    {
        if (! $this->physicalCards->contains($card)) {
            $this->physicalCards->add($card);
            $card->setGame($this);
        }

        return $this;
    }

    public function removeCard(PhysicalCard $card): static
    {
        if ($this->physicalCards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getGame() === $this) {
                $card->setGame(null);
            }
        }

        return $this;
    }
}
