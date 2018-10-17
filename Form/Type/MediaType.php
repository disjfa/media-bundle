<?php

namespace Disjfa\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MediaType extends AbstractType
{
    public function getParent()
    {
        return TextType::class;
    }
}
