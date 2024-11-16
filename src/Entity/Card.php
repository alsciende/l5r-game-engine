<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\CardTypes\AllyCard;
use App\Entity\CardTypes\ArtifactCard;
use App\Entity\CardTypes\CompanionCard;
use App\Entity\CardTypes\ConditionCard;
use App\Entity\CardTypes\EventCard;
use App\Entity\CardTypes\MinionCard;
use App\Entity\CardTypes\PossessionCard;
use App\Entity\CardTypes\RingCard;
use App\Entity\CardTypes\SiteCard;
use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * One of the physical cards in a deck *during a game*.
 */
#[ORM\Entity(repositoryClass: CardRepository::class)]
#[ORM\MappedSuperclass]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string', length: 10)]
#[ORM\DiscriminatorMap(self::DISCRIMINATOR_MAP)]
#[ORM\Table('cards')]
abstract class Card
{
    public const array DISCRIMINATOR_MAP = [
        'Ally' => AllyCard::class,
        'Artifact' => ArtifactCard::class,
        'Companion' => CompanionCard::class,
        'Condition' => ConditionCard::class,
        'Event' => EventCard::class,
        'Minion' => MinionCard::class,
        'Possession' => PossessionCard::class,
        'Site' => SiteCard::class,
        'Ring' => RingCard::class,
    ];
    final public const string ATTR_BASE_VITALITY = 'base_vitality';
    final public const string ATTR_CURRENT_VITALITY = 'current_vitality';
    final public const string ATTR_BASE_STRENGTH = 'base_strength';
    final public const string ATTR_CURRENT_STRENGTH = 'current_strength';
    final public const string ATTR_WOUNDS = 'wounds';
    final public const string ATTR_TRANSIENT_EFFECTS = 'transient_effects';

    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\Column]
    private int $source;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $currentPlace = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * @var array<string,mixed>
     */
    #[ORM\Column]
    protected array $state = [];

    #[ORM\ManyToOne(inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $player = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitle = null;

    private ?string $type = null;

    public function __construct(string $id, int $source)
    {
        $this->id = $id;
        $this->source = $source;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSource(): int
    {
        return $this->source;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return array<string,mixed>
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * @return $this
     */
    public function setState(string $key, mixed $value): static
    {
        $this->state[$key] = $value;

        return $this;
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

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}
