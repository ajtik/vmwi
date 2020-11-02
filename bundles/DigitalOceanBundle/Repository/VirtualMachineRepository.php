<?php

namespace DigitalOceanBundle\Repository;

use DigitalOceanBundle\Entity\DoVirtualMachine;
use Doctrine\ORM\EntityManagerInterface;
use VirtualMachineBundle\Entity\VirtualMachine;
use VirtualMachineBundle\Repository\VirtualMachineRepositoryInterface;

class VirtualMachineRepository implements VirtualMachineRepositoryInterface
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
        $this->doRepository = $em->getRepository(DoVirtualMachine::class);
        $this->repository = $em->getRepository(VirtualMachine::class);
    }

    public function find(int $id): ?VirtualMachine
    {
        return $this->repository->find($id);
    }

    /**
     * @param array<string,mixed>     $criteria
     */
    public function findOneBy(array $criteria): ?VirtualMachine
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @param array<string,mixed>     $criteria
     */
    public function findOneDoBy(array $criteria): ?DoVirtualMachine
    {
        return $this->doRepository->findOneBy($criteria);
    }

    /**
     * @return array<VirtualMachine>
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /**
    * @param array<string,mixed>     $criteria
    * @param array<string,mixed>     $orderBy
    * @return array<VirtualMachine>
    */
   public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
   {
       return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
   }
}
