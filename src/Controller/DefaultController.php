<?php

namespace App\Controller;

use App\Classe\Contact;
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
     * @Route("/profil/{id}", name="default_profil", methods={"GET|POST"})
     * @param User $user
     * @return Response
     */
    public function profile(User $user): Response
    {
        return $this->render('user/profil.html.twig', [
            'user' => $user
        ]);
    }

}

