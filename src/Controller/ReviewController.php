<?php


namespace App\Controller;


use App\Entity\Product;
use App\Entity\Review;
use App\Entity\User;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     *
     * @return Response
     */
    public function add_product_name(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render("review/formReview.html.twig", ['products'=> $products]);
    }


    /**
     * @Route("/allProductReviews/{id}", name="all_product_reviews", methods={"GET|POST"})
     * @param Product $product
     * @return Response
     */
    public function show_all_product_reviews(Product $product): Response
    {
        return $this->render("review/allProductReviews.html.twig", ['product' => $product]);
    }

}

