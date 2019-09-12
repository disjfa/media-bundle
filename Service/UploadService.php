<?php

namespace Disjfa\MediaBundle\Service;

use DateTime;
use Disjfa\MediaBundle\Entity\Media;
use Disjfa\MediaBundle\Model\MediaModel;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
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
     * @param string $uploadPath
     * @param string $rootDir
     * @param string $publicPath
     * @param RequestStack $requestStack
     * @param Filesystem $filesystem
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface $token
     *
     * @throws Exception
     */
    public function __construct(string $uploadPath, string $rootDir, string $publicPath,RequestStack $requestStack, Filesystem $filesystem, EntityManagerInterface $entityManager, TokenStorageInterface $token)
    {
        $this->publicFolder = realpath($rootDir . $publicPath);
        $this->uploadFolder = realpath($rootDir . $uploadPath);
        if (null === $this->uploadFolder) {
            throw new Exception('Path does not exists: ' . $rootDir . $uploadPath);
        }
        $this->request = $requestStack->getMasterRequest();
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
        if (null !== $token->getToken()) {
            $this->user = $token->getToken()->getUser();
        }
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

        return $this->saveFile($upload, $upload->getClientOriginalName());
    }

    /**
     * @param File $file
     * @param string $fileName
     * @return Media
     * @throws Exception
     */
    public function saveFile(File $file, string $fileName)
    {
        $folder = $this->getFolder();
        $filename = sha1(uniqid(mt_rand(), true)) . '.' . $file->guessExtension();
        $file = $file->move($folder, $filename);

        $userId = null;
        if($this->user) {
            $userId = $this->user->getId();
        }
        $media = new Media($file, $fileName, $userId);
        $media->setUrl(str_replace($this->publicFolder, '', $file->getRealPath()));

        $this->entityManager->persist($media);
        $this->entityManager->flush();

        return $media;
    }

    private function getFolder()
    {
        $date = new DateTime('now');
        $folder = $this->uploadFolder;
        $folder = $folder . '/' . $date->format('Y');
        if (false === $this->filesystem->exists($folder)) {
            $this->filesystem->mkdir($folder);
        }
        $folder .= '/' . $date->format('m');
        if (false === $this->filesystem->exists($folder)) {
            $this->filesystem->mkdir($folder);
        }
        $folder .= '/' . $date->format('d');
        if (false === $this->filesystem->exists($folder)) {
            $this->filesystem->mkdir($folder);
        }
        return $folder;
    }
}
