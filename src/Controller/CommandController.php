<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\DataCommand;
use App\Classe\Mail;
use App\Entity\Command;
use App\Entity\Detail;
use App\Entity\Product;
use ContainerSFVfHvO\getMaker_AutoCommand_MakeCommandService;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @Route("/commande", name="command_index")
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

        return $this->render('command/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    /**
     * @Route("/commande/confirmer", name="new_command", methods={"POST|GET"})
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
            $address_content = $address->getUser()->getFirstName() . ' ' . $address->getUser()->getLastName();
            $address_content .= '<br/>' . $address->getAddress();
            $address_content .= '<br/>' . $address->getPostalCode() . ' ' . $address->getCity();
            $address_content .= '<br/>' . $address->getCountry();

            // Enregistrer ma commande
            $command = new Command();
            $reference = $date->format('dmY') . '-' . uniqid();
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

    /**
     * @Route("/commande/succes/{stripeSessionID}", name="command_success")
     * @param Command $command
     * @param Cart $cart
     * @return RedirectResponse|Response
     */
    public function success(Command $command, Cart $cart)
    {
        if (!$command || $command->getUSer() !== $this->getUser()) {
            return $this->redirectToRoute('default_index');
        }

        if (!$command->getIsPaid()) {
            // Commande payée
            $command->setIsPAid(1);
            $this->entityManager->flush();
        }

        $price= null;
        $quanity = null;

        foreach ($cart->getFull() as $product) {
            $price += $product['products']->getPrice() * $product['quantities'];
            $quanity += $product['quantities'];
        }

        //PDF
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->setDefaultFont('Roboto');
        $options->setDefaultPaperOrientation('portrait');
        $options->setIsHtml5ParserEnabled(true);
        $dompdf->setOptions($options);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('command/pdf.html.twig', [
            'command' => $command,
            'price' => $price,
            'quantity' => $quanity
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size
        $dompdf->setPaper('A4');

        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output([0]);

        // In this case, we want to write the file in the public directory
        $publicDirectory = $this->getParameter('pdf_directory');

        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath = $publicDirectory . $command->getReference() . '.pdf';

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);

        //envoi de mail de commande
        $user = $command->getUser();
        $content = "<br>
        <h2>Confirmation de commande</h2><br>
        <p>Félicitations <span style='color: #ECBC10'>" . $user->getFirstname() . ' ' . $user->getLastname() . "</span> pour votre commande !<br><br>
            Nous vous remercions pour votre commande n°<strong style='color: #ECBC10'>" . $command->getReference() . "</strong>.<br>
            Une confirmation vient de vous etre envoyé par mail à l'adresse <strong style='color: #ECBC10'>" . $user->getEmail() . "</strong>.
        </p>
        <hr>
        <p>
            Votre commande sera livrée par <strong style='color: #ECBC10'>" . $command->getCarrier()->getName() . "</strong> à l'adresse : <br><br>" . $command->getAddress() . ".
        </p>
        <hr>";

        $mail = new Mail();
        $mail->send(
            $user->getEmail(),
            $user->getFirstname() . ' ' . $user->getLastname(),
            'Confirmation de votre commande n°' . $command->getReference(),
            $content
        );

        // Panier vidé
        $cart->remove();

        return $this->render('command/success.html.twig', [
            'command' => $command
        ]);
    }

    /**
     * @Route("/commande/annulation/{stripeSessionID}", name="command_cancel")
     * @param Command $command
     * @param Cart $cart
     * @return Response
     */
    public function cancel(Command $command, Cart $cart): Response
    {
        if (!$command || $command->getUSer() !== $this->getUser()) {
            return $this->redirectToRoute('default_index');
        }

        // Update des stocks en BDD
        foreach ($cart->getFull() as $product) {
            $stock = $product['products']->getStock();
            $stock = $stock + $product['quantities'];
            $product['products']->setStock($stock);
        }

        $this->entityManager->flush();

        return $this->render('command/cancel.html.twig', [
            'command' => $command
        ]);
    }
}
