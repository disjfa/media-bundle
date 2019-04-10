<?php

namespace Disjfa\MediaBundle\Service;

use DateTime;
use Disjfa\MediaBundle\Entity\Media;
use Disjfa\MediaBundle\Model\MediaModel;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UploadService
{
    /**
     * @var string
     */
    private $uploadFolder;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * UploadService constructor.
     *
     * @param string                 $uploadPath
     * @param string                 $rootDir
     * @param RequestStack           $requestStack
     * @param Filesystem             $filesystem
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface  $token
     *
     * @throws Exception
     */
    public function __construct(string $uploadPath, string $rootDir, RequestStack $requestStack, Filesystem $filesystem, EntityManagerInterface $entityManager, TokenStorageInterface $token)
    {
        $this->uploadFolder = realpath($rootDir.$uploadPath);
        if (null === $this->uploadFolder) {
            throw new Exception('Path does not exists: '.$rootDir.$uploadPath);
        }
        $this->request = $requestStack->getMasterRequest();
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
        $this->user = $token->getToken()->getUser();
    }

    /**
     * @param MediaModel $upload
     *
     * @return Media
     *
     * @throws Exception
     */
    public function uploadFile(MediaModel $upload)
    {
        $upload = $upload->getUpload();
        if (false === $upload instanceof UploadedFile) {
            throw new Exception('No uploaded file found');
        }

        $date = new DateTime('now');
        $folder = $this->uploadFolder;
        $folder = $folder.'/'.$date->format('Y');
        if (false === $this->filesystem->exists($folder)) {
            $this->filesystem->mkdir($folder);
        }
        $folder .= '/'.$date->format('m');
        if (false === $this->filesystem->exists($folder)) {
            $this->filesystem->mkdir($folder);
        }
        $folder .= '/'.$date->format('d');
        if (false === $this->filesystem->exists($folder)) {
            $this->filesystem->mkdir($folder);
        }

        $filename = sha1(uniqid(mt_rand(), true)).'.'.$upload->guessExtension();
        $file = $upload->move($folder, $filename);

        $media = new Media($file, $upload->getClientOriginalName(), $this->user->getId());
        $media->setUrl($this->request->getUriForPath(str_replace($this->request->server->get('DOCUMENT_ROOT'), '', $file->getRealPath())));

        $this->entityManager->persist($media);
        $this->entityManager->flush();

        return $media;
    }
}
