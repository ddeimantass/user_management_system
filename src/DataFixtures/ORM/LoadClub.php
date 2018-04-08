<?php

namespace App\DataFixtures\ORM;

use App\Entity\Club;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadClub extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i <= 10; $i++) {
            $club = new Club;
            $club->setTitle("Club" . $i);
            $this->addReference($i, $club);
            $manager->persist($club);
        }
        $manager->flush();
    }
}