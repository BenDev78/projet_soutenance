<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Carrier;
use App\Entity\Command;
use App\Entity\Detail;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/command/new", name="new_command")
     * @param Cart $cart
     * @return Response
     * @throws ApiErrorException
     */
    public function new(Cart $cart): Response
    {
        $products = $cart->getFull();

        $carriers = $this->entityManager->getRepository(Carrier::class)->findAll();

        // Enregistrer une commande
        $command = new Command();
        $command->setCreatedAt(new \DateTime())
            ->setUser($this->getUser())
            ->setCarrier($carriers[0]);
       $this->entityManager->persist($command);
//        $this->entityManager->flush();


        //Enregistrer les details des produits de la commande

        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        for($i = 0; $i < count($products); $i++) {
            $detail = new Detail();
            $detail->setProduct($products[$i]['products'])
                ->setCommand($command)
                ->setQuantity($products[$i]['quantities']);
            $this->entityManager->persist($detail);
//            $this->entityManager->flush();

            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $products[$i]['products']->getPrice(),
                    'product_data' => [
                        'name' => $products[$i]['products']->getName(),
                        'images' => [$YOUR_DOMAIN."/uploads/images/".$products[$i]['products']->getImage()],
                    ],
                ],
                'quantity' => $products[$i]['quantities'],
            ];
        }

        //Dump du panier
        $cart->remove();

//        // Paiement Stripe
//        Stripe::setApiKey('sk_test_51I0tD5Lp500DOCJ4yBRm8uuAwAAHHBMcCmUpgXmXG9GpogFBmIgPd9HY0xviunD02AwnOfODtXAB2HC5sOELbUHp00DWLglKk7');
//
//        $checkout_session = Session::create([
//            'payment_method_types' => ['card'],
//            'line_items' => [
//                $products_for_stripe
//            ],
//            'mode' => 'payment',
//            'success_url' => $YOUR_DOMAIN . '/success.html',
//            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
//        ]);


        return $this->render('command/index.html.twig', [

        ]);
    }
}