<?php

namespace App\DataFixtures\ORM;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class LoadUser extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i <= 10; $i++) {
            $user = new User;
            $encoder = $this->container->get("security.password_encoder");
            if($i == 0){
                $user->setName("Admin");
                $user->setUsername("UserNameAdmin");
                $password = $encoder->encodePassword($user, "PassAdmin");
                $user->setPassword($password);
                $user->setRole($this->getReference("Admin"));
            }
            else{
                $user->setName("Name" . $i);
                $user->setUsername("UserName" . $i);
                $user->setPassword(md5("Pass". $i));
                $password = $encoder->encodePassword($user, "Pass".$i);
                $user->setPassword($password);
                $user->setRole($this->getReference("User"));
                $user->addClub($this->getReference(rand(2, 5)));
                $user->addClub($this->getReference(rand(5, 9)));
            }

            $manager->persist($user);

        }

        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null){
        $this->container = $container;
    }

    public function getDependencies()
    {
        return array(
            LoadRole::class,
            LoadClub::class,
        );
    }
}