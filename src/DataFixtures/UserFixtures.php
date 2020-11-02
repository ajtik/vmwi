<?php declare(strict_types = 1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        // create admin user
        $user = new User();
        $user->setEmail('adam@cuba-developer.cz');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'test1234'
        ));

        $user->setRoles(['ROLE_ADMIN']);
        $user->setActive(true);

        $manager->persist($user);

        $manager->flush();
    }

}
