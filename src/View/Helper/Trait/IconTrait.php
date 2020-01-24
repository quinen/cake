<?php
/**
 * @author Laurent DESMONCEAUX <laurent@quinen.net>
 * @created 31/08/2018
 * aller plus loin que https://github.com/quinen/cakephp3-plugin/blob/dev/src/Template/Element/Pages/icons.ctp
 * penser a autoswitcher le style en fonction d ela valeur soumise
 * @version 1.0
 */

namespace QuinenCake\View\Helper;

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
        return $this->getIconText($options)[0];
    }

    /**
     * @param array|string $options
     * @return array
     */
    public function getIconText($options = [])
    {
        /*
        if(isset($options['icon']) && $options['icon']=='file-export'){
            debug($options);
        }
        */

        if (\is_string($options)) {
            $options = ['text' => $options];
        }

        $optionsDefaults = [
            'showIcon' => true,
            'icon' => false,
            'showText' => true,
            'text' => false,
            'preText' => false,
            'isAfter' => false,
        ];

        $options += $optionsDefaults;

        $iconText = [];

        if ($options['showIcon'] && $options['icon']) {
            list($icon, $iconOptions) = $this->getContentOptions($options['icon']);
            $iconText[] = $this->icon($icon, $iconOptions);
        }

        $text = $options['text'];
        if ($text !== false && $options['showText']) {

            if ($options['preText']) {
                $iconText[] = $options['preText'];
            }

            list($text, $textOptions) = $this->getContentOptions($text);

            if (empty($textOptions)) {
                $iconText[] = $text;
            } else {
                $iconText[] = $this->tag('span', $text, $textOptions);
            }
        }

        if ($options['isAfter']) {
            $iconText[] = array_shift($iconText);
        }

        if ($options['text']) {
            $options += ['title' => strip_tags($text)];
        }

        // strip options from defaults key
        $options = array_diff_key($options, $optionsDefaults);

        return [implode('&nbsp;', $iconText), $options];
    }
}
