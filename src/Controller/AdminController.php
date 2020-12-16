<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\CreateType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @Route("/admin")
 * @package App\Controller
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_dashboard")
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
        $form = $this->createForm(CreateType::class)->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var UploadedFile $productFile */
            $productFile = $form->get('brochure')->getData();
            if ($productFile) {
                $productFileName = $fileUploader->upload($productFile);
                $product->setImage($productFileName);
            }
        }

        return $this->render('admin/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
