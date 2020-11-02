<?php

namespace DigitalOceanBundle\Manager;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use VirtualMachineBundle\Manager\ImageManagerInterface;

class ImageManager implements ImageManagerInterface
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

    public function getAvailableImages(): array
    {
        $request = $this->httpClient->request('GET', 'https://api.digitalocean.com/v2/images?type=distribution', [
            'auth_bearer' => $this->digitalOceanApiToken
        ]);

        return json_decode($request->getContent(), true)['images'];
    }
}