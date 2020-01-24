<?php

namespace QuinenCake\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;

/**
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class Bs4Helper extends Helper
{
    use Bootstrap4Trait;

    public $helpers = ['Html', 'Form'];

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->Html->setDefaultContext(
            $this->getView()->getRequest()->getParam('plugin') . '.' .
            $this->getView()->getRequest()->getParam('controller')
        );
    }

    public function getMapFormat($map, $options)
    {
        if (Configure::read('debug')) {
            $map['label'][1] = $map['label'][1] +
                ['title' => $map['field'][0] . ':' . $map['format']];
        }

        if ($map['format'] === 'icon') {
            $map['field'][1] = $this->Html->addClass($map['field'][1], 'text-center');
        }

        return $this->Html->getMapFormat($map, $options);
    }

    public function getMapLabel($field, $options)
    {
        return $this->Html->getMapLabel($field, $options);
    }

    public function formatDatetime($value)
    {
        return $value;
    }

    public function formatDate($value)
    {
        return $value;
    }

    public function submitRow($submit = "Valider", $options = [])
    {
        $options += [
            'buttons' => [],
            'isMb3' => true,
            'isClear' => true,
            'isReset' => true,
            'colOptions' => [
                'class' => 'offset-md-2'
            ],
            'isRow' => true
        ];

        if (is_array($submit)) {
            $submitOptions = $submit;
        } else {
            $submitOptions = ['text' => $submit];
        }

        // cancel link if based on a model with it
        $submitOptions = $submitOptions + ['button' => 'submit', 'link' => false];

        $buttons = [];
        if ($options['isClear']) {
            $buttons[] = $this->button('clear');
        }

        if ($options['isReset']) {
            $buttons[] = $this->button('reset');
        }

        $buttons[] = $this->button($submitOptions);


        $htmlButtons = implode('&nbsp;', array_merge($buttons, $options['buttons']));

        $colOptions = $this->Html->addClass($options['colOptions'], 'col' . ($options['isMb3'] ? ' mb-3' : ''));

        $row = $this->Html->tag('div', $htmlButtons, $colOptions);

        if ($options['isRow']) {
            $this->Html->div('row', $row);
        }

        return $row;
    }
}
