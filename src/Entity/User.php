<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity()
 * @UniqueEntity(
 * fields={"username"},
 * errorPath="username",
 * message="It appears username already registered."
 *)
 */


class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $salt;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="users")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */

    private $role;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Club", inversedBy="users")
     * @ORM\JoinTable(name="user_club")
     */
    private $clubs;


    public function __construct()
    {
        $this->clubs = new ArrayCollection();
    }


    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username): void
    {
        $this->username = $username;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getRoles()
    {
        return array('ROLE_'.strtoupper($this->role->getTitle()));
    }

    public function setRole($role): void
    {
        $this->role = $role;
    }

    public function getClubs()
    {
        return $this->clubs;
    }

    public function addClub($club)
    {
        if ($this->clubs->contains($club)) {
            return;
        }
        $this->clubs[] = $club;
    }
    public function getSalt()
    {
        return $this->salt;
    }

    public function eraseCredentials(){}

}
