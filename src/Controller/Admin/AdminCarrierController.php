<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Form\CarrierType;
use App\Form\CreateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminProductController
 * @Route("/admin/carriers")
 * @package App\Controller\Admin
 */
class AdminCarrierController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/create", name="admin_carrier_create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $carrier = new Carrier();
        $form = $this->createForm(CarrierType::class, $carrier);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($carrier);
            $this->em->flush();

            return $this->redirectToRoute('admin_carriers');
        }

        return $this->render('admin/create_carrier.html.twig', [
            'carriers' => $carrier,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update", name="admin_carrier_update", methods={"GET|POST"})
     * @param Carrier $carrier
     * @param Request $request
     * @return Response
     */
    public function update(Carrier $carrier, Request $request): Response
    {

        $form = $this->createForm(CreateType::class, $carrier)->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();

            return $this->redirectToRoute('admin_carriers');
        }

        return $this->render('admin/carrier.html.twig', [
            'carrier' => $carrier,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("delete/{id}", name="admin_carrier_delete", methods={"GET|POST"})
     * @param Carrier $carrier
     * @return Response
     */
    public function delete(Carrier $carrier): Response
    {
        $this->em->remove($carrier);
        $this->em->flush();
        return $this->redirectToRoute('admin_carriers');
    }
}
