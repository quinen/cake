<?php

namespace QuinenCake\View\Helper;

use Cake\Utility\Text;

trait Bs4NavTrait
{

    public function navTabInCard($navTab, $options = [])
    {
        $options += [
            'title' => false,
            'header' => false,
            'buttons' => false,
            'templates' => [
                //'header' => '<div class="float-left">{{title}}</div><div class="float-right">{{nav}}</div>'
            ]
        ];

        $navTabOptions = [
            'isHtml' => false,
            'tabClass' => "card-header-tabs"
        ];

        // injection titre en premier tab
        if ($options['title']) {
            // on injecte le titre comme onglet non clickable
            $navTab = array_merge([
                [
                    'tab' => [
                        $options['title'],
                        [
                            'link' => [
                                false,
                                [
                                    'class' => 'navbar-text h6 mb-1',
                                    //'style' => []
                                ]
                            ]
                        ]
                    ],
                    'content' => false
                ]
            ], $navTab);

            // on passe a l'onglet suivant par defaut
            $navTabOptions += [
                'isActiveDefault' => 1
            ];
        }

        list($nav, $content) = $this->navTab($navTab, $navTabOptions);

        $header = $this->Html->div('float-left', $nav);

        if (!$options['header'] && $options['buttons']) {
            $options['header'] = $this->buttons($options['buttons'], ['size' => 'xs']);
        }

        if ($options['header']) {
            $header .= $this->Html->div('float-right', $options['header']);
        }

        return $this->card($content, compact(['header']));
    }

    public function navTab($list = [], $options = [])
    {
        $options += [
            'id' => 'i'.Text::uuid(),
            'isHtml' => true,
            'tabClass' => false,
            'isActiveDefault' => 0
        ];

        // si aucun des elements n'as l'options isActive a true, alors on set le premier par defaut
        $hasActive = collection($list)->some(function ($tabContent) {
            return isset($tabContent['isActive']) && true === $tabContent['isActive'];
        });

        if (!$hasActive) {
            $list[$options['isActiveDefault']]['isActive'] = true;
        }

        $tabContentDefault = [
            'tab' => false,
            'link' => false,
            'content' => false,
            'isActive' => false,
            'color' => false,
        ];

        // options par defaut pour chaque tabContent
        $navTab = collection($list)->map(function ($tabContent, $index) use ($options, $tabContentDefault) {

            return $tabContent += $tabContentDefault +
                [
                    'id' => $options['id'] . "-" . $index,
                ];

        })->reduce(function ($reducer, $tabContent) {

            if ($tabContent['tab']) {
                list($tab, $tabOptions) = $this->getContentOptions($tabContent['tab']);

                // tab.link
                $linkOptions = [
                    'data-toggle' => "tab",
                    'data-target' => "#" . $tabContent['id'],
                    //'role' => "tab"
                ];
                if ($tabContent['isActive']) {
                    $linkOptions = $this->Html->addClass($linkOptions, 'active');
                }

                if ($tabContent['color']) {
                    $linkOptions = $this->Html->addClass($linkOptions,
                        'border-bottom-0 border-' . $tabContent['color']);
                    $linkOptions = $this->Html->addClass($linkOptions, 'btn-outline-' . $tabContent['color']);
                }

                // tab
                $tabOptions += [
                    'link' => ["#", $linkOptions]
                ];
                $reducer['tabs'][] = [$tab, $tabOptions];
            } elseif ($tabContent['link']) {

                $reducer['tabs'][] = $tabContent['link'];
            }

            if ($tabContent['content']) {
                // content
                list($content, $contentOptions) = $this->getContentOptions($tabContent['content']);
                $contentOptions += [
                    'id' => $tabContent['id'],
                    'class' => "tab-pane",
                ];
                if ($tabContent['isActive']) {
                    $contentOptions = $this->addClass($contentOptions, 'active');
                }
                $reducer['contents'][] = $this->Html->tag('div', $content, $contentOptions);
            }


            return $reducer;
        }, ['tabs' => [], 'contents' => []]);

        $navOptions = [
            'class' => "nav-tabs",
            'id' => $options['id'],
        ];

        if ($options['tabClass']) {
            $navOptions = $this->addClass($navOptions, $options['tabClass']);
        }

        $nav = $this->nav(
            $navTab['tabs'], $navOptions
        );

        $content = $this->Html->div(
            'tab-content',
            implode($navTab['contents']),
            [
                'id' => 'c-'.$options['id'] . ''
            ]
        );

        if (!$options['isHtml']) {
            return [$nav, $content];
        }
        return $nav . $content;
    }

    public function nav($list = [], $options = [])
    {
        $options += [
            'id' => Text::uuid()
        ];

        $options = $this->Html->addClass($options, "nav");

        $list = $this->Html->addClassInLink($list, "nav-link");
        return $this->Html->list($list, $options, ['class' => "nav-item"]);
    }
}
