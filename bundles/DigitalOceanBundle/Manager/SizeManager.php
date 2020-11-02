<?php

namespace DigitalOceanBundle\Manager;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use VirtualMachineBundle\Manager\SizeManagerInterface;

class SizeManager implements SizeManagerInterface
{
    /** @var string */
    private $digitalOceanApiToken;

    /** @var HttpClientInterface */
    private $httpClient;

    public function __construct(string $digitalOceanApiToken, HttpClientInterface $httpClient)
    {
        $this->digitalOceanApiToken = $digitalOceanApiToken;
        $this->httpClient = $httpClient;
    }

    public function getAvailableSizes(): array
    {
        /*$request = $this->httpClient->request('GET', 'https://api.digitalocean.com/v2/sizes', [
            'auth_bearer' => $this->digitalOceanApiToken
        ]);

        return json_decode($request->getContent(), true)['sizes'];
        */

        // just for this purpose enable minimal setup cuz of $$$
        return [
            ["slug" => "s-1vcpu-1gb", "memory" => 1024, "vcpus" => 1, "disk" => 25]
        ];
    }
}