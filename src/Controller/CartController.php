<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Carrier;
use App\Entity\Command;
use App\Entity\Detail;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/cart", name="cart")
     * @param Cart $cart
     * @return Response
     */
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getFull()
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     * @param Cart $cart
     * @param Product $product
     * @return Response
     */
    public function add(Cart $cart, Product $product): Response
    {
        $cart->add($product->getId());

        $this->addFlash('success', 'Votre produit a bien été ajouté au panier.');

        return $this->redirectToRoute('shop_product', [
            'id' => $product->getId(),
            'slug' => $product->getSlug()
        ]);
    }

    /**
     * @Route("/cart/increase/{id}", name="increase_cart")
     * @param Cart $cart
     * @param $id
     * @return Response
     */
    public function increase(Cart $cart, $id): Response
    {
        $cart->add($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/decrease/{id}", name="decrease_cart")
     * @param Cart $cart
     * @param $id
     * @return Response
     */
    public function decrease(Cart $cart, $id): Response
    {
        $cart->decrease($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/delete/{id}", name="delete_to_cart")
     * @param Cart $cart
     * @param $id
     * @return Response
     */
    public function delete(Cart $cart, $id): Response
    {
        $cart->delete($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/remove", name="remove_cart")
     * @param Cart $cart
     * @return Response
     */
    public function remove(Cart $cart): Response
    {
        $cart->remove();

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/newCommand", name="cart_newCommand")
     * @param Cart $cart
     * @return Response
     */
    public function newCommand(Cart $cart): Response
    {
        $products = $cart->getFull();

        $carriers = $this->entityManager->getRepository(Carrier::class)->findAll();

        $command = new Command();
        $command->setCreatedAt(new \DateTime())
            ->setUser($this->getUser())
            ->setCarrier($carriers[0])
        ;

        $this->entityManager->persist($command);
        $this->entityManager->flush();

        for($i = 0; $i < count($products); $i++) {
            $detail = new Detail();
            $detail->setProduct($products[$i]['products'])
            ->setCommand($command)
            ->setQuantity($products[$i]['quantities']);

            $this->entityManager->persist($detail);
            $this->entityManager->flush();

        }

        $cart->remove();


//        dd($command->getDetails());
        // new Command()

        /* foreach(item in cart)
            {
                  new detail();
                  $detail->set(product)
                        ->(quantity)
                        ->setCommand($command)
                  $command->addDetail()
            }

       $this->em->persist($command)
        $this->em->flush()
         */

    }
}
