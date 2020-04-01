<?php

namespace QuinenCake\View\Helper;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\View\Helper\FormHelper as BaseHelper;


/**
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class FormHelper extends BaseHelper
{

    public function period($fieldName, $options)
    {
        $options = [
                'type' => 'date',
                'templates' => [
                    'inputContainer' => '<div class="form-group {{required}}">{{content}}</div>',
                    'label' => '<label class="col-form-label col {{attrs.class}}"{{attrs}}>{{text}}</label>',
                    'select' => '<select name="{{name}}" class="form-control {{attrs.class}}"{{attrs}}>{{content}}</select>',
                ]
            ] + $options + [
                'suffix' => ['-start', '-end'],
                'default' => [null, null]
            ];

        $suffix = $options['suffix'];
        unset($options['suffix']);

        $defaults = $options['default'];
        unset($options['default']);

        // specifique
        $optionsStart = [
                'id' => $options['id'] . $suffix[0],
                'label' => "du",
            ] + $options + [
                'default' => $this->getSourceValue($fieldName . $suffix[0], ['default' => $defaults[0]])
            ];

        $optionsEnd = [
                'id' => $options['id'] . $suffix[1],
                'label' => "au",
            ] + $options + [
                'default' => $this->getSourceValue($fieldName . $suffix[1], ['default' => $defaults[1]])
            ];

        return $this->formatTemplate('period', [
            'dateStart' => $this->control($fieldName . $suffix[0], $optionsStart),
            'dateEnd' => $this->control($fieldName . $suffix[1], $optionsEnd)
        ]);
    }

    public function control($fieldName, array $options = [])
    {
        $options += [
            'autocomplete' => "new-password"
        ];

        if (Configure::read('debug')) {
            unset($options['autocomplete']);
        }

        $options = $this->handlePlaceholder($options);
        $options = $this->handleInputGroup($options);

        return parent::control($fieldName, $options);
    }

    private function handlePlaceholder(array $options)
    {
        $options += [
            'placeholder' => true,
        ];

        if ($options['placeholder'] === true) {
            if (isset($options['label'])) {
                $options['placeholder'] = $options['label'];
            } else {
                unset($options['placeholder']);
            }
        }

        return $options;
    }

    /**
     * @param $fieldName
     * @param array $options
     * @return mixed
     */
    public function date($fieldName, array $options = [])
    {
        $options = [
                'label' => false
            ] + $options;

        $optionsTextId = $options['id'] . '-p';
        $fieldNameText = $fieldName . '-p';

        // champ factice
        $optionsText = [
                'type' => 'text',
                'data-toggle' => 'datetimepicker',
                'data-target' => '#' . $optionsTextId,
                'data-date' => '#' . $options['id'],
                'id' => $optionsTextId,
            ] + $options;

        // vrai champ envoyé au formulaire
        $optionsHidden = [
                'type' => 'date',
                'hidden' => true
            ] + $options;


        // renvoi un champ date fr + champ hidden avec valeur yyyy-mm-dd
        return $this->control($fieldNameText, $optionsText) .
            parent::date($fieldName, $optionsHidden);
    }

    public function booleanSearch($fieldName, array $options = [])
    {
        //debug([$fieldName, $options]);

        unset($options['type']);
        if (!isset($options['options'])) {
            $options['options'] = [
                '0' => 'Non',
                '1' => 'Oui',
            ];
        }

        $options += [
            'type' => 'select',
            'empty' => "*",
            'templates' => [
                'inputContainer' => '<div class="{{required}}">{{content}}</div>',
            ]
        ];
        //debug([$fieldName, $options]);
        return $this->control($fieldName, $options);
    }

    private function handleInputGroup(array $options)
    {
        $options += [
            'prepend' => false,
            'append' => false
        ];

        if ($options['prepend'] || $options['append']) {

            $prepend = $append = false;

            // on va manipuler le template formGroup
            $templateFormGroupKey = 'templates.formGroup';
            $templateFormGroup = Hash::get($options, $templateFormGroupKey, $this->getTemplates('formGroup'));

            if($options['prepend']){
                $prepend = $this->Html->div('input-group-prepend',$options['prepend']);
            }

            if($options['append']){
                $append = $this->Html->div('input-group-append',$options['append']);
            }

            $input = '{{input}}';

            $inputGroup = template('{{prepend}}{{input}}{{append}}',compact('prepend','append','input'));
            $inputGroup  = $this->Html->div('input-group', $inputGroup);
            $templateFormGroup = str_replace('{{input}}',$inputGroup,$templateFormGroup);
            $options = Hash::insert($options, $templateFormGroupKey, $templateFormGroup);
        }

        return $options;
    }


}

