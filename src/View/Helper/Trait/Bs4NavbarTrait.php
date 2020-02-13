<?php

namespace QuinenCake\View\Helper;

trait Bs4NavbarTrait
{
    public function navbar($brand = false, $list = [], $options = [])
    {
//        debug(func_get_args());
        $optionsDefault = [
            // sm or lg possible, switch size for mobile menu
            'expand' => true,
            // light, dark
            'theme' => "light",
            'bg' => "light"
        ];

        $options += $optionsDefault;

        $options = $this->Html->addClass($options, 'navbar');

        // expand
        if ($options['expand']) {
            $expand = (is_string($options['expand']) ? '-' . options['expand'] : '');
            $options = $this->Html->addClass($options, 'navbar-expand' . $expand);
        }

        // theme
        if ($options['theme']) {
            $options = $this->Html->addClass($options, 'navbar-' . $options['theme']);
        }

        // color
        if ($options['bg']) {
            $options = $this->Html->addClass($options, 'bg-' . $options['bg']);
        }

        $navbar = [];
        if ($brand) {
            list($brand, $brandOptions) = $this->getContentOptions($brand);
            if (!$this->Html->isLinkExistInOptions($brandOptions)) {
                $brandOptions += ['link' => '#'];
            }
            list($navbar[],) = $this->linkify($brand, $brandOptions, ['class' => "navbar-brand"]);
        }

        // convert list elements
        $navbarList = $this->navbarList($list, $options);

        // split navbar on left and right
        $navbarLeftRight = collection($navbarList)->reduce(function ($reducer, $navbar) {
            if (isset($navbar[1]['align']) && $navbar[1]['align'] === 'right') {
                unset($navbar[1]['align']);
                $reducer['right'][] = $navbar;
            } else {
                $reducer['left'][] = $navbar;
            }
            return $reducer;
        }, ['left' => [], 'right' => []]);

        $navbar[] = $this->Html->list($navbarLeftRight['left'], ['class' => "navbar-nav"]);
        $navbar[] = $this->Html->list($navbarLeftRight['right'], ['class' => "navbar-nav ml-auto"]);
        $options = array_diff_key($options, $optionsDefault);

        return $this->Html->tag('nav', implode($navbar), $options);
    }

    protected function navbarList($list, $options = [])
    {
        return collection($list)->map(function ($element) use ($options) {
            // formatage
            list($element, $elementOptions) = $this->getContentOptions($element);

            if (is_array($element) && empty($elementOptions)) {
                $elementOptions = $element;
                $element = false;
            }

            list($element, $elementOptions) = $this->Html->getIconText($elementOptions + ['text' => $element]);
            // linkify
            if ($this->Html->isLinkExistInOptions($elementOptions)) {
                list($element, $elementOptions) = $this->Html->linkify($element, $elementOptions,
                    ['class' => "nav-link"]);

            } else {
                if (isset($elementOptions['list'])) {

                    $list = $elementOptions['list'];
                    unset($elementOptions['list']);

                    $button = [
                        false,
                        [
                            // option for navbar only
                            'align' => false
                        ] + $elementOptions + [
                            'text' => $element,
                            'color' =>
                                $options['bg']
                        ]
                    ];
                    unset($elementOptions['title']);

                    $elementOptions = $this->Html->addClass($elementOptions, 'dropdown');
                    $element = $this->dropdown($button, $list, ['inDiv' => false]);

                } else {
                    $element = $this->Html->div('navbar-text', $element);
                }
            }

            $elementOptions = $this->addClassFromBooleanOptions($elementOptions, ['active', 'disabled']);
            $elementOptions = $this->Html->addClass($elementOptions, "nav-item");

            return [$element, $elementOptions];
        })->toArray();
    }
}
