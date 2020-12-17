<?php

namespace App\Controller\Admin;

use App\Entity\Command;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CreateType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @Route("/admin")
 * @package App\Controller
 */
class AdminController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="admin_dashboard", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/products", name="admin_products", methods={"GET|POST"})
     * @return Response
     */
    public function allProducts(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findall();

        return $this->render('admin/products.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/users", name="admin_users", methods={"GET"})
     * @return Response
     */
    public function allUsers(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('admin/user.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/commands", name="admin_commands", methods={"GET"})
     * @return Response
     */
    public function allCommands(): Response
    {
        $commands = $this->getDoctrine()->getRepository(Command::class)->findAll();

        return $this->render('admin/commands.html.twig', [
            'commands' => $commands
        ]);
    }

}
