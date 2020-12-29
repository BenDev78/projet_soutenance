<?php


namespace App\Controller\Admin;


use App\Entity\Actuality;
use App\Form\ActualityType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
