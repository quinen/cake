<?php

namespace QuinenCake\View\Helper;

trait Bootstrap3Trait
{
    use BootstrapTrait {
        table as bootstrapTable;
    }

    use GlyphiconTrait {
        glyphicon as icon;
    }

    use Bs3ButtonTrait;

    protected $tableBooleanOptions = [
        ''
    ];

    public function getMapLabel($field)
    {
        // TODO: Implement getMapLabel() method.
        return $field;
    }

    public function getMapFormat($map)
    {
        // TODO: Implement getMapFormat() method.
        return $map;
    }

    /**
     * @param $value
     * @param array $options
     * @param array $data
     * @return mixed
     */
    public function boolean($value, $options = [], $data = [])
    {

        list($true, $trueOptions) = $this->Html->getIconText([
            'icon' => 'ok',
            'text' => 'Oui',
            'showText' => false
        ]);

        list($false, $falseOptions) = $this->Html->getIconText([
            'icon' => 'remove',
            'text' => 'Non',
            'showText' => false
        ]);

        $options += [
            'valueTrue' => $this->label('success', '&nbsp;' . $true, $trueOptions),
            'valueFalse' => $this->label('danger', '&nbsp;' . $false, $falseOptions),
        ];
        return $this->formatBoolean($value, $options, $data);
    }

    public function booleanOrNull($value, $options = [], $data = [])
    {
        list($null, $nullOptions) = $this->Html->getIconText([
            'icon' => 'question-sign',
            'text' => 'Inconnu',
            'showText' => false
        ]);

        $options += [
            'callback' => false,
            'valueNull' => $this->label('info', '&nbsp;' . $null, $nullOptions)
        ];

        if ($options['callback']) {
            $value = $options['callback']($value);
            $options['callback'] = false;
        }

        if ($value === null) {
            return $options['valueNull'];
        }

        return $this->boolean($value, $options, $data);
    }

    public function table($datas, $maps = [], $options = [])
    {
        $options += [
            'isCondensed' => true,
        ];

        $options = $this->addClassFromBooleanOptions(
            $options,
            ['condensed','responsive'],
            ['prefix' => "table"]
        );

        return $this->bootstrapTable($datas, $maps, $options);
    }
/*
    public function getTablePaginator($options = [])
    {
        $options += [
            'format' => ' {{start}}-{{end}} de {{count}} résultats'
        ];

        $paginator = $this->getView()->Paginator;

        return $this->Html->div('row', $this->Html->div('col-xs-9', $this->Html->tag('ul',
                $paginator->prev($this->icon('chevron-left'), ['escape' => false]) .
                $this->button([
                    'button' => 'default',
                    'text' => $paginator->counter(['format' => $options['format']], ['escape' => false]),
                    'isDisabled' => true,
                    'class' => 'disabled'
                ]) .
                $paginator->next($this->icon('chevron-right'), ['escape' => false]) .
                '',
                [
                    'class' => "list-inline"
                ]
            )) . $this->Html->div('col-xs-3',
                $paginator->limitControl([
                    //5 => 5, 20 => 20,
                ], null, [
                    'label' => false,
                    'templates' => [
                        'selectFormGroup' => '<div class="col-xs-12">{{input}}</div>',
                    ]
                    ,
                    'empty' => "Resultats / pages"
                ])
            )
        );
    }
*/
    public function label($model = false, $content, $options = [])
    {

        $modelOptions = [
            'default',
            'primary',
            'success',
            'info',
            'warning',
            'danger'
        ];

        $options += [
            'button' => 'default'
        ];

        if (!$model || !in_array($model, $modelOptions)) {
            $model = $options['model'];
        }
        unset($options['model']);

        $options = $this->addClass($options, 'label label-' . $model);

        return $this->Html->tag('span', $content, $options);


    }

    /**
     * @param $body
     * @param array $options
     * @return mixed
     */
    public function panel($body, $options = [])
    {
        $panelColors = ['default', 'primary', 'success', 'info', 'warning', 'danger'];

        $options += [
            'color' => 'default',
            'header' => false,
            'title' => false,
            'body' => $body,
            'content' => false,
            'footer' => false,
            'buttons' => []
        ];

        // color
        if (!in_array($options['color'], $panelColors)) {
            $options['color'] = $panelColors[0];
        }

        if (!$options['header']) {
            $options['header'] = [];

            if ($options['title']) {
                $options['header'][] = $this->Html->tag('div', $options['title'], [
                    'class' => 'panel-title pull-left'
                ]);
            }

            if ($options['buttons']) {
                $options['header'][] = $this->buttons($options['buttons'], ['class' => "pull-right btn-group-sm"]);
            }

            $options['header'] = implode($options['header']);
        }
        $header = false;
        if ($options['header']) {
            $header = $this->Html->div('panel-heading clearfix', $options['header']);
        }

        $body = false;
        if ($options['body']) {
            $body = $this->Html->div('panel-body', $options['body']);
        }

        $content = $options['content'];

        $footer = false;
        if ($options['footer']) {
            $footer = $this->Html->div('panel-footer', $options['footer']);
        }

        return $this->Html->div('panel panel-' . $options['color'], $header . $body . $content . $footer);
    }

    public function tableInPanel($datas, $maps, $optionsTable = [], $optionsPanel = [])
    {
        return $this->panel(false, [
                'content' => $this->table($datas, $maps, $optionsTable)
            ]
            + $optionsPanel
        );
    }
}
