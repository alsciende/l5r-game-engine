<?php

declare(strict_types=1);

namespace App\Workflow;

use App\Entity\Game;
use App\Entity\Player;
use App\Service\PlayerStateManager;
use Symfony\Component\Workflow\Attribute\AsGuardListener;
use Symfony\Component\Workflow\Event\GuardEvent;

#[AsGuardListener(workflow: 'game', transition: 'setup_game')]
class SetupGameGuardListener
{
    public function __construct(
        readonly private PlayerStateManager $stateManager,
    ) {
    }

    public function __invoke(GuardEvent $event): void
    {
        /** @var Game $game */
        $game = $event->getSubject();

        foreach ($game->getPlayers() as $player) {
            if ($this->hasPlayerSelectedStrongholdProvince($player) === false) {
                $event->setBlocked(true, 'Player has not selected stronghold province.');
            }
        }
    }

    private function hasPlayerSelectedStrongholdProvince(Player $player): bool
    {
        $state = $this->stateManager->getState($player);

        return is_string($state->strongholdProvinceId);
    }
}
