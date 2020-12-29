<?php

namespace App\Controller;


use App\Classe\Cart;
use App\Data\SearchData;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Review;
use App\Form\QuantitySelectorType;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em= $em;
    }


    /**
     * @Route("/boutique", name="shop_index", methods={"GET|POST"})
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
     * @Route("/boutique/{alias}", name="shop_category", methods={"GET|POST"})
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
     * @Route("/boutique/produit/{slug}_{id}", name="shop_product", methods={"GET|POST"})
     * @param Product $product
     * @param Request $request
     * @param Cart $cart
     * @return Response
     */
    public function product(Product $product, Request $request, Cart $cart): Response
    {
        $reviews = $product->getReviews();

        $count = $this->em->getRepository(Product::class)->countReviews($product->getId());

        $sumReviews = null;

        for($i = 0;$i < count($reviews); $i ++)
        {
            $sumReviews += $reviews[$i]->getRating();
        }

        $avg = null;

        if($sumReviews > 0)
        {
            $avg = round($sumReviews / $count[0], 1);
        }

        $form = $this->createForm(QuantitySelectorType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $cart->add($product->getId(), $data['quantity']);

            $this->addFlash('success', 'Votre produit a bien été ajouté au panier.');

            return $this->redirectToRoute('shop_product', [
                'id' => $product->getId(),
                'slug' => $product->getSlug()
            ]);
        }

        return $this->render("shop/product.html.twig", [
            'product' => $product,
            'avg' => $avg,
            'count' => $count,
            'form' => $form->createView()
        ]);
    }

    /**
     * Compte le nombre d'entrées d'une entité
     * @param $entity
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function rowCount($entity)
    {
        $data = $this->em->getRepository($entity);

        return $data->createQueryBuilder('d')
            ->select('count(d.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

}
