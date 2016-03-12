<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use AppBundle\Entity\Interest;
use AppBundle\Form\InterestType;
use Symfony\Component\HttpFoundation\Request;

class InterestController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBundle:Interest:index.html.twig', array(
            // ...
        ));
    }

    public function editAction(Request $request, $id = -1)
    {
        $sc = $this->get('security.authorization_checker');
        if (!$sc->isGranted("ROLE_LANDLORD")) {
            throw new AccessDeniedException();
        }

        $em = $this->get('doctrine')->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $interest = $em->getRepository('AppBundle:Interest')->find($id);
        if ($interest != null) {
            //edit interest
            $title = "Edit offer";
        } else {
            //add interest
            $title = "Add offer";
            $interest = new Interest();

        }
        $form = $this->createForm(InterestType::Class, $interest);

        return $this->render('AppBundle:Interest:edit.html.twig', array(
            "interest" => $interest,
            "title" => $title,
            "form" => $form->createView(),
        ));
    }

}
