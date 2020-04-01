<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 06/12/18
 * Time: 13:30
 */

namespace QuinenCake\Controller;

use QuinenCake\View\Helper\AuthHelper;
use QuinenCake\View\Helper\Bs4Helper;
use QuinenCake\View\Helper\FormHelper;
use QuinenCake\View\Helper\HtmlHelper;
use QuinenCake\View\Helper\PaginatorHelper;
use QuinenCake\View\Helper\UiHelper;
use QuinenCake\View\Widget\DateTimeWidget;

/** @var \App\Controller\AppController $this */
trait InitTrait
{
    public function initBs4Helpers(array $options = [])
    {
        $options += [
            'bs4' => Bs4Helper::class
        ];
        $this->viewBuilder()->setHelpers([
            'Breadcrumbs' => [
                'templates' => 'QuinenCake.Helper/breadcrumbs_bootstrap4'
            ],
            'Bs4' => ['className' => $options['bs4']],
            'Form' => [
                'className' => FormHelper::class,
                'templates' => 'QuinenCake.Helper/form_bootstrap4',

                'widgets' => [
                    'datetime' => [DateTimeWidget::class, 'select'],
                ],
            ],
            'Paginator' => [
                'className' => PaginatorHelper::class,
                'templates' => 'QuinenCake.Helper/paginator_bootstrap4'
            ],
        ]);
    }

    public function initUiHelpers($options = [])
    {
        $options += [
            'auth' => AuthHelper::class,
            'html' => HtmlHelper::class,
        ];

        if($options['auth']){
            $this->viewBuilder()->setHelpers(['Auth'=>['className' => $options['auth']]]);
        }

        $this->viewBuilder()->setHelpers([
            //'Auth' => ['className' => $options['auth']],
            'Html' => ['className' => $options['html']],
            'Ui' => ['className' => UiHelper::class],
        ]);
    }
}