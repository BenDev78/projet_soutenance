<?php

namespace App\Controller;

//use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="default_shop", methods={"GET|POST"})
//     * @param Product $product
     * @return Response
     */
    public function index(/*Product $product*/): Response
    {
        return $this->render('shop/index.html.twig', [
//            'product' => $product,
        ]);
    }
}
