<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 02/11/18
 * Time: 09:23
 */

$cols = [2, 10];
$colLabel = 'col-xs-12 col-sm-' . $cols[0] . ' col-md-' . $cols[0] . ' col-lg-' . $cols[0] . '';
$colLabelOffset = 'col-sm-offset-' . $cols[0] . '';
$colControl = 'col-xs-12 col-sm-' . $cols[1] . ' col-md-' . $cols[1] . ' col-lg-' . $cols[1] . '';
return [
    // form
    'formStart' => '<form class="form-horizontal {{attrs.class}}" {{attrs}}>',

    // global
    'inputContainer' => '<div class="form-group{{required}}">{{content}}</div>',
    'formGroup' => '{{label}}<div class="' . $colControl . '">{{input}}</div>',
    'label' => '<label class="' . $colLabel . ' control-label{{attrs.class}}"{{attrs}}>{{text}}</label>',
    'input' => '<input type="{{type}}" name="{{name}}" class="form-control{{attrs.class}}" autocomplete="new-password" {{attrs}}/>',

    // select
    'select' => '<select name="{{name}}" class="form-control{{attrs.class}}"{{attrs}}>{{content}}</select>',
    'selectMultiple' => '<select name="{{name}}[]" multiple="multiple" class="form-control{{attrs.class}}"{{attrs}}>{{content}}</select>',

    // checkbox & radio
    'nestingLabel' => '{{hidden}}<label {{attrs}}>{{input}}{{text}}</label>',
    // checkbox
    'checkboxContainer' => '<div class="form-group{{required}}"><div class="' . $colLabelOffset . ' ' . $colControl . '">{{content}}</div></div>',
    'checkboxFormGroup' => '<div class="checkbox">{{label}}</div>',
    // radio
    'radioFormGroup' => '{{label}}<div class="' . $colControl . '"><div class="row">{{input}}</div></div>',
    'radio' => '<div class="col-md-1"><input type="radio" name="{{name}}" value="{{value}}"{{attrs}}></div>&nbsp;',
    // textarea
    'textarea' => '<textarea name="{{name}}" class="form-control{{attrs.class}}" {{attrs}}>{{value}}</textarea>',

    // buttons
    'inputSubmit' => '<input type="{{type}}" class="btn btn-primary {{attrs.class}}"{{attrs}}/>',
    'submitContainer' => '{{content}}',
    'button' => '<button class="btn btn-default {{attrs.class}}"{{attrs}}>{{text}}</button>',

    // widgets
    //'dateWidget' => '<div class="form-inline">{{day}}{{month}}{{year}}</div>',
    //'dateTextWidget' => '<input type="date" name="{{name}}" class="form-control{{attrs.class}}"
    // autocomplete="new-password" {{attrs}}/>'
    // 'dateWidget' => '<div class="inline"><div class="col-xs-1">{{day}}</div><div class="col-xs-2">{{month}}</div><div class="col-xs-2">{{year}}</div></div>'
    'period' => '<div class="form-inline">{{dateStart}}{{dateEnd}} <label class="control-label">inclus</label></div>'
];