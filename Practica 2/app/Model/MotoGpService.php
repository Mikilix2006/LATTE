<?php
declare(strict_types=1);

use Nette\Utils\Json;

class MotoGpService
{
    // URL para MotoGP (ID: 4407)
    private string $apiUrl = 'https://www.thesportsdb.com/api/v1/json/3/lookupleague.php?id=4407';

    public function getLeagueDetails(): array
    {
        $content = @file_get_contents($this->apiUrl);
        if (!$content) {
            return [];
        }

        $data = Json::decode($content, Json::FORCE_ARRAY);
        return $data['leagues'][0] ?? [];
    }
}