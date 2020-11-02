<?php declare(strict_types = 1);

namespace DigitalOceanBundle\Repository;

use DigitalOceanBundle\Entity\DoSSHKey;
use Doctrine\ORM\EntityManagerInterface;
use VirtualMachineBundle\Entity\SSHKey;
use VirtualMachineBundle\Repository\SSHKeyRepositoryInterface;

class SSHKeyRepository implements SSHKeyRepositoryInterface
{

    /** @var EntityManagerInterface */
    private $em;

    /** @var ObjectRepository */
    private $doRepository;

    /** @var ObjectRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->doRepository = $em->getRepository(DoSSHKey::class);
        $this->repository = $em->getRepository(SSHKey::class);
    }

    public function findDo(int $doId): ?DoSSHKey
    {
        return $this->doRepository->find($doId);
    }

    public function find(int $id): ?SSHKey
    {
        return $this->repository->find($id);
    }

    /**
     * @param array<string,mixed>     $criteria
     */
    public function findOneDoBy(array $criteria): ?DoSSHKey
    {
        return $this->doRepository->findOneBy($criteria);
    }

    /**
     * @param array<string,mixed>     $criteria
     */
    public function findOneBy(array $criteria): ?SSHKey
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return array<SSHKey>
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @param array<string,mixed>     $criteria
     * @param array<string,mixed>     $orderBy
     * @return array<SSHKey>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

}
