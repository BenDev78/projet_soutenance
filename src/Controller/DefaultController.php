<?php

namespace App\Controller;

use App\Classe\Contact;
use App\Entity\Actuality;
use App\Entity\Command;
use App\Entity\Product;
use App\Entity\Review;
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

        $actualities = $this->em->getRepository(Actuality::class)->findAll();

        return $this->render('default/index.html.twig', [
            'products' => $products,
            'actualities' => $actualities
        ]);
    }

    /**
     * @Route("/contact", name="default_contact", methods={"GET|POST"})
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact)->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $email = (new TemplatedEmail())
                ->from(new Address($contact->getEmail(), $contact->getFirstname()))
                ->to('94edbdcabe-d0b5d2@inbox.mailtrap.io')
                ->subject($contact->getSubject())
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'firstname' => $contact->getFirstname(),
                    'lastname' => $contact->getLastname(),
                    'subject' => $contact->getSubject(),
                    'message' => $contact->getMessage()
                ])
            ;

            $mailer->send($email);

            $this->addFlash('success', 'Votre email a bien été envoyé, nous vous répondrons dans les plus brefs délais.');

            return $this->redirectToRoute('default_contact');
        }


        return $this->render('default/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profil", name="default_profile", methods={"GET|POST"})
     * @return Response
     */
    public function profile(): Response
    {
        return $this->render('user/profil.html.twig');
    }

    /**
     * @Route("/profil/commandes", name="default_profile_commands", methods={"GET|POST"})
     * @return Response
     */
    public function commands(): Response
    {
        return $this->render('user/commandes-profile.html.twig');
    }

    /**
     * @Route("/profil/adresses", name="default_profile_addresses", methods={"GET|POST"})
     * @return Response
     */
    public function addresses(): Response
    {
        return $this->render('user/addresses-profile.html.twig');
    }

    /**
     * @Route("/profil/commande/{id}", name="default_profil_command")
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
    /**
     * @Route("/profil/commentaires", name="default_profile_reviews", methods={"GET|POST"})
     * @return Response
     */
    public function reviews(): Response
    {
        return $this->render('user/detail-review.html.twig');
    }

    /**
     * @Route("/conditions-generales", name="default_terms", methods={"GET"})
     * @return Response
     */
    public function terms(): Response
    {
        return $this->render('default/terms.html.twig');
    }

    /**
     * @Route("/histoire", name="default_history", methods={"GET"})
     * @return Response
     */
    public function about(): Response
    {
        return $this->render('default/history.html.twig');
    }
}

