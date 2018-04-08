<?php

namespace App\DataFixtures\ORM;


use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRole extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $roles = ["Admin", "User"];
        foreach ($roles as $roleTitle) {
            $role = new Role();
            $role->setTitle($roleTitle);
            $this->addReference($roleTitle, $role);
            $manager->persist($role);
        }
        $manager->flush();
    }
}