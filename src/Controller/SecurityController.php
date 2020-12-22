<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
//         if ($this->getUser()) {
//             return $this->redirectToRoute('');
//         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
        #TODO ajouter un message de confirmation de connexion
    }
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): Response
    {
        return $this->render('default/index.html.twig');
    }
    /**
     * @Route("/delete_user/{id}", name="delete_user")
     * @param $id
     * @return Response
     */
    public function deleteUser($id): Response
    {
        $currentUserId = $this->getUser()->getId();
        if ($currentUserId == $id) {
            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();

            $em = $this->getDoctrine()->getManager();
            $usrRepo = $em->getRepository(User::class);

            $user = $usrRepo->find($id);
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'Votre compte utilisateur a bien été supprimé !');

        }
        return $this->redirectToRoute('app_login');
    }
}
