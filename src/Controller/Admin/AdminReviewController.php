<?php


namespace App\Controller\Admin;


use App\Classe\Mail;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminReviewController
 * @Route("admin/review")
 * @package App\Controller\Admin
 */
class AdminReviewController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/delete/{id}", name="admin_review_delete", methods={"GET|POST"})
     * @param Review $review
     * @return Response
     */
    public function delete(Review $review): Response
    {
        $user = $review->getUser();
        $userEmail = $user->getEmail();
        $this->em->remove($review);
        $this->em->flush();

        $this->addFlash('success', 'Le commentaire de l\'utilisateur '.$user->getFirstname().' '.$user->getLastname().' a bien été supprimé');

        /*$email = (new Email())
            ->from('leson.benjamin78@gmail.com')
            ->to('johnnypierre@icloud.com')
            ->subject('Suppression de commentaire')
            ->html('
            <p>
            Bonjour,

            Suite à de nombreux signalement et après analyuse de nos équipes, votre commentaire a été supprimé parqu\'il ne respecte pas notre charte d\'utilisateurs.

            Vous pouvez toujours non contacter <a href="https://localhost:8000/contact">ici</a> si vous jugez cette décision injuste.

            Bien cordialement,

            l\'Administration
            </p>
            ')
        ;

        $mailer->send($email);
        */
        return $this->redirectToRoute("admin_user_reviews", [
            'id' => $user->getId()
        ]);
    }

}
