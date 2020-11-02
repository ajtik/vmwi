<?php

namespace DigitalOceanBundle\Manager;

use DigitalOceanBundle\Entity\DoSSHKey;
use DigitalOceanBundle\Repository\SSHKeyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use VirtualMachineBundle\Entity\SSHKey;
use VirtualMachineBundle\Manager\SSHKeyManagerInterface;
use VirtualMachineBundle\Repository\VirtualMachineRepositoryInterface;

class SSHKeyManager implements SSHKeyManagerInterface
{
    /** @var string */
    private $digitalOceanApiToken;

    /** @var HttpClientInterface */
    private $httpClient;

    /** @var EntityManagerInterface */
    private $entityManager;
    
    /** @var VirtualMachineRepositoryInterface */
    private $virtualMachinerRepository;

    /** @var SSHKeyRepository */
    private $sshKeyRepository;

    public function __construct(
        string $digitalOceanApiToken, 
        HttpClientInterface $httpClient, 
        SSHKeyRepository $sshKeyRepository,
        EntityManagerInterface $entityManager,
        VirtualMachineRepositoryInterface $virtualMachineRepository)
    {
        $this->digitalOceanApiToken = $digitalOceanApiToken;
        $this->httpClient = $httpClient;
        $this->sshKeyRepository = $sshKeyRepository;
        $this->entityManager = $entityManager;
        $this->virtualMachineRepository = $virtualMachineRepository;
    }

    public function create(SSHKey $sshKey): SSHKey
    {
        $doSshKey = $this->sshKeyRepository->findOneDoBy([
            'sshKey' => $sshKey
        ]);

        if ($doSshKey !== null) {
            throw new Exception("", 10);
        }

        $sshKeyBody = [
            'name' => $sshKey->getName(),
            'public_key' => $sshKey->getPublicKey()
        ];

        $sshKeyRequest = $this->httpClient->request('POST', 'https://api.digitalocean.com/v2/account/keys', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'auth_bearer' => $this->digitalOceanApiToken,
            'body' => json_encode($sshKeyBody)
        ]);

        $doSshKeyData = json_decode($sshKeyRequest->getContent(), true)['ssh_key'];

        $doSshKey = new DoSSHKey();
        $doSshKey->setDoId($doSshKeyData['id'])
            ->setSshKey($sshKey);

        $this->entityManager->persist($doSshKey);

        return $sshKey;
    }

    public function delete(SSHKey $sshKey): void
    {
        $doSshKey = $this->sshKeyRepository->findOneDoBy([
            'sshKey' => $sshKey
        ]);

        if ($doSshKey === null) {
            return;
        }

        $this->entityManager->remove($doSshKey);
        $this->entityManager->flush();

        $this->httpClient->request('DELETE', sprintf('https://api.digitalocean.com/v2/account/keys/%s', $doSshKey->getDoId()), [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'auth_bearer' => $this->digitalOceanApiToken
        ]);
    }
}