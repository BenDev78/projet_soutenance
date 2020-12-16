<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\CreateType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/create", name="admin_dashboard")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function create(Request $request, FileUploader $fileUploader): Response
    {
        $product = new Product();
        $form = $this->createForm(CreateType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var UploadedFile $productFile */
            $productFile = $form->get('image')->getData();
            if ($productFile) {
                $productFileName = $fileUploader->upload($productFile);
                $product->setImage($productFileName);
            }

            $this->em->persist($product);
            $this->em->flush();
        }

        return $this->render('admin/create.html.twig', [
            'form' => $form->createView()
        ]);
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
     * @Route("/update/{id}", name="admin_update", methods={"GET|POST"})
     * @param Product $product
     */
    public function update(Product $product)
    {
        
    }
}
