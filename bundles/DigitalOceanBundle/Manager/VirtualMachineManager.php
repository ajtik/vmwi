<?php

namespace DigitalOceanBundle\Manager;

use App\Entity\User;
use App\Enum\VirtualMachineStatus;
use DigitalOceanBundle\Entity\DoSSHKey;
use DigitalOceanBundle\Entity\DoVirtualMachine;
use DigitalOceanBundle\Repository\SSHKeyRepository;
use DigitalOceanBundle\Repository\VirtualMachineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use VirtualMachineBundle\Entity\SSHKey;
use VirtualMachineBundle\Entity\VirtualMachine;
use VirtualMachineBundle\Manager\SSHKeyManagerInterface;
use VirtualMachineBundle\Manager\VirtualMachineManagerInterface;

class VirtualMachineManager implements VirtualMachineManagerInterface
{
    /** @var string */
    private $digitalOceanApiToken;

    /** @var HttpClientInterface */
    private $httpClient;

    /** @var SSHKeyRepository */
    private $sshKeyRepository;

    /** @var SSHKeyManagerInterface */
    private $sshKeyManager;

    /** @var VirtualMachineRepository */
    private $virtualMachineRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Security */
    private $security;

    public function __construct(
        string $digitalOceanApiToken, 
        HttpClientInterface $httpClient, 
        SSHKeyRepository $sshKeyRepository,
        VirtualMachineRepository $virtualMachineRepository,
        SSHKeyManagerInterface $sshKeyManager,
        EntityManagerInterface $em,
        Security $security
    ) {
        $this->digitalOceanApiToken = $digitalOceanApiToken;
        $this->httpClient = $httpClient;
        $this->sshKeyRepository = $sshKeyRepository;
        $this->sshKeyManager = $sshKeyManager;
        $this->virtualMachineRepository = $virtualMachineRepository;
        $this->entityManager = $em;
        $this->security = $security;
    }

    public function create(string $name, string $sizeSlug, string $imageSlug, int $sshKeyId): VirtualMachine
    {
        $doSshKey = $this->sshKeyRepository->findOneDoBy([
            'sshKey' => $sshKeyId
        ]);
        
        // if we have not DigitalOcean SSH key yet, then create one otherwise DigitalOcean will return 422
        if ($doSshKey === null) {
            $sshKey = $this->sshKeyRepository->find($sshKeyId);
            $sshKey = $this->sshKeyManager->create($sshKey);

            $doSshKey = $this->sshKeyRepository->findOneDoBy([
                'sshKey' => $sshKey
            ]);
        }

        $body = [
            'name' => $name,
            'region' => 'fra1',
            'size' => $sizeSlug,
            'image' => $imageSlug,
            'ssh_keys' => [
                $doSshKey->getDoId()
            ]
        ];

        $request = $this->httpClient->request('POST', 'https://api.digitalocean.com/v2/droplets', [
            'headers' => [
                'Content-type' => 'application/json'
            ],
            'auth_bearer' => $this->digitalOceanApiToken,
            'body' => json_encode($body)
        ]);

        $virtualMachineData = json_decode($request->getContent(), true)['droplet'];

        /** @var User $user */
        $user = $this->security->getUser();

        $virtualMachine = new VirtualMachine();
        $virtualMachine->setName($virtualMachineData['name'])
            ->setUser($user)
            ->setRam($virtualMachineData['memory'])
            ->setOs(sprintf('%s %s', $virtualMachineData['image']['distribution'], $virtualMachineData['image']['name']))
            ->setHdd($virtualMachineData['disk'])
            ->setVcpus($virtualMachineData['vcpus'])
            ->setStatus(VirtualMachineStatus::CREATED())
            ->setRegion($virtualMachineData['region']['name']);

        $user->addVirtualMachine($virtualMachine);

        $doVirtualMachine = new DoVirtualMachine();
        $doVirtualMachine->setDoId($virtualMachineData['id'])
            ->setVirtualMachine($virtualMachine)
            ->addSshKey($doSshKey);

        $this->entityManager->persist($doVirtualMachine);
        $this->entityManager->flush();

        return $virtualMachine;
    }

    public function checkStatus(VirtualMachine $virtualMachine): VirtualMachine
    {
        $doVirtualMachine = $this->virtualMachineRepository->findOneDoBy([
            'virtualMachine' => $virtualMachine
        ]);

        $request = $this->httpClient->request('GET', sprintf('https://api.digitalocean.com/v2/droplets/%s', $doVirtualMachine->getDoId()), [
            'auth_bearer' => $this->digitalOceanApiToken
        ]);

        $virtualMachineData = json_decode($request->getContent(), true)['droplet'];

        switch($virtualMachineData['status']) {
            case 'active':
                $status = VirtualMachineStatus::ONLINE();
            break;
            case 'off':
                $status = VirtualMachineStatus::OFFLINE();
            break;
            default:
                $status = VirtualMachineStatus::CREATED();
        }

        if (isset($virtualMachineData['networks']['v4'][0]['ip_address'])) {
            $virtualMachine->setIpV4Address($virtualMachineData['networks']['v4'][0]['ip_address']);
        }
        
        $virtualMachine->setStatus($status);

        $this->entityManager->flush();
        
        return $virtualMachine;
    }

    public function restart(VirtualMachine $virtualMachine): void
    {
        $doVirtualMachine = $this->virtualMachineRepository->findOneDoBy([
            'virtualMachine' => $virtualMachine
        ]);

        $body = [
            'type' => 'reboot'
        ];

        $request = $this->httpClient->request('POST', sprintf('https://api.digitalocean.com/v2/droplets/%s/actions', $doVirtualMachine->getDoId()), [
            'headers' => [
                'Content-type' => 'application/json'
            ],
            'auth_bearer' => $this->digitalOceanApiToken,
            'body' => json_encode($body)
        ]);
    }

    public function start(VirtualMachine $virtualMachine): void
    {
        $doVirtualMachine = $this->virtualMachineRepository->findOneDoBy([
            'virtualMachine' => $virtualMachine
        ]);

        $body = [
            'type' => 'power_on'
        ];

        $this->httpClient->request('POST', sprintf('https://api.digitalocean.com/v2/droplets/%s/actions', $doVirtualMachine->getDoId()), [
            'headers' => [
                'Content-type' => 'application/json'
            ],
            'auth_bearer' => $this->digitalOceanApiToken,
            'body' => json_encode($body)
        ]);
    }

    public function stop(VirtualMachine $virtualMachine): void
    {
        $doVirtualMachine = $this->virtualMachineRepository->findOneDoBy([
            'virtualMachine' => $virtualMachine
        ]);

        $body = [
            'type' => 'power_off'
        ];

        $this->httpClient->request('POST', sprintf('https://api.digitalocean.com/v2/droplets/%s/actions', $doVirtualMachine->getDoId()), [
            'headers' => [
                'Content-type' => 'application/json'
            ],
            'auth_bearer' => $this->digitalOceanApiToken,
            'body' => json_encode($body)
        ]);
    }

    public function delete(VirtualMachine $virtualMachine): void
    {
        $doVirtualMachine = $this->virtualMachineRepository->findOneDoBy([
            'virtualMachine' => $virtualMachine
        ]);

        $this->entityManager->remove($doVirtualMachine);
        $this->entityManager->flush();

        $this->httpClient->request('DELETE', sprintf('https://api.digitalocean.com/v2/droplets/%s', $doVirtualMachine->getDoId()), [
            'headers' => [
                'Content-type' => 'application/json'
            ],
            'auth_bearer' => $this->digitalOceanApiToken,
        ]);
    }
}