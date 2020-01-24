<?php

return [
    'period' => '<div class="form-inline">' .
        /*
                '<div class="col-5">{{dateStart}}</div>' .
                '<div class="col-5">{{dateEnd}}</div>' .
                '<label class="col-form-label col-2">&nbsp;inclus</label>' .
         */
        //'<div class="row">' .
        '{{dateStart}}' .
        '{{dateEnd}}' .
        '<div class="col">inclus</div>' .
        //'</div>' .
        '</div>',
    'dateWidget' => '{{day}}{{month}}{{year}}{{hour}}{{minute}}{{second}}{{meridian}}',
    'label' => '<label class="col-form-label col-2 {{attrs.class}}"{{attrs}}>{{text}}</label>',

    // container around label + input , {{type}} exist also
    'inputContainer' => '<div class="form-group form-row {{required}}">{{content}}</div>',
    'input' => '<input type="{{type}}" name="{{name}}" class="form-control col {{attrs.class}}"{{attrs}}/>',
    'select' => '<select name="{{name}}" class="form-control col {{attrs.class}}"{{attrs}}>{{content}}</select>',
    'nestingLabel' => '{{hidden}}' .
        '<div class="form-check">{{input}}' .
        '<label class="form-check-label mr-3 mt-1 {{attrs.class}}" {{attrs}}>{{text}}</label>' .
        '</div>',
    'radio' => '<input type="radio" name="{{name}}" value="{{value}}" class="form-check-input {{attrs.class}}"{{attrs}}>',
    'textarea' => '<textarea name="{{name}}" class="col form-control {{attrs.class}}"{{attrs}}>{{value}}</textarea>',
    'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}" class="form-check-input {{attrs.class}}"{{attrs}}>',
    'checkboxFormGroup' => '<div class="offset-2">{{label}}</div>',
    // errors
    'inputContainerError' => '{{error}}<div class="form-group form-row {{required}}">{{content}}</div>',
    'error' => '<div class="alert alert-danger">{{content}}</div>',

];