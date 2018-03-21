<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements AdvancedUserInterface {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=10, options={"unsigned"=true}, nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     */
    private $salt;

    /**
     * @ORM\Column(type="string")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string")
     */
    private $lastName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @ORM\Column(type="boolean", name="active")
     */
    private $active;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role")
     * @ORM\JoinTable(name="user_roles",
     *     joinColumns={@ORM\JoinColumn(name="user", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="id")}
     * )
     */
    private $roles;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id", name="created_by")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $createdFrom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id", name="updated_by")
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $updatedFrom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Token", mappedBy="user")
     */
    private $token;

    /**
     * User constructor.
     */
    public function __construct($email, $password = null, $roles = [])
    {
        $this->email = $email;
        $this->password = $password;
        $this->roles = new ArrayCollection($roles);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getFullName() {
        return sprintf("%s %s", $this->getFirstName(), $this->getLastName());
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @param \DateTime $lastLogin
     */
    public function setLastLogin(\DateTime $lastLogin)
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy(User $createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return string
     */
    public function getCreatedFrom()
    {
        return $this->createdFrom;
    }

    /**
     * @param string $createdFrom
     */
    public function setCreatedFrom($createdFrom)
    {
        $this->createdFrom = $createdFrom;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param User $updatedBy
     */
    public function setUpdatedBy(User $updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }

    /**
     * @return string
     */
    public function getUpdatedFrom()
    {
        return $this->updatedFrom;
    }

    /**
     * @param string $updatedFrom
     */
    public function setUpdatedFrom($updatedFrom)
    {
        $this->updatedFrom = $updatedFrom;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive();
    }

    public function getRoles()
    {
        return array_map(function(Role $role) {
            return $role->getRole();
        }, $this->roles->toArray());
    }

    public function addRole(Role $role) {
        $this->roles->add($role);
    }

    public function removeRole(Role $role) {
        $this->roles->removeElement($role);
    }

    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function eraseCredentials()
    {
        /*$this->password = "";
        $this->salt = "";*/
    }
}