<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use AppBundle\Entity\Url;
use AppBundle\Form\ImageType;
use AppBundle\Service\ImageUploader;
use AppBundle\Service\PersonPredictor;
use AppBundle\Service\ImageFromUrl;
use AppBundle\Service\VideoAPI;
use AppBundle\Repository\MainImage;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;

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
        /*return new JsonResponse([
            'mainImage' => 'test.jpg',
            'name' => 'emilia-d'
        ]);*/

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

    /**
     * @Route("/upload-from-url", name="upload-from-url")
     */
    public function uploadFromUrlAction(Request $request)
    {
        $url = new Url();

        // This is just another way of doing it without form type, unify the forms for image and url
        $form = $this->get('form.factory')
            ->createNamedBuilder('upload_form', FormType::class, $url, [
                'data_class' => Url::class,
                'csrf_protection' => false])
            ->add('url', UrlType::class)
            ->getForm();

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $url = $url->getUrl();
            $imageFromUrl = $this->get(ImageFromUrl::DIC);
            $imageToValidate = $imageFromUrl->getImage($url);

            $image = new Image();
            $image->setImage($imageToValidate);
            $validator = $this->get('validator');
            $validator->validate($image);

            /** @var ImageUploader $imageUploader */
            $imageUploader = $this->get(ImageUploader::DIC);
            $imageName = $imageUploader->upload($image->getImage());

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

    /**
     * @Route("/featured-pictures/{name}", name="featured-pictures")
     */
    public function getFeaturedPictures($name)
    {
        /** @var MainImage $mainImageRepository */
        $mainImageRepository = $this->get(MainImage::DIC);
        $featuredImagePaths = $mainImageRepository->getFeaturedPictures($name);
        
        return new JsonResponse(['featuredImagePaths' => $featuredImagePaths]);
    }

    /**
     * @Route("/embed/{name}", name="embed-video")
     */
    public function getEmbedVideos($name)
    {
        $videoAPI = $this->get(VideoAPI::DIC);
        $embedIds = $videoAPI->search($name);

        return new JsonResponse(['embedIds' => $embedIds]);
    }
}
