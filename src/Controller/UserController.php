<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * Inscription d'un utilisateur
     * @Route("/create", name="user_create", methods={"GET|POST"})
     * ex: http://localhost:8000/user/create
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */

    public function create(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        # Création d un utilisateur
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->setcreatedAt(new \DateTime());

        #Formulaire d inscription d'un utilisateur
        $form = $this->createForm('App\Form\RegisterType')->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            # Encodage du mot de passe
            $user->setPassword(
                $encoder->encodePassword(
                    $user,
                    $user->getPassword()
                )
            );
        }
        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
