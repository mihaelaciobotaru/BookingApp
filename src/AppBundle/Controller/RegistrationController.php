<?php
/**
 * Created by PhpStorm.
 * User: mihaela
 * Date: 06/03/16
 * Time: 02:39
 */

namespace AppBundle\Controller;

use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
    public function registerAction(Request $request)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 3) Encode the password (you could also do this via Doctrine listener)
            $passwordEncoder = $this->container->get('security.password_encoder');

            $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encodedPassword);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('security_login_form');
        }

        return $this->render(
            'AppBundle:Registration:register.html.twig',
            array('form' => $form->createView())
        );
    }
}