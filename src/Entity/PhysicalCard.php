<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\CardTypes\ConflictCard;
use App\Entity\CardTypes\DynastyCard;
use App\Entity\CardTypes\ProvinceCard;
use App\Entity\CardTypes\RoleCard;
use App\Enum\Side;
use App\Enum\Type;
use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * One of the stateful, physical cards in a deck *during a game*.
 */
#[ORM\Entity(repositoryClass: CardRepository::class)]
#[ORM\MappedSuperclass]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'side', type: 'string', length: 10)]
#[ORM\DiscriminatorMap(self::DISCRIMINATOR_MAP)]
#[ORM\Table('physical_cards')]
abstract class PhysicalCard
{
    public const array DISCRIMINATOR_MAP = [
        Side::CONFLICT->value => ConflictCard::class,
        Side::DYNASTY->value => DynastyCard::class,
        Side::PROVINCE->value => ProvinceCard::class,
        Side::ROLE->value => RoleCard::class,
    ];

    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\ManyToOne(inversedBy: 'physicalCards')]
    #[ORM\JoinColumn(nullable: false)]
    private LogicalCard $logicalCard;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $currentPlace = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * @var array<string,mixed>
     */
    #[ORM\Column]
    protected array $state = [];

    #[ORM\ManyToOne(inversedBy: 'physicalCards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\ManyToOne(inversedBy: 'physicalCards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $player = null;

    private ?string $side = null;

    public function __construct(string $id, LogicalCard $logicalCard)
    {
        $this->id = $id;
        $this->logicalCard = $logicalCard;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLogicalCard(): LogicalCard
    {
        return $this->logicalCard;
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

    public function getSide(): ?string
    {
        return $this->side;
    }

    /**
     * @return list<Type>
     */
    abstract public function getAllowedTypes(): array;
}
