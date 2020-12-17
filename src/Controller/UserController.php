<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
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

        if ($form->isSubmitted() && $form->isValid())
        {
            # Encodage du mot de passe
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
        }
        #TODO message de confirmation d inscription
        #TODO redirection lors de l inscription vers page profil

        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * Modification des données d'un utilisateur
     * @Route("/profil/edit", name="user_profil_edit", methods={"GET|POST"})
     * @param Request $request
     * @return Response
     */
    public function editProfil(Request $request)
    {
        # Récupération des données du user connecté
        $user = $this->getUser();

        # Récupération du formulaire
        $form = $this->createForm('App\Form\ProfilType')
            ->handleRequest($request);

        # Traitement du Formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }

        # Affichage dans la vue
        return $this->render("user/profil-edit.html.twig", [
            'form' => $form->createView()
        ]);
    }
}
