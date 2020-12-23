<?php

namespace App\Controller;

use App\Classe\Contact;
use App\Entity\Command;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="default_index", methods={"GET"})
     */
    public function index(): Response
    {

        $products = $this->em->getRepository(Product::class)->findByIsBest(1);

        return $this->render('default/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/contact", name="default_contact", methods={"GET|POST"})
     * @param Request $request
     * @return Response
     */
    public function contact(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact)->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $this->addFlash('success', 'Votre email a bien été envoyé, nous vous répondrons dans les plus brefs délais.');

            return $this->redirectToRoute('default_contact');
        }


        return $this->render('default/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile", name="default_profile", methods={"GET|POST"})
     * @return Response
     */
    public function profile(): Response
    {
        return $this->render('user/profil.html.twig');
    }

    /**
     * @Route("/profile/commands", name="default_profile_commands", methods={"GET|POST"})
     * @return Response
     */
    public function commands(): Response
    {
        return $this->render('user/commandes-profile.html.twig');
    }

    /**
     * @Route("/profile/addresses", name="default_profile_addresses", methods={"GET|POST"})
     * @return Response
     */
    public function addresses(): Response
    {
        return $this->render('user/addresses-profile.html.twig');
    }

    /**
     * @Route("/profile/command/{id}", name="default_profil_command")
     * @param Command $command
     * @return Response
     */
    public function command(Command $command): Response
    {

        if($command->getUser() !== $this->getUser()) {
            $this->addFlash('warning', 'Vous n\'avez pas accès à cette commande !');
            return $this->redirectToRoute('default_index');
        }

        return $this->render('user/detail-command.hmtl.twig', [
            'command' => $command
        ]);
    }

}

