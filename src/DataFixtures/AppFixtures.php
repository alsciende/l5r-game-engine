<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Candidate;
use App\Entity\Deck;
use App\Entity\Game;
use App\Entity\Player;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    use StarterDeckLionTrait;

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $deck = new Deck('1');
        $manager->persist($deck);
        $deck->setName('Lion Clan Starter Deck');
        $deck->setContent($this->getLionStarterCards());

        $john = new Candidate();
        $manager->persist($john);
        $john->setUserId('john');
        $john->setDeck($deck);

        $jane = new Candidate();
        $manager->persist($jane);
        $jane->setUserId('jane');
        $jane->setDeck($deck);

        $game = new Game('1');
        $manager->persist($game);

        $harry = new Player('1');
        $manager->persist($harry);
        $harry->setUserId('harry');
        $harry->setDeck($deck);
        $game->addPlayer($harry);

        $sally = new Player('2');
        $manager->persist($sally);
        $sally->setUserId('sally');
        $sally->setDeck($deck);
        $game->addPlayer($sally);

        $manager->flush();
    }
}
