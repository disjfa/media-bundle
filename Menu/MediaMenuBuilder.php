<?php

namespace Disjfa\MediaBundle\Menu;

use App\Menu\ConfigureMenuEvent;
use Symfony\Component\Translation\TranslatorInterface;

class MediaMenuBuilder
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * HomeMenuListener constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
//        $menu = $event->getMenu();
//
//        $menu->addChild('timetable', [
//            'route' => 'disjfa_timetable_timetable_index',
//            'label' => $this->translator->trans('menu.timetable', [], 'site'),
//        ])->setExtra('icon', 'fa-calendar');
    }
}
