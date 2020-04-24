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
        ];

        $navTabOptions = [
            'returnIsHtml' => false,
            'tabClass' => "card-header-tabs",
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
        unset($options['title']);

        if (!$options['header'] && !empty($options['buttons'])) {
            $options['header'] = $this->buttons($options['buttons'], ['size' => 'xs']);
        }
        unset($options['buttons']);

        $headerRight = '';
        if ($options['header']) {
            $headerRight = $this->Html->div('float-right', $options['header']);
        }
        unset($options['header']);

        list($nav, $content) = $this->navTab($navTab, $navTabOptions + $options);

        $header = $this->Html->div('float-left', $nav) . $headerRight;

        return $this->card($content, compact(['header']) + $options);
    }

    public function navTabVertical($list = [], $options = [])
    {
        $options += [
            'returnIsHtml' => false,
            'tabsWidth' => 2
        ];

        $options = $this->addClass($options, 'flex-column', 'tabClass');

        $tabsWidth = $options['tabsWidth'];
        unset($options['tabsWidth']);

        list($tabs, $contents) = $this->navTab($list, $options);
        return $this->row([
            [$tabs, ['class' => 'pr-0 border-right-0 col-' . $tabsWidth]]
            , [$contents, ['class' => 'border border-left-0 rounded-right', 'style' => 'border-left:0px']]
        ], ['class' => 'mr-1']);
    }

    public function navTab($list = [], $options = [])
    {
        // class ne dois et ne peux exister
        unset($options['class']);
        $options += [
            'id' => 'i' . Text::uuid(),
            'returnIsHtml' => true,
            'tabClass' => false,
            'isActiveDefault' => 0,
            'name' => false
        ];

        // si aucun des elements n'as l'options isActive a true, alors on set le premier par defaut
        $hasActive = collection($list)->some(function ($tabContent) {
            return isset($tabContent['isActive']) && true === $tabContent['isActive'];
        });

        if (!$hasActive) {
            while ($list[$options['isActiveDefault']]['isDisabled']) {
                $options['isActiveDefault']++;
            }
            $list[$options['isActiveDefault']]['isActive'] = true;
        }

        $tabContentDefault = [
            'tab' => false,
            'link' => false,
            'content' => false,
            'isActive' => false,
            'isDisabled' => false,
            'color' => false,
            'name' => false
        ];

        // options par defaut pour chaque tabContent
        $navTab = collection($list)->map(function ($tabContent, $index) use ($options, $tabContentDefault) {
            //debug($options);
            //debug($tabContent);
            //debug($tabContentDefault);
            if ($options['name']) {
                $name = (isset($tabContent['name']) && $tabContent['name'] ? $tabContent['name'] : $index);
                $tabContentDefault['data-name'] = $options['name'] . $name;
            }

            //debug($tabContentDefault);

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

                if ($tabContent['isDisabled']) {
                    $linkOptions = $this->Html->addClass($linkOptions, 'disabled');
                }

                if ($tabContent['color']) {
                    $linkOptions = $this->Html->addClass($linkOptions,
                        'border-bottom-0 border-' . $tabContent['color']);
                    $linkOptions = $this->Html->addClass($linkOptions, 'btn-outline-' . $tabContent['color']);
                }

                if (isset($tabContent['data-name'])) {
                    $linkOptions['data-name'] = $tabContent['data-name'];
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

                $linkOptions = $this->addClassFromBooleanOptions(
                    $linkOptions,
                    ['active', 'disabled']
                );

                if ($tabContent['isActive']) {
                    $contentOptions = $this->addClass($contentOptions, 'active');
                }

                if ($tabContent['isDisabled']) {
                    $contentOptions = $this->addClass($contentOptions, 'disabled');
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
                'id' => 'c-' . $options['id'] . ''
            ]
        );

        if (!$options['returnIsHtml']) {
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
