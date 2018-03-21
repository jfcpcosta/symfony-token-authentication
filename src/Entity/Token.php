<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="tokens")
 */
class Token {
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=10, options={"unsigned"=true}, nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="token")
     * @ORM\JoinColumn(referencedColumnName="id", name="user")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastAccessAt;

    /**
     * Token constructor.
     * @param $user
     */
    public function __construct(User $user)
    {
        $this->token = $this->generateToken();
        $this->user = $user;
        $this->createdAt = new \DateTime();
        $this->lastAccessAt = $this->createdAt;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
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
     * @return \DateTime
     */
    public function getLastAccessAt()
    {
        return $this->lastAccessAt;
    }

    /**
     * @param \DateTime $lastAccessAt
     */
    public function setLastAccessAt($lastAccessAt)
    {
        $this->lastAccessAt = $lastAccessAt;
    }

    public function isValid() {
        $now = new \DateTime();
        $interval = $now->getTimestamp() - $this->getLastAccessAt()->getTimestamp();

        return $interval <= 14600;
    }

    private function generateToken() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}