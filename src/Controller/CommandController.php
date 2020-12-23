<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\DataCommand;
use App\Entity\Command;
use App\Entity\Detail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/command", name="command_index")
     * @param Cart $cart
     * @param Request $request
     * @return Response
     */
    public function index(Cart $cart, Request $request): Response
    {
        //Pas d'adresses en bdd
//        if (!$this->getUser()->getAddresses()->getValues()) {
//            return $this->redirectToRoute('address_create');
//        }

        //Creation des cartes d'adresse client
        $form = $this->createForm(DataCommand::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
        }

        return $this->render('command/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    /**
     * @Route("/command/confirm", name="new_command", methods={"POST"})
     * @param Cart $cart
     * @param Request $request
     * @return Response
     */
    public function new(Cart $cart, Request $request): Response
    {
        //Pas d'adresses en bdd
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('address_create');
        }

        //Creation des cartes d'adresse client
        $form = $this->createForm(DataCommand::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime();
            $carriers = $form->get('carriers')->getData();
            $address = $form->get('address')->getData();
            $address_content = $address->getUser()->getFirstName().' '.$address->getUser()->getLastName();
            $address_content .= '<br/>'.$address->getAddress();
            $address_content .= '<br/>'.$address->getPostalCode().' '.$address->getCity();
            $address_content .= '<br/>'.$address->getCountry();

            // Enregistrer ma commande
            $command = new Command();
            $reference = $date->format('dmY').'-'.uniqid();
            $command->setReference($reference);
            $command->setUser($this->getUser())
                ->setCreatedAt($date)
                ->setCarrier($carriers)
                ->setAddress($address_content)
                ->setIsPaid(0);
            $this->entityManager->persist($command);

            // Enregistrer les Détails
            foreach ($cart->getFull() as $product) {
                $detail = new Detail();
                $detail->setCommand($command)
                    ->setProduct($product['products'])
                    ->setQuantity($product['quantities']);
                $this->entityManager->persist($detail);
            }

            $this->entityManager->flush();

            return $this->render('command/add.html.twig', [
                'cart' => $cart->getFull(),
                'carriers' => $carriers,
                'address' => $address,
                'addressContent' => $address_content,
                'reference' => $command->getReference()
            ]);
        }
        return $this->redirectToRoute('cart');
    }
}