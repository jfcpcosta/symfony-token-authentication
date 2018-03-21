<?php
namespace App\Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Token;

class ApiKeyUserProvider implements UserProviderInterface {

    private $em;

    /**
     * ApiKeyUserProvider constructor.
     * @param $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }


    public function getUsernameForApiKey($apiKey) {

        // Look up the username based on the token in the database, via
        // an API call, or do something entirely different
        $token = $this->em->getRepository(Token::class)->findOneBy(['token' => $apiKey]);

        if(!$token) {
            return null;
        }

        if (!$token->isValid()) {
            $this->em->remove($token);
            $this->em->flush();

            throw new UnauthorizedHttpException("token timeout");
        }

        $token->setLastAccessAt(new \DateTime());
        $this->em->persist($token);
        $this->em->flush();

        $username = $token->getUser()->getEmail();

        return $username;
    }

    public function loadUserByUsername($username) {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $username]);

        if (!$user->isActive()) {
            throw new UnauthorizedHttpException("user unauthorized");
        }

        return $user;
    }

    public function refreshUser(UserInterface $user) {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    public function supportsClass($class) {
        return User::class === $class;
    }

}