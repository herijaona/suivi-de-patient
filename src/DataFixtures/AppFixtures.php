<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $passwordEncoder;
    function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setFirstName('admin');
        $user->setLastName('admin');
        $user->setEmail('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
               'admin'
            )
        );
        $user->setEtat(1);
        $manager->persist($user);
        $manager->flush();
    }
}
