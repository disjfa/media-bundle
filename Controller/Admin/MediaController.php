<?php

namespace Disjfa\MediaBundle\Controller\Admin;

use Disjfa\MediaBundle\Entity\Media;
use Disjfa\MediaBundle\Form\Type\MediaType;
use Disjfa\MediaBundle\Model\MediaModel;
use Disjfa\MediaBundle\Service\UploadService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/upload")
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
     * @Route("/", name="disjfa_media_admin_media_upload")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function uploadAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $upload = new MediaModel();
        $form = $this->createForm(MediaType::class, $upload);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            try {
                $this->uploadService->uploadFile($upload);
            } catch (Exception $e) {
                $this->addFlash('warning', $e->getMessage());
            }
            return $this->redirectToRoute('disjfa_media_admin_media_upload');
        }

        return $this->render('@DisjfaMedia/media/upload.html.twig', [
            'form' => $form->createView(),
            'media' => $this->getDoctrine()->getRepository(Media::class)->findAll(),
        ]);
    }
}
