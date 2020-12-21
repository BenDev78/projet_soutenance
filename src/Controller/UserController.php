<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Form\RegisterType;
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
        $form = $this->createForm(RegisterType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # Encodage du mot de passe
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            #TODO message de confirmation d inscription
            $this->addFlash('success', 'Votre compte a bien été créé ! Connectez-vous maintenant !');

            #enregistrement en BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            #redirection lors de l inscription vers page connexion
            return $this->redirectToRoute('app_login');
        }

        # vue du formulaire d inscription
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
    public function editProfil(Request $request): Response
    {
        # Récupération des données du user connecté
        $user = $this->getUser();
        # Récupération du formulaire
        $form = $this->createForm(ProfileType::class , $user)->handleRequest($request);

        # Traitement du Formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            #TODO message de confirmation

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            #redirection lors de l inscription vers page acceuil
            return $this->redirectToRoute('default_index');
        }
        # Affichage dans la vue
        return $this->render("user/profil-edit.html.twig", [
            'form' => $form->createView()
        ]);
    }
}
