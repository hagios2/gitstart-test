<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(protected UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
         $user = new User();
         $user->setName('Emmanuel Wilson');
         $user->setEmail('hagioswilson@gmail.com');
         $password = $this->hasher->hashPassword($user, 'password');
         $user->setPassword($password);
         $manager->persist($user);

        $manager->flush();
    }
}
