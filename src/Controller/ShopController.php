<?php

namespace App\Controller;


use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="default_shop", methods={"GET|POST"})
     * @return Response
     */
    public function index(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render('shop/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/shop/product", name="shop_product", methods={"GET|POST"})
     * @return Response
     */
    public function product(): Response
    {
        return $this->render("shop/product.html.twig");
    }

}
