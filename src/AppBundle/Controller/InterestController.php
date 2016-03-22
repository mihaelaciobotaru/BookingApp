<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use AppBundle\Entity\Interest;
use AppBundle\Form\InterestType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

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

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $valid = $form->isValid();
            if ($valid) {
                $interest->setLandlord($user);
                $em->persist($interest);
                $em->flush();
                return $this->redirect($this->generateUrl("interest_upload", array("id"=>$interest->getId())));
            } else {
                var_dump("error");
            }
        }
        return $this->render('AppBundle:Interest:edit.html.twig', array(
            "interest" => $interest,
            "title" => $title,
            "form" => $form->createView(),
        ));
    }

    public function uploadAction(Request $request, $id)
    {
        $form = $this->createFormBuilder()->getForm();
        return $this->render('AppBundle:Interest:upload.html.twig', array(
            "title" => "Upload images for the offer earlier setted",
            "form" => $form->createView()
        ));
    }

    public function doUploadAction(Request $request)
    {
        $file = new File();
        $file->setType(File::TYPE_INTEREST_PHOTO);
        $file->setCreated(new \DateTime("now"));

        /*$form = $this->createFormBuilder($file)
                     ->add('name')
                     ->add('file', FileType::class)
                     ->getForm();*/
        $form = $this->createFormBuilder()->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            //$form->handleRequest($request);
            //var_dump($_POST);exit;
            //if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($file);
                $em->flush();

                //return $this->redirectToRoute($this->generateUrl("interest_edit", array("id"=> -1)));
            //}
        }

        return $this->render('AppBundle:Interest:upload.html.twig', array(
            "title" => "Upload images for the offer earlier setted",
            'form' => $form->createView()

        ));
    }

}
