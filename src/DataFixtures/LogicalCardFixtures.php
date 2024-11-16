<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\LogicalCard;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Serializer\SerializerInterface;

class LogicalCardFixtures extends Fixture
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly string $projectDir,
    ) {
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $finder = new Finder();
        $finder->files()->in($this->projectDir . '/fixtures/cards/')->name('*.json')->sortByName();
        foreach ($finder as $file) {
            try {
                $LogicalCard = $this->serializer->deserialize($file->getContents(), LogicalCard::class, 'json');
            } catch (\Exception $exception) {
                throw new \RuntimeException("Cannot deserializer {$file}", previous: $exception);
            }
            $manager->persist($LogicalCard);
        }

        $manager->flush();
    }
}
