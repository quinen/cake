<?php

namespace QuinenCake\View\Helper;

trait Bootstrap4Trait
{
    use BootstrapTrait {
        table as bootstrapTable;
    }
    // UI elements
    use Bs4ButtonTrait;
    use Bs4CardTrait;
    use Bs4DropdownTrait;
    use Bs4NavTrait;
    use Bs4NavbarTrait;

    public function row($row, $options = [])
    {
        $html = implode(PHP_EOL, collection($row)->map(function ($col) {
            list($col, $colOptions) = $this->getContentOptions($col);
            $colOptions = $this->Html->addClass($colOptions, 'col');
            return $this->Html->tag('div', $col, $colOptions);
        })->toArray());

        $options = $this->Html->addClass($options, 'row');

        return $this->Html->tag('div', $html, $options);
    }

    public function table($datas, $maps = [], $options = [])
    {
        $options += [
            'size' => 'xs', // xs, sm
        ];

        if ($options['size']) {
            $options['is' . ucfirst($options['size'])] = true;
        }
        unset($options['size']);

        $options = $this->addClassFromBooleanOptions(
            $options,
            ['sm', 'dark', 'xs'],
            ['prefix' => "table"]
        );
        /*
                $maps = collection($maps)->map(function($map){
                    return $this->addClass($map,'col');
                })->toArray();
        */
        return $this->bootstrapTable($datas, $maps, $options);
    }

    public function formatBoolean($value, $options = [])
    {
        $options += [
            'valueTrue' => $this->valueTrue(),
            'valueFalse' => $this->valueFalse(),
        ];

        return $this->Html->formatBoolean($value, $options);
    }

    public function valueTrue($text = 'Oui', $options = [])
    {
        return $this->badge('success', $this->Html->iconText('check', $text), $options);
    }

    public function badge($model = false, $content = null, $options = [])
    {

        $modelOptions = [
            'primary',
            'secondary',
            'success',
            'info',
            'warning',
            'danger',
            'light',
            'dark'
        ];

        $options += [
            'badge' => 'light',
            'isPill' => false,
            'tag' => 'h6',
        ];

        if (!$model || !in_array($model, $modelOptions)) {
            $model = $options['badge'];
        }
        unset($options['badge']);

        $options = $this->addClass($options, 'mx-auto badge badge-' . $model);

        /** @var array $options */
        $options = $this->addClassFromBooleanOptions(
            $options,
            ['pill'],
            [
                'prefix' => 'badge'
            ]
        );

        list($tag,$tagOptions) = $this->getContentOptions($options['tag']);
        unset($options['tag']);

        $html = $this->Html->tag('span', $content, $options);

        if ($tag) {
            $html = $this->Html->tag($tag, $html,$tagOptions);
        }

        return $html;


    }

    public function valueFalse($text = 'Non', $options = [])
    {
        return $this->badge('danger', $this->Html->iconText('times', $text), $options);
    }

    public function formatIcon($value)
    {
        return $this->Html->icon($value);
    }

    public function dl($data, $maps, $options = [])
    {
        $optionsDefault = [
            'mapCallbackClass' => $this,
            'nbCols' => 4,
            'tags' => ['dd', 'dt'],
            'labelClass' => false,
            'fieldClass' => 'border-bottom',

            // valeurs ecrasées par calcul
            'colBase' => null,
            'colBaseClass' => null
        ];

        $options += $optionsDefault + [
                'class' => 'row'
            ];

        // normalize mapping
        $mapsOptions = [
            'callbackClass' => $options['mapCallbackClass']
        ];

        $maps = $this->normalizeMaps($maps, $data, $mapsOptions);

        // transform data
        $line = $this->transformMapsWithLine($maps, $data);

        $options['colBase'] = 12 / ($options['nbCols'] * 2);
        $options['colBaseClass'] = 'col-' . strtr($options['colBase'], ['.' => '-']);

        // gestion colspan
        $array = collection($maps)->reduce(function ($reducer, $map, $index) use ($line, $options) {

            $options = $this->Html->addClass($options, $options['colBaseClass'], 'labelClass');

            if (isset($map['colspan']) && $map['colspan']) {
                // calcul pour n colonnes de field
                $colField = $options['colBase'] * ($map['colspan'] * 2 - 1);
                $options = $this->Html->addClass($options, 'col-' . strtr($colField, ['.' => '-']), 'fieldClass');
            } else {
                $options = $this->Html->addClass($options, $options['colBaseClass'], 'fieldClass');
                $map['colspan'] = 1;
            }

            $map['label'][1] = $this->addClass($map['label'][1], $options['labelClass']);
            $line[$index][1] = $this->addClass($line[$index][1], $options['fieldClass']);

            $dt = $this->Html->tag($options['tags'][0], $map['label'][0], $map['label'][1]);
            $dd = $this->Html->tag($options['tags'][1], $line[$index][0] . '&nbsp;', $line[$index][1]);

            $reducer['html'][] = $dt . $dd;
            $reducer['count'] += $map['colspan'];

            if ($reducer['count'] % $options['nbCols'] == 0) {
                $reducer['html'][] = $this->Html->div('w-100', '');
            }

            return $reducer;
        }, ['count' => 0, 'html' => []]);

        $html = implode($array['html']);

        $options = array_diff_key($options, $optionsDefault);

        return $this->Html->tag('dl', $html, $options);
    }

    public function tableInCard($datas, $maps, $optionsTable = [], $optionsCard = [])
    {
        return $this->card(
            [
                'content' => $this->table($datas, $maps, $optionsTable)
            ] + $optionsCard
        );
    }

    public function formatBooleanOrNull($value, $options = [])
    {
        $options += [
            'callback' => false,
            'valueNull' => $this->valueNull()
        ];

        if ($options['callback']) {
            $value = $options['callback']($value);
            $options['callback'] = false;
        }

        if ($value === null) {
            return $options['valueNull'];
        }

        return $this->formatBoolean($value, $options);

    }

    public function valueNull($text = 'Inconnu')
    {
        return $this->badge('info', $this->Html->iconText('question', $text));
    }

    public function progress($bars, $options = [])
    {
        $options += [
            'showPct' => true
        ];

        $progressBars = collection($bars)->map(function ($bar) use ($options) {
            list($bar, $barOptions) = $this->getContentOptions($bar);
            $barOptions += [
                'role' => 'progressbar',
                'width' => 100, // value in pct
                'color' => false,
                'isStriped' => false,
            ];

            $pct = round($barOptions['width'], 1) . '%';

            // style
            $barOptions['style'] = [];
            if ($barOptions['width']) {
                $barOptions['style'][] = 'width:' . $pct;
            }
            unset($barOptions['width']);

            // class
            $class = 'progress-bar';

            // bg-color
            if ($barOptions['color']) {
                $class .= ' bg-' . $barOptions['color'];
            }
            unset($barOptions['color']);

            // striped
            if ($barOptions['isStriped']) {
                $class .= ' progress-bar-striped';
            }
            unset($barOptions['isStriped']);

            if ($options['showPct']) {
                $bar .= '<h6>' . $this->badge('light', $pct) . '</h6>';
            }

            return $this->Html->div($class, $bar, $barOptions);
        })->toArray();

        $options['style'] = [];
        if ($options['showPct']) {
            $options['style'][] = 'height:4em';
        }
        unset($options['showPct']);

        return $this->Html->div('progress', $progressBars, $options);
    }

    public function getCurrentContext()
    {
        return $this->Html->getCurrentContext();
    }

    public function setCurrentContext($context)
    {
        return $this->Html->setCurrentContext($context);
    }

    public function listgroup($list, $options = [], $optionsItem = [])
    {
        $options = $this->addClass($options, 'list-group');
        $optionsItem = $this->addClass($optionsItem, 'list-group-item');
        return $this->Html->list($list, $options, $optionsItem);
    }

    public function modal($body = false, $options = [])
    {
        $optionsDefaults = [
            'title' => false,
            'header' => false,
            'body' => $body,
            'footer' => false,
            'buttons' => [],
            'isFade' => true,
            'isCentered' => true,
            'isClose' => true,
            'size' => false,
        ];

        $options += $optionsDefaults;

        // header   ////////////////////////////////////////////////////////////
        $title = false;
        if ($options['title']) {
            $title = $this->Html->tag('h5', $options['title'], ['class' => 'modal-title']);
        }

        $header = false;
        if ($options['header'] || $title) {
            $header = $this->Html->div('modal-header', $title . $options['header']);
        }

        // footer   ////////////////////////////////////////////////////////////
        $buttons = $options['buttons'];
        if ($options['isClose']) {
            $buttons[] = $this->button('light', [
                'icon' => 'times',
                'text' => __('Fermer'),
                'data-dismiss' => 'modal'
            ]);
        }


        $footer = false;
        if ($options['footer'] || !empty($buttons)) {
            $footer = $this->Html->div('modal-footer', $options['footer'] . implode($buttons));
        }

        // body ////////////////////////////////////////////////////////////////
        $body = false;
        if ($options['body']) {
            $body = $this->Html->div('modal-body', $options['body']);
        }

        $content = $this->Html->div('modal-content', $header . $body . $footer);
        $isCentered = $options['isCentered'];

        $dialogClass = '';
        $dialogClass .= ($isCentered ? ' modal-dialog-centered' : '');
        $dialogClass .= ($options['size'] ? ' modal-' . $options['size'] : '');

        $dialog = $this->Html->div(
            'modal-dialog' . $dialogClass,
            $content,
            ['role' => 'document']);

        $isFade = $options['isFade'];
        unset($options['isFade']);

        $options += [
            'tabindex' => '-1',
            'role' => 'dialog',
            'aria-hidden' => true
        ];

        $options = array_diff_key($options, $optionsDefaults);

        return $this->Html->div('modal' . ($isFade ? ' fade' : ''), $dialog, $options);
    }

}
