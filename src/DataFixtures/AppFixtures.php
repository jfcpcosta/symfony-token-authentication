<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture implements ContainerAwareInterface {
    use ContainerAwareTrait;

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager) {
        $roleUser = new Role('User', 'ROLE_USER');
        $roleAdmin = new Role('Admin', 'ROLE_ADMIN');
        
        $manager->persist($roleUser);
        $manager->persist($roleAdmin);

        $manager->flush();
        
        $user = new User('john@email.com');
        $user->setFirstName("John");
        $user->setLastName("Doe");
        $user->setCreatedAt(new \DateTime());
        $user->setActive(true);
        $user->setSalt(md5(uniqid()));
        $user->setPassword($this->encoder->encodePassword($user, '12345'));
        $user->setCreatedFrom("127.0.0.1");
        
        $user->addRole($roleAdmin);

        $manager->persist($user);
        $manager->flush();
    }
}