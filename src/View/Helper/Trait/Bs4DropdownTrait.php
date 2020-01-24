<?php
/**
 * @author Laurent DESMONCEAUX <laurent@quinen.net>
 * @created 31/08/2018
 * aller plus loin que https://github.com/quinen/cakephp3-plugin/blob/dev/src/Template/Element/Pages/icons.ctp
 * penser a autoswitcher le style en fonction d ela valeur soumise
 * @version 1.0
 */

namespace QuinenCake\View\Helper;

use Cake\Utility\Hash;

/**
 *
 */
trait Bs4DropdownTrait
{
    /**
     *
     * example from navbar : $element =  $this->dropdown(
     * [false,['text'=>$element,'class'=>"nav-link"]],
     * $elementOptions['list'],
     * ['inDiv'=>false]
     * );
     *
     * @param $button
     * @param $list
     * @param array $options
     * @return string
     */
    public function dropdown($button, $list, $options = [])
    {
        $options += [
            'inDiv' => true,
            'class' => 'dropdown'
        ];

        $dropdown = [];

        // button
        list($button, $buttonOptions) = $this->getContentOptions($button);

        $buttonOptions += [
            'data-toggle' => "dropdown"
        ];

        $buttonOptions = $this->addClass($buttonOptions, 'dropdown-toggle');
        $dropdown[] = $this->button($button, $buttonOptions);

        // list
        list($list, $listOptions) = $this->getContentOptions([$list]);
        $dropdown[] = $this->dropdownList($list, $listOptions);

        $content = implode($dropdown);
        if ($options['inDiv']) {
            $content = $this->Html->div($options['class'], $content);
        }
        return $content;
    }

    // comme list mais avec un div et des links
    private function dropdownList($list, $options = [])
    {
        $element = implode(collection($list)->map(function ($element) {
            // standardisation
            list($element, $elementOptions) = $this->getContentOptions($element);

            if (is_array($element) && empty($elementOptions)) {
                $elementOptions = $element;
                $element = false;
            }

            list($element, $elementOptions) = $this->Html->getIconText($elementOptions + ['text' => $element]);

            if ('-' === $element) {
                $element = $this->Html->div('dropdown-divider', "");
            } elseif (isset($elementOptions['list'])) {
                $element = $this->dropdown(
                    [
                        false,
                        [
                            'text' => $element,
                            'class' => "dropdown-item",
                            'link' => '#'
                        ]
                    ],
                    $elementOptions['list'],
                    ['class' => 'dropdown-submenu']
                );
            } else {
                if (is_array($element)) {
                    list($element, $elementOptions) = $this->Html->getIconText($element);
                }
                list($element) = $this->linkify($element, $elementOptions, ['class' => "dropdown-item"]);
            }

            return $element;
        })->toArray());

        // tag
        $tag = Hash::get($options, 'tag', 'div');
        unset($options['tag']);

        // class
        $options = $this->addClass($options, "dropdown-menu");

        // list
        return $this->Html->tag($tag, $element, $options);
    }
}
