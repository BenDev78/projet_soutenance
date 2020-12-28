<?php


namespace App\Controller;


use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AddressController
 * @Route("profile/address")
 * @package App\Controller
 */
class AddressController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/create", name="address_create", methods={"GET|POST"})
     * @param Cart $cart
     * @param Request $request
     * @return Response
     */
    public function create(Cart $cart, Request $request): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address)->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $address->setUser($this->getUser());

            $this->em->persist($address);
            $this->em->flush();

            if ($cart->getFull()) {
                return $this->redirectToRoute('command_index');
            }

            return $this->redirectToRoute('default_profile_addresses');
        }

        return $this->render('user/address-create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update/{id}", name="address_update", methods={"GET|POST"})
     * @param Address $address
     * @param Request $request
     * @return Response
     */
    public function update(Address $address, Request $request): Response
    {

        if($address->getUser() !== $this->getUser()) {
            $this->addFlash('warning', 'Vous n\'avez pas accès à cette adresse !');
            return $this->redirectToRoute('default_index');
        }

        $form = $this->createForm(AddressType::class, $address)->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();

            return $this->redirectToRoute('default_profile_addresses');
        }

        return $this->render('user/address-create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="address_delete", methods={"GET|POST"})
     * @param Cart $cart
     * @param Address $address
     * @return Response
     */
    public function delete(Cart $cart, Address $address): Response
    {
        if($address->getUser() !== $this->getUser()) {
            $this->addFlash('warning', 'Vous n\'avez pas accès à cette adresse !');
            return $this->redirectToRoute('default_index');
        }

        $this->em->remove($address);
        $this->em->flush();

        if ($cart->getFull()) {
            return $this->redirectToRoute('command_index');
        }

        return $this->redirectToRoute('default_profile_addresses');
    }
}
