<?php


namespace App\Controller\Admin;


use App\Classe\Mail;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminReviewController
 * @Route("admin/commentaire")
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
     * @Route("/supprimer/{id}", name="admin_review_delete", methods={"GET|POST"})
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

        $mail = new Mail();
        $mail->send(
            $user,
            $userEmail,
            'Suppression de commentaire',
            "Bonjour ".$user->getFirstname().' '.$user->getLastname().",<br><br> Suite à de nombreux signalements de votre commentaire sur notre produit <strong>".$review->getProduct()->getName()."</strong>, un admistrateur à juger bon de le supprimer car il ne respecte pas notre charte d'utilisateur.<br><br> Vous pouvez toujours nous contacter <a href='https://localhost:8000/contact'>ici</a> si vous trouvez notre décision injuste.<br><br> Nous vous remercion de votre compréhension, <br><br> l'équipe Cognac Guy Bonnaud"
        );

        return $this->redirectToRoute("admin_user_reviews", [
            'id' => $user->getId()
        ]);
    }

}
