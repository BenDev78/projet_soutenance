<?php


namespace App\Controller\Admin;


use App\Entity\Command;
use App\Entity\Detail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminCommandController
 * @Route("/admin/commande")
 * @package App\Controller\Admin
 */
class AdminCommandController extends AbstractController
{
    /**
     * @Route("/details/{id}", name="admin_command_details", methods={"GET"})
     * @param Command $command
     * @return Response
     */
    public function details(Command $command): Response
    {
        $details = $command->getDetails();
        $total = [];

        /**
         * On insert pour chaque produit le prix * quantité
         */
        for($i = 0; $i < count($details); $i++)
        {
            $total[] = $details[$i]->getProduct()->getPrice() * $details[$i]->getQuantity();
        }

        /**
         * On récupère la somme des éléments du tableau
         */
        $somme = array_sum($total);

        return $this->render('admin/details.html.twig', [
           'details' => $details,
            'somme' => $somme
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="admin_command_delete")
     * @param Command $command
     * @return RedirectResponse
     */
    public function delete(Command $command): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $details = $this->getDoctrine()->getRepository(Detail::class)->findByCommand($command);

        foreach ($details as $detail) {
            $command->getDetails()->removeElement($detail);
        }

        $em->flush();

        return $this->redirectToRoute('admin_commands');
    }
}
