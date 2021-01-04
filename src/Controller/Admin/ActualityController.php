<?php


namespace App\Controller\Admin;


use App\Entity\Actuality;
use App\Form\ActualityType;
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
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class ActualityController
 * @Route("admin/actualite")
 * @package App\Controller\Admin
 */
class ActualityController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @Route("/creer", name="admin_actuality_create", methods={"GET|POST"})
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function create(Request $request, FileUploader $fileUploader): Response
    {
        $actuality = new Actuality();

        $form = $this->createForm(ActualityType::class, $actuality)->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if ($form->isSubmitted() && $form->isValid()) {

                /** @var UploadedFile $actualityFile */
                $actualityFile = $form->get('flyer')->getData();
                if ($actualityFile) {
                    $actualityFileName = $fileUploader->upload($actualityFile);
                    $actuality->setFlyer($actualityFileName);
                }

                $this->em->persist($actuality);
                $this->em->flush();

                return $this->redirectToRoute('admin_actualities');
            }
        }

        return $this->render('admin/actuality-create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/modifier/{id}", methods={"GET|POST"})
     * @param Actuality $actuality
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function update(Actuality $actuality, Request $request, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        if($actuality->getFlyer())
        {
            $oldFile = new File($this->getParameter('images_directory') . '/' . $actuality->getFlyer());
            $oldFileName = $oldFile->getFilename();
        }
        else{
            $oldFileName = NULL;
        }
        
        $form = $this->createForm(ActualityType::class, $actuality)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $actuality->setFlyer($oldFileName);

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('flyer')->getData();

            if ($imageFile) {
                $filesystem = new Filesystem();
                $filesystem->remove($this->getParameter('images_directory') . '/' . $oldFileName);

                $newFilename = $fileUploader->upload($imageFile);

                $actuality->setFlyer($newFilename);
            }

            $this->em->flush();

            return $this->redirectToRoute('admin_actuality_create');
        }

        return $this->render('admin/actuality-create.html.twig', [
            'actuality' => $actuality,
            'form' => $form->createView()
        ]);
    }
}
