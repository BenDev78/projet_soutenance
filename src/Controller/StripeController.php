<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Carrier;
use App\Entity\Command;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/command/create_session/{reference}", name="stripe_create_session")
     * @param EntityManagerInterface $entityManager
     * @param Cart $cart
     * @param $reference
     * @return Response
     * @throws ApiErrorException
     */
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference): Response
    {
        $product_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $command = $entityManager->getRepository(Command::class)->findOneByReference($reference);

        if (!$command){
            new JsonResponse(['error' => 'command']);
        }

        foreach ($command->getDetails() as $detail) {
            $product_object = $entityManager->getRepository(Product::class)->findOneById($detail->getProduct());
            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $detail->getProduct()->getPrice(),
                    'product_data' => [
                        'name' => $detail->getProduct()->getName(),
                        'images' => [$YOUR_DOMAIN."/uploads/images/".$product_object->getImage()],
                    ],
                ],
                'quantity' => $detail->getQuantity(),
            ];
        }

        $carrier_object = $entityManager->getRepository(Carrier::class)->findOneById($command->getCarrier());
        $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $command->getCarrier()->getPrice(),
                'product_data' => [
                    'name' => $command->getCarrier()->getName(),
                    'images' => [$YOUR_DOMAIN."/uploads/images/".$carrier_object->getImage()],
                ],
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey('sk_test_51I0tD5Lp500DOCJ4yBRm8uuAwAAHHBMcCmUpgXmXG9GpogFBmIgPd9HY0xviunD02AwnOfODtXAB2HC5sOELbUHp00DWLglKk7');

        $checkout_session = Session::create([

            'allow_promotion_codes' => true,
            'billing_address_collection' => 'required',
            'payment_method_types' => ['card'],
            'line_items' => [[
                $product_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/command/success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/command/cancel/{CHECKOUT_SESSION_ID}',
        ]);

        $command->setStripeSessionID($checkout_session->id);
        $this->em->flush();

        return new JsonResponse(['id' => $checkout_session->id]);
    }
}
