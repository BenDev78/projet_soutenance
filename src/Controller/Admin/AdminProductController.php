<?php


namespace App\Controller\Admin;


use App\Entity\Product;
use App\Form\CreateType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class AdminProductController
 * @Route("/admin/produits")
 * @package App\Controller\Admin
 */
class AdminProductController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/creer", name="admin_product_create")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function create(Request $request, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $product->setIsBest(false);
        $product->setSlug($slugger->slug('-', $product->getName()));

        $form = $this->createForm(CreateType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $productFile */
            $productFile = $form->get('image')->getData();
            if ($productFile) {
                $productFileName = $fileUploader->upload($productFile);
                $product->setImage($productFileName);
            }

            $this->em->persist($product);
            $this->em->flush();

            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/create_product.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="admin_product_update", methods={"GET|POST"})
     * @param Product $product
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function update(Product $product, Request $request, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        # Récupération du l'image existante
        $oldFile = new File($this->getParameter('images_directory') . '/' . $product->getImage());
        $oldFileName = $oldFile->getFilename();

        $form = $this->createForm(CreateType::class, $product)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setImage($oldFileName);

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            # Générer le nom de l'image | sécurisation du nom de l'image
            if ($imageFile) {
                # Supprime l'ancienne image si elle doit être modifiée
                $filesystem = new Filesystem();
                $filesystem->remove($this->getParameter('images_directory') . '/' . $oldFileName);

                $newFilename = $fileUploader->upload($imageFile);

                # /!\ Permet d'insérer le nouveau nom de l'image dans la BDD /!\
                $product->setImage($newFilename);
            }

            $this->em->flush();

            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/create_product.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/isbest/{id}", name="admin_isbest_product", methods={"GET|POST"})
     * @param Product $product
     * @return JsonResponse
     */
    public function isBest(Product $product): JsonResponse
    {
        $product->setIsBest(!$product->getIsBest());
        $this->em->flush();
        return $this->json(['success' => true]);
    }

    /**
     * @Route("/supprimer/{id}", name="admin_product_delete", methods={"GET|POST"})
     * @param Product $product
     * @return Response
     */
    public function delete(Product $product): Response
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getParameter('images_directory') . '/' . $product->getName());

        $this->em->remove($product);
        $this->em->flush();
        return $this->redirectToRoute('admin_products');
    }
}
