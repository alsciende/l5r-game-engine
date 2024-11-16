<?php

declare(strict_types=1);

namespace App\Tests\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Exception\JsonException;

class ApiTest extends WebTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        $client = static::createClient();
        $this->markTestSkipped(
            'API Scenario not available',
        );
    }

    /**
     * @throws JsonException
     */
    private function get(string $username, string $url): array
    {
        $client = static::getClient();
        $client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $username);
        $client->jsonRequest('GET', $url);

        return $client->getInternalResponse()->toArray();
    }

    /**
     * @throws JsonException
     */
    private function post(string $username, string $url, array $body): array
    {
        $client = static::getClient();
        $client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $username);
        $client->jsonRequest('POST', $url, $body);

        return $client->getInternalResponse()->toArray();
    }

    public function testScenario(): void
    {
        $playerIds = [];

        /**
         * As John.
         */
        $username = 'john';

        // get self info
        $response = $this->get($username, '/api/self');
        $this->assertResponseIsSuccessful();
        $this->assertSame([
            'user' => $username,
        ], $response);

        // create deck
        $response = $this->post($username, '/api/decks', [
            'name' => 'Aragorn Starter',
            'cards' => $this->getAragornStarter(),
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('id', $response);
        $deckId = $response['id'];
        $this->assertArrayHasKey('cards', $response);
        $this->assertCount(63, $response['cards']);

        // create game
        $response = $this->post($username, '/api/games', [
            // empty for now
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('id', $response);
        $gameId = $response['id'];
        $this->assertArrayHasKey('players', $response);
        $this->assertCount(0, $response['players']);

        // fetch game
        $response = $this->get($username, '/api/games/' . $gameId);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('id', $response);
        $this->assertSame($gameId, $response['id']);
        $this->assertArrayHasKey('players', $response);
        $this->assertCount(0, $response['players']);

        // join game with deck
        $response = $this->post($username, '/api/games/' . $gameId . '/players', [
            'deck' => $deckId,
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('id', $response);
        $playerIds[$username] = $response['id'];

        // fetch game again
        $response = $this->get($username, '/api/games/' . $gameId);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('id', $response);
        $this->assertSame($gameId, $response['id']);
        $this->assertArrayHasKey('players', $response);
        $this->assertCount(1, $response['players']);
        $this->assertSame($playerIds[$username], $response['players'][0]['id']);

        /**
         * As Jane.
         */
        $username = 'jane';

        // get self info
        $response = $this->get($username, '/api/self');
        $this->assertResponseIsSuccessful();
        $this->assertSame([
            'user' => $username,
        ], $response);

        // create deck
        $response = $this->post($username, '/api/decks', [
            'name' => 'Aragorn Starter',
            'cards' => $this->getAragornStarter(),
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('id', $response);
        $deckId = $response['id'];
        $this->assertArrayHasKey('cards', $response);
        $this->assertCount(63, $response['cards']);

        // fetch games
        $response = $this->get($username, '/api/games');
        $this->assertResponseIsSuccessful();
        $this->assertNotSame(0, $response['items']);

        // fetch game
        $response = $this->get($username, '/api/games/' . $gameId);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('id', $response);
        $this->assertSame($gameId, $response['id']);
        $this->assertArrayHasKey('players', $response);
        $this->assertCount(1, $response['players']);

        // join game with deck
        $response = $this->post($username, '/api/games/' . $gameId . '/players', [
            'deck' => $deckId,
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('id', $response);
        $playerIds[$username] = $response['id'];

        // fetch game again
        $response = $this->get($username, '/api/games/' . $gameId);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('id', $response);
        $this->assertSame($gameId, $response['id']);
        $this->assertArrayHasKey('players', $response);
        $this->assertCount(2, $response['players']);
        $this->assertArrayHasKey('status', $response);
        $this->assertSame('started', $response['status']);
    }
}
