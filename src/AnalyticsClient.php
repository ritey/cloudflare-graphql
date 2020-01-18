<?php

namespace Wappr\Cloudflare;

use GraphQL\Query;
use GraphQL\Client;
use Wappr\Cloudflare\Contracts\ResourceInterface;

class AnalyticsClient
{
    protected $client;
    protected $resources = [];

    public function __construct($email, $key)
    {
        $this->client = new Client(
            'https://api.cloudflare.com/client/v4/graphql',
            [],
            [
                'headers' => [
                    'X-AUTH-EMAIL' => $email,
                    'X-AUTH-KEY'   => $key,
                ],
            ]
        );
    }

    public function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource->getResource();

        return $this;
    }

    public function runQuery()
    {
        // I'm not sure if this is a good idea, or good practice, or if I should
        // handle it another way. I like the idea of being able to add as many
        // resources and run them in the same query.
        if (!$this->resources) {
            throw new \Exception('Must add a resource before calling "runQuery()"');
        }

        $gql = (new Query('viewer'))->setSelectionSet($this->resources);

        return $this->client->runQuery($gql)->getResponseBody();
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
    }
}
