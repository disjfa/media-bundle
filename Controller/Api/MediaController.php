<?php

namespace Disjfa\MediaBundle\Controller\Api;

use Disjfa\MediaBundle\Form\Type\UploadType;
use Disjfa\MediaBundle\Model\MediaModel;
use Disjfa\MediaBundle\Service\UploadService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/upload")
 */
class MediaController extends Controller
{
    /**
     * @var UploadService
     */
    private $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * @Route("/", name="disjfa_media_api_media_upload")
     * @Method("POST")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function uploadAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $upload = new MediaModel();
        $form = $this->createForm(UploadType::class, $upload);

        $form->handleRequest($request);
        try {
            $media = $this->uploadService->uploadFile($upload);
        } catch (Exception $e) {
            return new JsonResponse([
                'messsage' => $e->getMessage(),
            ], 400);
        }

        return new JsonResponse([
            'id' => $media->getId(),
            'url' => $media->getUrl(),
            'name' => $media->getName(),
        ]);
    }
}
