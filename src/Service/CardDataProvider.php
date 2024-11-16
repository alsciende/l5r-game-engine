<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\Data\SourceNotFoundException;
use App\Model\CardData;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Serializer\SerializerInterface;

#[Autoconfigure(lazy: true)]
class CardDataProvider
{
    /**
     * @var array<int, CardData>
     */
    private array $cardsData = [];

    public function __construct(
        string $cardsFile,
        private readonly SerializerInterface $serializer,
    ) {
        $contents = file_get_contents($cardsFile);
        if ($contents === false) {
            throw new \RuntimeException("Wrong path [{$cardsFile}] for cards file");
        }

        /** @var array<CardData> $list */
        $list = $this->serializer->deserialize(
            $contents,
            CardData::class . '[]',
            'yaml'
        );

        foreach ($list as $item) {
            $this->cardsData[$item->id] = $item;
        }
    }

    public function getCardData(int $source): CardData
    {
        if (array_key_exists($source, $this->cardsData)) {
            return $this->cardsData[$source];
        }

        throw new SourceNotFoundException($source);
    }
}
