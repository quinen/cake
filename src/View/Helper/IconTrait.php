<?php
/**
 * @author Laurent DESMONCEAUX <laurent@quinen.net>
 * @created 31/08/2018
 * aller plus loin que https://github.com/quinen/cakephp3-plugin/blob/dev/src/Template/Element/Pages/icons.ctp
 * penser a autoswitcher le style en fonction d ela valeur soumise
 * @version 1.0
 */

namespace QuinenCake\View\Helper;

use Cake\ORM\Entity;

/**
 *
 */
trait IconTrait
{
    public function iconText($icon = false, $text = false, $options = [])
    {
        $options += [
            'icon' => $icon,
            'text' => $text
        ];

        list($iconText, $spanOptions) = $this->getIconText($options);
        return $this->tag('span', $iconText, $spanOptions);
    }

    /**
     * @param array|string $options
     * @return array
     */
    public function getIconText($options = [])
    {

        if (\is_string($options)) {
            $options = ['text' => $options];
        } else if($options instanceof Entity){
            $options = $options->toArray();
        }

        $optionsDefaults = [
            'showIcon' => true,
            'icon' => false,
            'showText' => true,
            'text' => false,
            'isTitle' => true,
            'template' => '{{icon}} {{text}}'
        ];

        $options += $optionsDefaults;

        $iconText = [];

        if ($options['showIcon'] && $options['icon']) {
            list($icon, $iconOptions) = $this->getContentOptions($options['icon']);
            $iconText['icon'] = $this->icon($icon, $iconOptions);
        }

        $text = $options['text'];
        if ($text !== false && $options['showText']) {

            list($text, $textOptions) = $this->getContentOptions($text);

            if (empty($textOptions)) {
                $iconText['text'] = $text;
            } else {
                $iconText['text'] = $this->tag('span', $text, $textOptions);
            }
        }

        if ($options['isTitle'] && $options['text']) {
            // menu auth a vide durant test^M
            if (is_array($text)) {
                $text = $text[0];
            }

            $options += ['title' => strip_tags($text)];
        }

        $template = $options['template'];
        // strip options from defaults key
        $options = array_diff_key($options, $optionsDefaults);

        return [trim(\template($template, $iconText)), $options];
    }
}
