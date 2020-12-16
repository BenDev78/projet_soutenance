<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/user", name="user")
 */
class UserController extends AbstractController
{
    /**
     * Inscription d'un utilisateur
     * @Route("/create", name="user_create", methods={"GET|POST"})
     * ex: http://localhost:8000/user/create
     */

    public function create()
    {
        # Création d un utilisateur
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->setcreatedAt(new \DateTime());

        #Formulaire d inscription d'un utilisateur
        $form = $this->createForm('App\Form\RegisterType');
    }
}
