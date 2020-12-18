<?php

namespace App\Controller;


use App\Data\SearchData;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Review;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tightenco\Collect\Support\Collection;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop_index", methods={"GET|POST"})
     * @param Request $request
     * @param ProductRepository $repository
     * @return Response
     */
    public function index(Request $request, ProductRepository $repository): Response
    {
        $data = new SearchData();

        $form = $this->createForm(SearchType::class, $data)->handleRequest($request);
        $products = $repository->findSearch($data);

//        if (!empty($data)) {
//            $products = new Collection($repository->findSearch($data));
//            $filtered = $products->filter(function ($value, $key) use ($data) {
//                return(
//                    $value['rate'] < $data->rating + 1 && $value['rate'] > $data->rating - 1
//                );
//            });
//            $products = $filtered->all();
//        }

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('shop/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/shop/{alias}", name="shop_category", methods={"GET|POST"})
     * @param Category $category
     * @param Request $request
     * @return Response
     */
    public function category(Category $category, Request $request): Response
    {
        $data = new SearchData();
        $form = $this->createForm(SearchType::class, $data)->handleRequest($request);
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('shop/category.html.twig', [
            'category' => $category,
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/shop/product", name="shop_product", methods={"GET|POST"})
     * @return Response
     */
    public function product(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $reviews = $this->getDoctrine()->getRepository(Review::class)->findAll();

        return $this->render("shop/product.html.twig", [
            'products' => $products,
            'reviews' => $reviews
        ]);
    }

}
