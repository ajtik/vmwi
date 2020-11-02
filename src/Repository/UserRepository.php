<?php declare(strict_types = 1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository implements PasswordUpgraderInterface
{

    /** @var EntityManagerInterface */
    private $em;

    /** @var ObjectRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(User::class);
    }

    public function find(int $id): ?User
    {
        return $this->repository->find($id);
    }

    /**
     * @param array<string,mixed>     $criteria
     */
    public function findOneBy(array $criteria): ?User
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return array<User>
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @param array<string,mixed>     $criteria
     * @param array<string,mixed>     $orderBy
     * @return array<User>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->em->persist($user);
        $this->em->flush();
    }

}
