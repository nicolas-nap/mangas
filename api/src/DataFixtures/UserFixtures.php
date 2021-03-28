<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Admin;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new Admin();
        $user->setFirstName('Admin');
        $user->setLastName('MangasCR');
        $user->setUserType('admin');
        $user->setEmail('admin@mangasCR.com');
        $user->plainPassword = 'demo';
        $manager->persist($user);

        $this->execute($manager);
    }

    private function execute(ObjectManager $manager): void
    {
        $manager->flush();
    }
}