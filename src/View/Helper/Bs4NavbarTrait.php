<?php

namespace QuinenCake\View\Helper;

use Cake\Utility\Hash;

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
            'bg' => "light",
            'classRight' => 'ml-auto align-items-center'
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
            $align = (isset($navbar[1]['align']) ? $navbar[1]['align'] : 'left');
            $reducer[$align][] = $navbar;
            return $reducer;
        }, []);

        $navbar[] = $this->Html->list($navbarLeftRight['left'], ['class' => "navbar-nav"]);
        if (isset($navbarLeftRight['center'])) {
            $navbar[] = implode('', Hash::extract($navbarLeftRight['center'], '{n}.0'));
        }
        if (isset($navbarLeftRight['right'])) {
            $navbar[] = $this->Html->list($navbarLeftRight['right'], ['class' => trim("navbar-nav " . $options['classRight'])]);
        }

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
                if (isset($elementOptions['id'])) {
                    $id = $elementOptions['id'];
                    unset($elementOptions['id']);
                }
                // id should be unique
                list($element, $elementOptions) = $this->Html->linkify(
                    $element,
                    $elementOptions,
                    ['class' => "nav-link"]
                );

                if (isset($id)) {
                    $elementOptions += compact('id');
                }

            } else {
                if (isset($elementOptions['list'])) {

                    $list = $elementOptions['list'];
                    unset($elementOptions['list']);

                    $button = [
                        false,
                        [
                            // option for navbar only
                            'align' => false
                        ] + array_diff_key($elementOptions, array_flip(['id', 'style'])) + [
                            'text' => $element,
                            'color' => $options['bg']
                        ]
                    ];
                    unset($elementOptions['title']);

                    // on elimine les classes superficielle dans l'element de titre, seul le bouton doit la recevoir
                    $elementOptions['class'] = '';
                    $elementOptions = $this->Html->addClass($elementOptions, 'dropdown');
                    $element = $this->dropdown($button, $list, ['inDiv' => false]);

                } else if (isset($elementOptions['raw'])) {
                    $element = $elementOptions['raw'];
                    unset($elementOptions['raw']);
                } else {
                    $elementOptions = $this->addClass($elementOptions, 'navbar-text');
                    $element = $this->Html->tag('div', $element, $elementOptions);
                }
            }

            $elementOptions = $this->addClassFromBooleanOptions($elementOptions, ['active', 'disabled']);
            $elementOptions = $this->Html->addClass($elementOptions, "nav-item");

            return [$element, $elementOptions];
        })->toArray();
    }
}
