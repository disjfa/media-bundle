<?php

namespace Disjfa\MediaBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaModel
{
    private $upload;

    /**
     * @return UploadedFile
     */
    public function getUpload()
    {
        return $this->upload;
    }

    /**
     * @param UploadedFile $upload
     */
    public function setUpload($upload)
    {
        $this->upload = $upload;
    }
}
