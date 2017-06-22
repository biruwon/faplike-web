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
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        // TODO: worst code ever!

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                list($predictedInfo, $lookALikeImage) = $this->onImageUpload($image);

                if ($predictedInfo['name'] === 'none') {
                    return new JsonResponse(
                        ['message' => 'No face detected in the image'],
                        400
                    );
                }

            } catch (ProcessFailedException $exception){
                return new JsonResponse('', 500);
            }

        } else {

            $error = $form->getErrors(true)->current();
            $errorMessage = $error->getMessage();

            return new JsonResponse(
                ['message' => $errorMessage],
                400
            );

        }

        return new JsonResponse([
            'mainImage' => $lookALikeImage,
            'name' => $predictedInfo['name'],
            'confidence' => $predictedInfo['confidence']
        ]);
    }

    /**
     * @Route("/upload-from-url", name="upload-from-url")
     */
    public function uploadFromUrlAction(Request $request)
    {
        $url = new Url();

        // TODO: This is just another way of doing it without form type, unify the forms for image and url
        $form = $this->get('form.factory')
            ->createNamedBuilder('upload_form', FormType::class, $url, [
                'data_class' => Url::class,
                'csrf_protection' => false])
            ->add('url', UrlType::class)
            ->getForm();

        $form->handleRequest($request);

        // TODO: all the return shit brother!
        if ($form->isSubmitted() && $form->isValid()) {
            $url = $url->getUrl();
            $imageFromUrl = $this->get(ImageFromUrl::DIC);
            $imageToValidate = $imageFromUrl->getImage($url);
            if (is_null($imageToValidate)) {
                return new JsonResponse(
                    ['message' => 'This is not a valid url'],
                    400
                );
            }

            $image = new Image();
            $image->setImage($imageToValidate);
            $validator = $this->get('validator');
            $errorList = $validator->validate($image);
            if ($errorList->count() > 0) {
                $errorMessage = $errorList[0]->getMessage();
                return new JsonResponse(
                    ['message' => $errorMessage],
                    400
                );
            }

            try {
                list($predictedInfo, $lookALikeImage) = $this->onImageUpload($image);

                if ($predictedInfo['name'] === 'none') {
                    return new JsonResponse(
                        ['message' => 'No face detected in the image'],
                        400
                    );
                }
            } catch (ProcessFailedException $exception){
                return new Response('', 500);
            }
            
        } else {

            $error = $form->getErrors(true)->current();
            $errorMessage = $error->getMessage();
            
            return new JsonResponse(
                ['message' => $errorMessage],
                400
                );
            }

        return new JsonResponse([
            'mainImage' => $lookALikeImage,
            'name' => $predictedInfo['name'],
            'confidence' => $predictedInfo['confidence']
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
        /** @var VideoAPI $videoAPI */
        $videoAPI = $this->get(VideoAPI::DIC);
        $videoInfoList = $videoAPI->search($name);

        return new JsonResponse(['videoInfoList' => $videoInfoList]);
    }

    /**
     * @param Image $image
     * @return array
     */
    protected function onImageUpload(Image $image)
    {
        /** @var ImageUploader $imageUploader */
        $imageUploader = $this->get(ImageUploader::DIC);
        $imageName = $imageUploader->upload($image->getImage());

        /** @var PersonPredictor $personPredictor */
        $personPredictor = $this->get(PersonPredictor::DIC);
        $predictedInfo = $personPredictor->predict($imageName);

        /** @var MainImage $mainImageRepository */
        $mainImageRepository = $this->get(MainImage::DIC);
        $lookALikeImage = $mainImageRepository->getByName($predictedInfo['name']);

        return [$predictedInfo, $lookALikeImage];
    }
}
