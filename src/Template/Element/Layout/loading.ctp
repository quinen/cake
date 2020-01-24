<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/21/19
 * Time: 6:07 PM
 */
/* @var \App\View\AppView $this */

//  <div class="spinner-border"><span class="sr-only">Loading...</span></div>
$loading = $this->Html->div(
    'd-flex justify-content-center',
    str_repeat($this->Html->div(
        'spinner-grow',
        '',
        [
            'style' => [
                'width:5rem;',
                'height:5rem'
            ]
        ]
    ), 3)
);

echo $this->Bs4->modal(
    $loading
    , [
        'id' => 'loadingModal',
        'title' => __('Chargement ...'),
        'isFade' => false,
        'isClose' => false,
        'style' => [
            'z-index:1051;'
        ]
    ]
);