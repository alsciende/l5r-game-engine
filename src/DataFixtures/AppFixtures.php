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
    public const array ARAGON_STARTER_DECK = [
        1000002, // The One Ring	The Ruling Ring
        1000290, // Frodo	Son of Drogo
        1000327, // Bree Gate
        1000340, // Rivendell Terrace
        1000346, // Moria Lake
        1000349, // The Bridge of Khazad-dûm
        1000351, // Galadriel's Glade
        1000355, // Silverlode Banks
        1000358, // Pillars of the Kings
        1000361, // Slopes of Amon Hen
        1000365, // Aragorn	King in Exile
        1000365, // Aragorn	King in Exile
        1000094, // Athelas
        1000095, // Blade of Gondor
        1000097, // Boromir	Son of Denethor
        1000097, // Boromir	Son of Denethor
        1000101, // Coat of Mail
        1000104, // Eregion's Trails
        1000104, // Eregion's Trails
        1000107, // Great Shield
        1000107, // Great Shield
        1000299, // Hobbit Sword
        1000299, // Hobbit Sword
        1000051, // Legolas	Prince of Mirkwood
        1000108, // No Stranger to the Shadows
        1000108, // No Stranger to the Shadows
        1000110, // Pathfinder
        1000110, // Pathfinder
        1000311, // Sam	Son of Hamfast
        1000116, // Swordarm of the White Tower
        1000116, // Swordarm of the White Tower
        1000116, // Swordarm of the White Tower
        1000117, // Swordsman of the Northern Kingdom
        1000117, // Swordsman of the Northern Kingdom
        1000117, // Swordsman of the Northern Kingdom
        1000121, // Bred for Battle
        1000121, // Bred for Battle
        1000121, // Bred for Battle
        1000128, // Lurtz's Battle Cry
        1000127, // Lurtz	Servant of Isengard
        1000133, // Saruman's Ambition
        1000133, // Saruman's Ambition
        1000141, // Their Arrows Enrage
        1000141, // Their Arrows Enrage
        1000150, // Uruk Rager
        1000150, // Uruk Rager
        1000150, // Uruk Rager
        1000151, // Uruk Savage
        1000151, // Uruk Savage
        1000151, // Uruk Savage
        1000152, // Uruk Shaman
        1000152, // Uruk Shaman
        1000152, // Uruk Shaman
        1000153, // Uruk Slayer
        1000153, // Uruk Slayer
        1000154, // Uruk Soldier
        1000154, // Uruk Soldier
        1000157, // Uruk-hai Armory
        1000157, // Uruk-hai Armory
        1000158, // Uruk-hai Raiding Party
        1000158, // Uruk-hai Raiding Party
    ];

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $deck = new Deck('1');
        $manager->persist($deck);
        $deck->setName('Aragorn Starter Deck');
        $deck->setContent(self::ARAGON_STARTER_DECK);

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
