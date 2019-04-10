<?php

namespace Disjfa\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity
 * @ORM\Table(name="disjfa_media")
 */
class Media
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="url", type="string")
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(name="user_id", type="string")
     */
    private $userId;

    /**
     * @var int
     * @ORM\Column(name="size", type="bigint")
     */
    private $size;

    /**
     * @var string
     * @ORM\Column(name="mime_type", type="string")
     */
    private $mimeType;

    /**
     * @var string
     * @ORM\Column(name="extension", type="string")
     */
    private $extension;

    /**
     * @param File $file
     * @param $userId
     *
     * @throws Exception
     */
    public function __construct(File $file, $name, $userId)
    {
        $this->id = Uuid::uuid4();
        $this->userId = $userId;
        $this->name = $name;
        $this->size = $file->getSize();
        $this->mimeType = $file->getMimeType();
        $this->extension = $file->getExtension();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
