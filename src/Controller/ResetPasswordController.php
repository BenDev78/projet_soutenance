<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/mot-de-passe-oublie", name="resetPassword_reset")
     * @param Request $request
     * @return Response
     */
    public function reset(Request $request): Response
    {
        if($this->getUser())
        {
            return $this->redirectToRoute('default_index');
        }

        if($request->get('email'))
        {
            $user = $this->em->getRepository(User::class)->findOneByEmail($request->get('email'));

            if($user)
            {
                # Enregistrer la demande de reset de l'utilisateuyr en bdd
                $reset_password = new ResetPassword();
                $reset_password->setUser($user)
                    ->setToken(uniqid())
                    ->setCreatedAt(new \DateTime())
                ;

                $this->em->persist($reset_password);
                $this->em->flush();

                # Envoie de l'email avec le token de confirmation lui permettant de modifier son mot de passe
                $url = $this->generateUrl('resetPassword_update', [
                    'token' => $reset_password->getToken()
                ]);

                $content = "Bonjour ".$user->getFirstName()."<br>Vous avez demandé à rénitialiser votre mot de passe sur le site Cognac Guy Bonnaud.<br><br>.";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='https://projetsoutenance.herokuapp.com".$url."'>mettre à jour votre mot de passe</a>";

                $mail = new Mail();
                $mail->send(
                    $user->getEmail(), $user->getFirstName().' '.$user->getLastName(),'Réinitialisation de mot de passe', $content
                );

                $this->addFlash('info', 'Vous aller recevoir un email pour réinitialiser votre mot de passe.');
            } else
            {
                $this->addFlash('warning', 'Cette adresse email est inconnue.');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="resetPassword_update")
     * @param $token
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse
     */
    public function update($token, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $reset_password = $this->em->getRepository(ResetPassword::class)->findOneByToken($token);

        if(!$reset_password)
        {
            return $this->redirectToRoute('resetPassword_reset');
        }

        # Vérifier si le createdAt = now - 3h
        $now = new \DateTime();
        if($now > $reset_password->getCreatedAt()->modify('+ 3 hour'))
        {
            $this->addFlash('warning', 'Votre demande de mot de passe a expirée. Merci de la renouveller.');

            return $this->redirectToRoute('resetPassword_reset');
        }

        $form = $this->createForm(ResetPasswordType::class)->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $reset_password->getUser();
            $new_password = $form->get('password')->getData();
            $password = $encoder->encodePassword($user, $new_password);

            $user->setPassword($password);
            $this->em->flush();

            $this->addFlash('success', 'Votre mot de passe a bien été modifié');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
