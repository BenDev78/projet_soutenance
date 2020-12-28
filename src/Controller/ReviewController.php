<?php


namespace App\Controller;


use App\Classe\Mail;
use App\Entity\Product;
use App\Entity\Report;
use App\Entity\Review;
use App\Entity\User;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    # Faire modification de la route une fois qu'on aura les produits => "/product{id}/review" #

    /**
     * Création du formulaire d'avis produit
     * @IsGranted("ROLE_USER")
     * @Route("/review/{id}", name="product_review", methods={"GET|POST"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function review(Request $request, Product $product): Response
    {
        #Remplacer "$user = $this->>getDoctrine etc" par ligne ci-dessous lorsque les logins seront fonctionnels
        $review = new Review();
        $review->setProduct($product);
        $review->setUser($this->getUser());
        $review->setCreatedAt(new \DateTime());


        $form = $this->createForm(ReviewType::class, $review);
        $form -> handleRequest($request);


        if($form->isSubmitted() && $form->isValid())
        {
//            dd($review);
            if(is_null($review->getPseudo()))
            {
                $review->setPseudo("Un utilisateur");
            }

            $this->em->persist($review);
            $this->em->flush();

            $this->addFlash('success', 'Votre avis a bien été envoyé, merci!');

            return $this->redirectToRoute("shop_product", [
                'id' => $product->getId(),
                'slug' => $product->getSlug()
            ]);

        }

        return $this->render('review/formReviews.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);

    }

    #TODO
    #Nullable=true ne fonctionne pas pour le pseudo
    #faire page allProductReview avec Kpn paginator pour permettre de selectionner le nombre de commentaires à afficher/page

    /**
     * @IsGranted("ROLE_USER")
     * @Route("product/{id}/reviews", name="all_product_reviews", methods={"GET|POST"})
     * @param Product $product
     * @return Response
     */
    public function show_all_product_reviews(Product $product): Response
    {
        return $this->render("review/allProductReviews.html.twig", ['product' => $product]);
    }

    /**
     * @Route("/report/{id}")
     * @param Review $review
     * @return JsonResponse
     */
    public function report(Review $review): JsonResponse
    {
        $user = $review->getUser();

        # check if a user has already report a review
        $is_already_reported = $this->em->getRepository(Report::class)->searchUser($user, $review);

        if($is_already_reported)
        {
            return $this->json(['success' => false]);
        }

        $report = new Report();
        $report->setUser($review->getUser())
            ->setReview($review);

        $this->em->persist($report);
        $this->em->flush();

        if($this->em->getRepository(Report::class)->countReports($review) >= 1)
        {
            $mail = new Mail();
            $mail->send(
                'leson.benjamin78@gmail.com',
                'Leson-Larivée',
                'Signalement d\'un commentaire',
                "Bonjour Benjamin,"."<br><br> Le commentaire <strong>".$review->getId()." du produit '".$review->getProduct()->getName()."'</strong> écrit par l'utilisateur".$user->getId()." a été signalé par plusieurs utilisateurs, merci de bien vouloir faire le nécéssaire.<br><br>L'équipe Cognac Guy Bonnaud"
            );
        }

        return $this->json(['success' => true]);
    }
}

