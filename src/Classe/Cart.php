<?php


namespace App\Classe;


use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{

    private $session;
    private $em;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->session = $session;
        $this->em = $em;
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function add($id, $quantity)
    {
        $cart = $this->session->get('cart', []);

        if(!empty($cart[$id])) {
            $cart[$id]+= $quantity;
        } else {
            $cart[$id] = $quantity;
        }

        $this->session->set('cart', $cart);
    }

    public function increase($id)
    {
        $cart = $this->session->get('cart', []);

        $cart[$id]++;

        $this->session->set('cart', $cart);
    }

    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);

        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }

        return $this->session->set('cart', $cart);
    }

    public function remove()
    {
        return $this->session->remove('cart');
    }

    public function delete($id)
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $this->session->set('cart', $cart);
    }

    public function getFull(): array
    {
        $cartComplete = [];

        if ($this->get()) {
            foreach ($this->get() as $id => $quantity) {
                $product_object = $this->em->getRepository(Product::class)->findOneById($id);

                if (!$product_object) {
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    'products' => $product_object,
                    'quantities' => $quantity
                ];
            }
        }

        return $cartComplete;
    }


}