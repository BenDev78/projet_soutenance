<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="default_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
        ]);
    }

    # Faire modification de la route une fois qu'on aura les produits => "/product{id}/review" #

    /**
     * @Route("/review", name="product_review", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function review(Request $request): Response
    {
        $review = new Review();

        $form = $this->createForm(ReviewType::class, $review);
        $form -> handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($review);
            $this->em->flush();

            return $this->redirectToRoute("/");

        }

        return $this->render('formReviews.html.twig', ['form' => $form->createView()]);

    }

}

