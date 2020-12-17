<?php


namespace App\Controller\Admin;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminUserController
 * @Route("/admin/user")
 * @package App\Controller\Admin
 */
class AdminUserController extends AbstractController
{
    /**
     * @Route("/user/{id}", name="admin_user_delete", methods={"GET|POST"})
     * @param User $user
     * @return Response
     */
    public function delete(User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('admin_users');
    }
}
