<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login_form")
     */
    public function loginAction()
    {
        $helper = $this->get('security.authentication_utils');
        return $this->render('AppBundle:Security:login.html.twig', array(
            // last username entered by the user (if any)
            'last_username' => $helper->getLastUsername(),
            // last authentication error (if any)
            'error' => $helper->getLastAuthenticationError(),
        ));
    }

    /**
     * @Route("/login/auto/{username}")
     */
    public function autoLogin($username, Request $request)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(['username' => $username]);
        if (!$user) {
            throw $this->createNotFoundException('No user!');
        }
        $firewallKey = 'secured_area';
        $authenticator = $this->container->get('app.form_login_authenticator');
        $guardHandler = $this->container->get('security.authentication.guard_handler');
        $successResponse = $guardHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $authenticator,
            $firewallKey
        );
        return $successResponse;
    }

    /**
     * This is the route the login form submits to.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the login automatically. See form_login in app/config/security.yml
     *
     * @Route("/login_check", name="security_login_check")
     */
    public function loginCheckAction()
    {
        throw new \Exception('This should never be reached!');
    }

    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in app/config/security.yml
     *
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
        throw new \Exception('This should never be reached!');
    }
}
