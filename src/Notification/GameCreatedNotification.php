<?php

declare(strict_types=1);

namespace App\Notification;

use App\Entity\Game;

class GameCreatedNotification extends AbstractNotification
{
    public function __construct(
        private readonly Game $game,
    ) {
        $this->addTopic('/games');
        foreach ($this->game->getPlayers() as $player) {
            $this->addTopic('/users/' . $player->getUserId());
        }

        $event = new NotificationEvent('game_created');
        $event->add('id', $game->getId());

        $this->setEvent($event);
    }
}
