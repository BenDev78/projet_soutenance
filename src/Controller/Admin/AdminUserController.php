<?php


namespace App\Controller\Admin;


use App\Entity\Review;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * Class AdminUserController
 * @Route("/admin/utilisateur")
 * @package App\Controller\Admin
 */
class AdminUserController extends AbstractController
{
    /**
     * @Route("/supprimer/{id}", name="admin_user_delete", methods={"GET|POST"})
     * @param User $user
     * @return Response
     */
    public function delete(User $user): Response
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'L\'utilisateur à bien été supprimé');

        return $this->redirectToRoute('admin_users');
    }

    /**
     * @Route("/commentaires/{id}", name="admin_user_reviews", methods={"GET|POST"})
     * @param User $user
     * @return Response
     */
    public function reviews(User $user): Response
    {
        return $this->render('admin/reviews.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/commentaire/{id}", name="admin_user_review", methods={"GET|POST"})
     * @param Review $review
     * @return Response
     */
    public function review(Review $review): Response
    {
        return $this->render('admin/review.html.twig', [
            'review' => $review
        ]);
    }

}
