<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\File;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    public function uploadAction(Request $request, $what)
    {
        if (!$this->isGranted('ROLE_USER')) {
            return new Response(json_encode(array("error"=>true, "message"=>"You must be logged in to access this functionality.")), 200, array('Content-Type'=>'application/json'));
        } elseif (!isset($_FILES['uploadfile'])) {
            return new Response(json_encode(array("error"=>true, "message"=>"Error. Please contact an administrator.")), 200, array('Content-Type'=>'application/json'));
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $entity = null; //If this is an entity make sure we save data to it
        $file   = $_FILES['uploadfile'];

        $em     = $this->getDoctrine()->getManager();

        $fileName = null;

        if (strstr($what, "interest_")) {
            $what = explode("_", $what);
            $interest_id = $what[1];

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, array("jpg", "jpeg", "png"))) {
                $what = "interest_picture";
            }
        }

        switch ($what){
            case "interest_picture":
                $allowedExt = array("jpg", "jpeg", "png");
                $messageInvalidExtension = "Picture couldn't be loaded.<br />Please insure that the photo is of type  ".implode(", ", $allowedExt)."!";
                $entity = new File();
                $entity->setType(File::TYPE_INTEREST_PHOTO);
                $entity->setName($file['name']);
                $entity->setCreated(new \DateTime("now"));
                break;
            default:
                $fileName = uniqid(time(), false).'.';
                return new Response(json_encode(array("error"=>true, "message"=>"Nu se poate uploada acest fisier")), 200, array('Content-Type'=>'application/json'));
        }

        $id = -1;
        if ($entity!=null){ //This is an entity
            $em->persist($entity);
            $em->flush();
            $uploadDir = $entity->getFolderName();
            if ($fileName==null){
                $fileName = $entity->getId();
            }
            $id = $entity->getId();
        }
        $uploadRoot = __DIR__.'/../../../../web/bundles/app/uploads/';
        $uploadDir  = $uploadRoot.$uploadDir;
        var_dump($uploadDir);
        if (!is_dir($uploadDir)) {
            var_dump(is_dir($uploadDir));
            mkdir($uploadDir); chmod($uploadDir, 0777);
        } //We make sure the directory exists

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);

        if (!in_array($ext, $allowedExt)) {
            return new Response(json_encode(array("error"=>true, "message"=>$messageInvalidExtension)), 200, array('Content-Type'=>'application/json'));
        } else {
            $fileName .= ".".$ext;
            $destinationfile = $uploadDir."/".$fileName;
            if (file_exists($destinationfile)) {
                unlink($destinationfile);
            }//We delete the upload if it already exist

            if (move_uploaded_file($file['tmp_name'], $destinationfile)) {
                return new Response(json_encode(array("error"=>false, "original"=>$file['name'], "id"=>$id, "name"=>$fileName, "message"=>$file['name']." was uploaded successfully.")), 200, array('Content-Type'=>'application/json'));
            } else {
                return new Response(json_encode(array("error"=>true, "message"=>"Upload failed.<br />Please check your file.")), 200, array('Content-Type'=>'application/json'));
            }
        }
    }

    public function uploadReverseAction(Request $request, $what) {

        $em = $this->getDoctrine()->getManager();

        $what = explode("_", $what);
        $interest_id = $what[1];

        $interest = $em->getRepository("AppBundle:Interest")->find($interest_id);
        if ($interest != null && $interest->getImages() != null) {
            $images = $interest->getImages();
            if ($images[0] == ",") {
                $images = substr($images,1);
            }
            $images = explode(",", $images);
            $uploadRoot = $this->get('kernel')->getRootDir() . '/../web';
            $result = array();
            foreach ($images as $img) {
                $file = $em->getRepository("AppBundle:File")->find($img);
                $obj["name"] = $file->getPath();
                $obj["size"] = filesize($uploadRoot.$file->getPath());
                $obj["fullName"] = $file->getName();
                $ext = explode(".", $file->getName())[1];
                switch ($ext) {
                    case "jpg":
                    case "jpeg":
                        $ext = "image/jpeg";
                        break;
                    case "png":
                        $ext = "image/png";
                        break;
                }
                $obj["ext"] = $ext;
                $result[] = $obj;
            }

            return new Response(json_encode($result), 200, array('Content-Type'=>'application/json'));
        }
        return new Response();
    }
}
