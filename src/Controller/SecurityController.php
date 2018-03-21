<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Token;

class SecurityController extends Controller
{
    /**
     * @Route("/api/auth/login", name="auth_security_login")
     * @Method("POST")
     */
    public function login(Request $request)
    {
        return "login";
    }

    /**
     * @Route("/api/auth/logout", name="auth_security_logout")
     */
    public function logout(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $tokenHeader = $request->headers->get('Authorization');
        $tokenParts = explode(' ', $tokenHeader);
        $tokenString = $tokenParts[0] == 'Bearer' ? $tokenParts[1] : null;

        $token = $em->getRepository(Token::class)->findOneBy(['token' => $tokenString]);
        if ($token) {
            $em->remove($token);
            $em->flush();
        }

        return [
            'success' => [
                'message' => sprintf("%s logged out", $this->getUser()->getEmail())
            ]
        ];
    }

    /**
     * @Route("/api/auth/me", name="auth_security_me")
     * @Method("GET")
     */
    public function me(Request $request)
    {
        $user = $this->getUser();
        return $user;
    }
}