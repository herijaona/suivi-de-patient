<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
       /* $user = new User();
        $user->setEmail('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $password = $this->encoder->encodePassword($user, 'admin');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();*/
    }
}
