<?php


namespace App\Controller;


use App\Classe\Mail;
use App\Entity\Product;
use App\Entity\Report;
use App\Entity\Review;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
     * @Route("/commentaire/{id}", name="form_review", methods={"GET|POST"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function review(Request $request, Product $product): Response
    {
        #Remplacer "$user = $this->>getDoctrine etc" par ligne ci-dessous lorsque les logins seront fonctionnels
        $reviews = $product->getReviews();

        # If the user already reviewed a product, we redirect him to the product page
        foreach ($reviews as $review)
        {
            if($review->getUser() == $this->getUser())
            {
                $this->addFlash('warning', 'Vous avez déjà laissé votre avis sur ce produit.');

                return $this->redirectToRoute('shop_product', [
                    'id' => $product->getId(),
                    'slug' => $product->getSlug()
                ]);
            }
        }

        $review = new Review();
        $review->setProduct($product);
        $review->setUser($this->getUser());
        $review->setCreatedAt(new \DateTime());


        $form = $this->createForm(ReviewType::class, $review);
        $form -> handleRequest($request);


        if($form->isSubmitted() && $form->isValid())
        {

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
     * @Route("produit/{id}/commentaires", name="product_reviews", methods={"GET|POST"})
     * @param Product $product
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function product_reviews(Product $product, Request $request, PaginatorInterface $paginator): Response
    {
        $reviews = $this->getDoctrine()->getRepository(Review::class)->findByProduct($product);

        $reviews = $paginator->paginate(
            $reviews, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), 2
        );

        return $this->render("review/productReviews.html.twig", [
            'reviews' => $reviews,
            'product' => $product
        ]);
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

        if($this->em->getRepository(Report::class)->countReports($review) >= 25)
        {
            $mail = new Mail();
            $mail->send(
                'leson.benjamin78@gmail.com',
                'Leson-Larivée',
                'Signalement d\'un commentaire',
                "Bonjour Benjamin,"."<br><br> Le commentaire n°<strong>".$review->getId()." du produit '".$review->getProduct()->getName()."'</strong> écrit par l'utilisateur".$user->getId()." a été signalé par plusieurs utilisateurs, merci de bien vouloir faire le nécéssaire.<br><br>L'équipe Cognac Guy Bonnaud"
            );
        }

        return $this->json(['success' => true]);
    }
}

