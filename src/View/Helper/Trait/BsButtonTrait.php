<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 05/11/18
 * Time: 11:55
 */

namespace QuinenCake\View\Helper;


use Cake\Utility\Hash;


trait BsButtonTrait
{

    /**
     * generate button, differents calls possibles
     *
     * @param string|array $model model key
     * @param array|string $options options or text for button model
     * @return string link with buttons class
     */
    public function button($model = false, $options = [], $data = [])
    {
        // get options from model
        $options = $this->getButtonOptionsFromModel($model, $options, $data);

        $options += [
            'isBlock' => false,
        ];

        // generate first mandatory class values
        $options = $this->getButtonColor($options);
        $options = $this->getButtonSize($options);

        if ($options['isBlock']) {
            $options = $this->addClass($options, 'btn-block');
        }
        unset($options['isBlock']);


        // linkify the button
        $buttonLink = $this->getButtonLink($options);

        return $buttonLink;
    }

    public function buttons($buttons, $options = [], $data = [])
    {
        $options += [
            'size' => false
        ];

        $options = $this->addClass($options, 'btn-group');

        if ($options['size']) {
            $options = $this->addClass($options, 'btn-group-' . $options['size']);
        }

        $content = implode(collection($buttons)->map(function ($button) use ($data) {
            list($button, $buttonOptions) = $this->getContentOptions($button);
            return $this->button($button, $buttonOptions, $data);
        })->toArray());

        return $this->Html->tag('div', $content, $options);
    }

    public function buttonsField($field, $options)
    {
        $content = implode(collection($options)->map(function ($button) use ($field) {
            return $this->buttonField($field, $button);
        })->toArray());
        return $this->Html->div('btn-group', $content);
    }

    public function buttonField($field, $options)
    {
        if (!is_array($field)) {
            $field = [$field];
        }

        if (is_scalar($options)) {
            $options = ['button' => $options];
        }
        $options += ['size' => 'sm', 'showText' => false];

        return $this->button($options, [], $field);
    }

    /**
     * extrait et recupere les options lié a un model
     * si le modle n'existe pas on considere que c'est du texte
     *
     * cas A : $this->Html->button('model')
     *
     * cas B : $this->Html->button('model',"text")
     *
     * cas C : $this->Html->button('model',[options])
     * /!\ si model est ecrit dans options il ignorera celui ecrit en premier parametre
     *
     * cas D : $this->Html->button([options])
     *
     * @param string|array $model model key
     * @param array|string $options options or text for button model
     * @param array $data
     * @return array options
     */
    protected function getButtonOptionsFromModel($model, $options, $data = [])
    {
        // normalisation des parametres
        if (is_array($model)) {
            // cas D
            $options = $model; //+ $options;
        } elseif (is_string($options)) {
            // cas B
            $options = [
                'button' => $model,
                'text' => $options
            ];
        } else {
            // cas A & C
            $options += ['button' => $model];
        }

        // while model is buttonsModel key and model != options[model]
        // si model existe et n'est pas false
        if (isset($options['button']) && $options['button'] && $this->getButtonModels($options['button'])) {
            // tant que model est set
            while (isset($options['button'])) {
                $currentModelOptions = $this->getButtonModels($options['button']);

                // si la valeur est string on replace dans model
                if (is_string($currentModelOptions)) {
                    $options['button'] = $currentModelOptions;
                } else {
                    if (isset($currentModelOptions['button']) && $currentModelOptions['button'] == $options['button']) {
                        $currentModelOptions['recursive'] = $currentModelOptions['button'];
                        unset($currentModelOptions['button']);
                    }
                    unset($options['button']);
                    $options += $currentModelOptions;
                }
            }
        }
        unset($options['button']);

        // autocomplete templating
        $options = \template($options, $data);
        return $options;
    }

    protected function getButtonSize($options)
    {
        $options += [
            'size' => false,
        ];

        // color
        if ($options['size']) {
            $options = $this->addClass($options, 'btn-' . $options['size']);
        }
        unset($options['size']);
        return $options;
    }

    /**
     * @param array $options options from button
     * @param array $linkOptions options for link
     * @return string mixed html string with link
     */
    protected function getButtonLink($options)
    {
        $linkOptions = [
            'role' => "button"
        ];

        // .class fusion
        $linkOptions = $this->addClass($linkOptions, Hash::get($options, 'class'));

        // convert icon + text in content
        list($content, $options) = $this->Html->getIconText($options);

        // add title
        //$linkOptions += ['title' => strip_tags($content)];

        // ce qui reste des options doit se retrouver dans le lien
        // incorporate options in linkOptions options.class was handleld before
        // linkify content

        if ($this->Html->isLinkExistInOptions($options)) {
            list($content, $options) = $this->linkify($content, $options, $linkOptions + $options);
        } else {
            $content = $this->Html->tag('button', $content, $options);
        }

        return $content;
    }

    protected function getButtonColor($options)
    {
        $options += [
            'color' => 'light',
        ];

        // color
        if ($options['color']) {
            $options = $this->addClass($options, 'btn btn-' . $options['color']);
        }
        unset($options['color']);
        return $options;
    }

    protected function getButtonModels($model = null)
    {
        //debug([$model,$this->Html->getCurrentContext(),$this->Html->getDefaultContext()]);
        $context = [];
        if ($this->Html->getCurrentContext() !== false && false !== strpos($this->Html->getCurrentContext(), '.')) {
            $context = array_combine(['plugin', 'controller'], explode('.', $this->Html->getCurrentContext()));
        }
        //debug($context);


        $buttonModels = [
            'primary' => ['color' => "primary"],
            'success' => ['color' => "success"],
            'danger' => ['color' => "danger"],
            'warning' => ['color' => "warning"],
            'info' => ['color' => "info"],
            'link' => ['color' => "link"],

            // crud
            'create' => [
                'icon' => "plus",
                'text' => "Ajouter",
                'button' => "success",
                'link' => [
                        'action' => 'add'
                    ] + $context
            ],
            'read' => [
                'icon' => "eye-open",
                'text' => "Detail",
                'button' => "info",
                'link' => [
                    [
                        'action' => 'view',
                        '{{0}}'
                    ] + $context
                ]
            ],
            'update' => [
                'icon' => "pencil",
                "text" => "Modifier",
                'button' => "warning",
                'link' => [['action' => 'edit', '{{0}}'] + $context],
            ],
            'delete' => [
                'icon' => "trash",
                'text' => "Supprimer",
                'button' => "danger",
                'postLink' => [
                    [
                        'action' => 'delete',
                        '{{0}}'
                    ] + $context,
                    [
                        'confirm' => "Are you sure to delete ?"
                    ]
                ],
            ],
            'list' => [
                'icon' => "list",
                'text' => "Liste",
                'color' => 'info',
                'link' => [
                    [
                        'action' => 'index'
                    ] + $context
                ]
            ],
            // alias
            'add' => "create",
            'edit' => "update",
            'view' => 'read',

            'button' => [
                'type' => 'button'
            ],
            'reset' => [
                'type' => 'reset',
                'text' => "Annuler",
                'icon' => 'refresh'
            ],
            'submit' => [
                'type' => 'submit',
                'button' => 'primary',
                'text' => 'Valider',
                'icon' => 'ok',
                'name' => 'submit',
                'value' => 1
            ],
            'export' => [
                'type' => 'submit',
                'icon' => 'export',
                'text' => 'Exporter',
                'button' => 'success',
                'name' => 'export',
                'value' => 1
            ],
            'search' => [
                'type' => 'submit',
                'icon' => 'search',
                'text' => 'Rechercher',
                'button' => 'info'
            ],
            'clear' => [
                'type' => 'button',
                'icon' => 'times',
                'text' => 'Vider',
                'data-clear-form' => true
            ],
            'moveUp' => [
                'icon' => 'chevron-up',
                'text' => 'Avant',
                'link' => [
                    [
                        'action' => 'moveUp',
                        '{{0}}'
                    ] + $context
                ]
            ],
            'moveDown' => [
                'icon' => 'chevron-down',
                'text' => 'Aprés',
                'link' => [
                    [
                        'action' => 'moveDown',
                        '{{0}}'
                    ] + $context
                ]
            ],
            'login' => [
                'icon' => 'sign-in-alt',
                'text' => __('Connexion'),
                'link' => [
                        [
                            'controller' => 'Users',
                            'action' => 'login'
                        ]
                    ] + $context,
                'isBlock' => true
            ],
            'logout' => [
                'icon' => 'sign-out-alt',
                'text' => __('Déconnexion'),
                'link' => [
                        [
                            'controller' => 'Users',
                            'action' => 'logout'
                        ]
                    ] + $context,
                'isBlock' => true
            ]
        ];


        if ($model !== null) {
            return Hash::get($buttonModels, $model, false);
        }
        return $buttonModels;
    }

}