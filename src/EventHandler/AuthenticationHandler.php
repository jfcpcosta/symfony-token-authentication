<?php

namespace App\EventHandler;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use App\Entity\Token;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface {

    private $router;
    private $session;
    private $em;
    private $container;

    public function __construct(EntityManager $em, Session $session, RouterInterface $router, $container) {
        $this->em = $em;
        $this->session = $session;
        $this->router = $router;
        $this->container = $container;
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param TokenInterface $token
     *
     * @return Response never null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        $user->setLastLogin(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();

        $token = new Token($user);
        $this->em->persist($token);
        $this->em->flush();

        $response = new JsonResponse([
            'success' => [
                'user' => [
                    'id' => $user->getId(),
                    'first_name' => $user->getFirstName(),
                    'last_name' => $user->getLastName(),
                    'email' => $user->getEmail(),
                    'last_login' => $user->getLastLogin()->format('Y-m-d H:i:s'),
                    'roles' => $user->getRoles()
                ],
                'token' => $token->getToken()
            ]
        ]);
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response The response to return, never null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $response = new JsonResponse([
            'error' => [
                'code' => Response::HTTP_UNAUTHORIZED,
                'message' => $exception->getMessage()
            ]
        ], Response::HTTP_UNAUTHORIZED);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}