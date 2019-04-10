<?php

namespace Disjfa\MediaBundle\Controller\Admin;

use Disjfa\MediaBundle\Entity\Media;
use Disjfa\MediaBundle\Form\Type\MediaEditType;
use Disjfa\MediaBundle\Form\Type\UploadType;
use Disjfa\MediaBundle\Model\MediaModel;
use Disjfa\MediaBundle\Service\UploadService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/admin/upload")
 */
class MediaController extends Controller
{
    /**
     * @var UploadService
     */
    private $uploadService;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(UploadService $uploadService, TranslatorInterface $translator)
    {
        $this->uploadService = $uploadService;
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="disjfa_media_admin_media_index")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws Exception
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $upload = new MediaModel();
        $form = $this->createForm(UploadType::class, $upload);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            try {
                $this->uploadService->uploadFile($upload);
            } catch (Exception $e) {
                $this->addFlash('warning', $e->getMessage());
            }

            return $this->redirectToRoute('disjfa_media_admin_media_index');
        }

        return $this->render('@DisjfaMedia/media/index.html.twig', [
            'form' => $form->createView(),
            'media' => $this->getDoctrine()->getRepository(Media::class)->findAll(),
        ]);
    }

    /**
     * @Route("/{media}", name="disjfa_media_admin_media_edit")
     *
     * @param Request $request
     * @param Media   $media
     *
     * @return Response
     */
    public function editAction(Request $request, Media $media)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(MediaEditType::class, $media);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->persist($media);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', $this->translator->trans('success.media_saved', [], 'disjfa-media'));

                return $this->redirectToRoute('disjfa_media_admin_media_index');
            }
        }

        return $this->render('@DisjfaMedia/media/edit.html.twig', [
            'form' => $form->createView(),
            'media' => $media,
        ]);
    }
}
