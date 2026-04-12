<?php

namespace App\Services;

use Algolia\AlgoliaSearch\SearchClient;
use App\Models\BusinessSetting;

class AlgoliaService
{
    protected $client;
    protected $appId;
    protected $apiKey;

    public function __construct()
    {
        $this->appId = BusinessSetting::where('key', 'algolia_app_id')->first()?->value;
        $this->apiKey = BusinessSetting::where('key', 'algolia_api_key')->first()?->value;

        if ($this->appId && $this->apiKey) {
            $this->client = SearchClient::create($this->appId, $this->apiKey);
        }
    }

    /**
     * Index a model record.
     */
    public function indexRecord(string $indexName, array $data): void
    {
        if (!$this->client) return;

        $index = $this->client->initIndex($indexName);
        $index->saveObject($data, ['objectIDKey' => 'id']);
    }

    /**
     * Delete a model record.
     */
    public function deleteRecord(string $indexName, $id): void
    {
        if (!$this->client) return;

        $index = $this->client->initIndex($indexName);
        $index->deleteObject($id);
    }

    /**
     * Search for records.
     */
    public function search(string $indexName, string $query, array $params = []): array
    {
        if (!$this->client) return [];

        $index = $this->client->initIndex($indexName);
        return $index->search($query, $params);
    }
}
