<?php

namespace VirtualMachineBundle\Repository;

use VirtualMachineBundle\Entity\SSHKey;

interface SSHKeyRepositoryInterface
{
    public function find(int $id): ?SSHkey;

    /**
     * @param array<string,mixed>     $criteria
     */
    public function findOneBy(array $criteria): ?SSHKey;

    /**
     * @return array<SSHKey>
     */
    public function findAll(): array;

    /**
    * @param array<string,mixed>     $criteria
    * @param array<string,mixed>     $orderBy
    * @return array<SSHKey>
    */
   public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;
}