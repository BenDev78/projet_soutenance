<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use App\Form\QuantitySelectorType;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @Route("/panier", name="cart")
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
     * @Route("/panier/augmenter/{id}", name="increase_cart")
     * @param Cart $cart
     * @param $id
     * @return Response
     */
    public function increase(Cart $cart, $id): Response
    {
        $cart->add($id, 1);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/panier/diminuer/{id}", name="decrease_cart")
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
     * @Route("/panier/supprimer/{id}", name="delete_to_cart")
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
     * @Route("/panier/retirer", name="remove_cart")
     * @param Cart $cart
     * @return Response
     */
    public function remove(Cart $cart): Response
    {
        $cart->remove();

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/nouvelle-commande", name="cart_newCommand")
     * @param Cart $cart
     * @return Response
     */

}
