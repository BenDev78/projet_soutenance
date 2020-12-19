<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        $products = $this->em->getRepository(Product::class)->findByIsBest(1);

        return $this->render('default/index.html.twig', [
            'products' => $products
        ]);
    }

}

