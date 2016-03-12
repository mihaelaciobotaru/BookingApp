<?php
/**
 * Created by PhpStorm.
 * User: mihaela
 * Date: 05/03/16
 * Time: 15:11
 */

namespace AppBundle\Lib;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\DependencyInjection\ContainerAware;

use Symfony\Component\Form\CallbackTransformer;

use AppBundle\Entity\User;

class Helpers extends ContainerAware
{
    /* Get current URI */
    public static function getUri()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            return $_SERVER['REQUEST_URI'];
        } else {
            return  "/";
        }
    }
}