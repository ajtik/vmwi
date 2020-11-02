<?php

namespace VirtualMachineBundle\Repository;

use VirtualMachineBundle\Entity\VirtualMachine;

interface VirtualMachineRepositoryInterface
{
    public function find(int $id): ?VirtualMachine;

    /**
     * @param array<string,mixed>     $criteria
     */
    public function findOneBy(array $criteria): ?VirtualMachine;

    /**
     * @return array<VirtualMachine>
     */
    public function findAll(): array;

    /**
    * @param array<string,mixed>     $criteria
    * @param array<string,mixed>     $orderBy
    * @return array<VirtualMachine>
    */
   public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;
}