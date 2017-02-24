<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use AppBundle\Form\ImageType;
use AppBundle\Service\ImageUploader;
use AppBundle\Service\PersonPredictor;
use AppBundle\Repository\MainImage;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/upload-image", name="upload-image")
     */
    public function uploadImageAction(Request $request)
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $image->getImage();
            /** @var ImageUploader $imageUploader */
            $imageUploader = $this->get(ImageUploader::DIC);
            $imageName = $imageUploader->upload($image);

            /** @var PersonPredictor $personPredictor */
            $personPredictor = $this->get(PersonPredictor::DIC);
            $predictedName = $personPredictor->predict($imageName);

            /** @var MainImage $mainImageRepository */
            $mainImageRepository = $this->get(MainImage::DIC);
            $lookALikeImage = $mainImageRepository->getByName($predictedName);
        }

        return new JsonResponse([
            'mainImage' => $lookALikeImage,
            'name' => $predictedName
        ]);
    }
}
