<?php


namespace App\Controller\Admin;


use App\Entity\Command;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
