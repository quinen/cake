<?php

namespace QuinenCake\View\Helper;

use QuinenLib\Tools;
use Cake\Utility\Inflector;
use Cake\Utility\Text;

trait LinkTrait
{
    public $linkDefaults = [
        'ajaxLink' => false,
        'link' => false,
        'modalLink' => false,
        'postLink' => false,
        'tabLink' => false,
        'trLink' => false,
    ];

    public function linkify($content, array $options = [], array $injectLinkOptions = [])
    {
        $linkCallback = [
            'link' => [$this->getView()->Html, 'link'],
            'postLink' => [$this->getView()->Form, 'postLink']
        ];

        $ajaxLinks = ['modal', 'tab', 'tr'];

        $options += $this->linkDefaults + [];

        foreach ($ajaxLinks as $ajaxKey) {
            $linkAsKey = 'linkAs' . ucfirst($ajaxKey);
            $linkKey = $ajaxKey . 'Link';

            // linkAsType
            if (isset($options[$linkAsKey]) && $options[$linkAsKey]) {
                $options[$linkKey] = $options['link'];
                unset($options['link']);
            }
            unset($options[$linkAsKey]);
            unset($injectLinkOptions[$linkAsKey]);


            // typeLink > ajaxLink
            if ($options[$linkKey]) {
                $options = $this->{$ajaxKey . 'LinkToAjaxLink'}($options);
                unset($injectLinkOptions[$linkKey]);
            }
        }

        // ajaxLink => link
        if ($options['ajaxLink']) {
            $options = $this->ajaxLinkToLink($options);
            unset($injectLinkOptions['ajaxLink']);
        }

        // postLink
        $key = false;
        if ($options['postLink']) {
            $key = 'postLink';
        } else {
            if ($options['link']) {
                $key = 'link';
            }
        }

        if ($key) {
            // remove copies in injection
            unset($injectLinkOptions[$key]);

            // take the link
            list($linkContent, $linkOptions) = $this->getContentOptions($options[$key]);

            // eliminate the key no longer useful
            unset($options[$key]);

            // class merge specific
            if (isset($injectLinkOptions['class'])) {
                $linkOptions = $this->addClass($linkOptions, $injectLinkOptions['class']);
                unset($injectLinkOptions['class']);
            }

            // inject before link for surcharge, ex : confirm on postLink
            $linkOptions = $injectLinkOptions + $linkOptions + ['escape' => false];

            if ($linkContent === false) {
                $content = $this->tag(
                    'a',
                    $content,
                    $linkOptions
                );
            } else {
                if ($this->getView()->Auth->check($linkContent) ||
                    (isset($linkContent['_access']) && $linkContent['_access'])
                ) {
                    if (is_array($linkContent)) {
                        unset($linkContent['_access']);
                    }

                    $content = \call_user_func(
                        $linkCallback[$key],
                        $content,
                        $linkContent,
                        $linkOptions
                    );
                } else {
                    $content = null;
                }
            }
        }

        $options = array_diff_key($options, $this->linkDefaults);

        return [$content, $options];
    }

    public function ajaxLinkToLink($options = [])
    {
        list($ajax, $ajaxOptions) = $this->getContentOptions($options['ajaxLink']);


        $ajaxOptions += [
            'id' => Text::uuid(),
            'url' => $this->Url->build($ajax),
            'beforeSend' => 'AdminUi.onBeforeSendAjaxLink',
            'success' => 'AdminUi.onSuccessAjaxLink',
            'error' => 'AdminUi.onErrorAjaxLink',
        ];

        $functionOptions = [
            'beforeSend' => 'xhr,options',
            'success' => 'data,status,xhr',
            'error' => 'xhr,status,error'
        ];


        // on enrobe le nom des fonctions dans une fonction anonyme
        $ajaxOptions = collection($ajaxOptions)->filter(function ($v, $k) use ($functionOptions) {
                return in_array($k, array_keys($functionOptions));
            })->map(function ($option, $k) use ($functionOptions) {
                return 'function(' . $functionOptions[$k] . '){return ' . $option .
                    '($event,' . $functionOptions[$k] . ');}';
            })->toArray() + $ajaxOptions;

        // on vire les options inconnues de jquery.ajax
        $linkOptions = array_diff_key($ajaxOptions, $functionOptions + ['url' => null]);
        $ajaxOptions = array_diff_key($ajaxOptions, $linkOptions);

        $options['link'] = ['#', $linkOptions];
        // fin de l'ecriture du lien


        $js = Text::insert('$(function(){$(\'#:id\').on("click",function($event){' .
            '$.ajax(:eventData);' .
            'return false;});});',
            [
                'id' => $linkOptions['id'],
                'eventData' => Tools::jsonEncodeWithFunction($ajaxOptions)
            ]);

        echo $this->getView()->Html->scriptBlock($js, ['block' => 'script']);

        unset($options['ajaxLink']);

        return $options;
    }

    public function modalLinkToAjaxLink($options = [])
    {
        $key = 'modalLink';
        $this->checkContentOptions($options[$key]);

        list($modal, $modalOptions) = $this->getContentOptions($options[$key]);

        $modalOptions += [
            'beforeSend' => 'AdminUi.onBeforeSendModalLink',
            'success' => 'AdminUi.onSuccessModalLink',
            'error' => 'AdminUi.onErrorAjaxLink',
        ];

        $options['ajaxLink'] = [$modal, $modalOptions];

        unset($options[$key]);

        return $options;
    }

    public function tabLinkToAjaxLink($options = [])
    {
        list($tab, $tabOptions) = $this->getContentOptions($options['tabLink']);

        $tabOptions += [
            'beforeSend' => 'AdminUi.onBeforeSendTabLink',
            'success' => 'AdminUi.onSuccessTabLink',
            'error' => 'AdminUi.onErrorAjaxLink',
            'data-show-on-click' => true,
        ];

        $options['ajaxLink'] = [$tab, $tabOptions];

        unset($options['tabLink']);

        return $options;
    }

    public function trLinkToAjaxLink($options = [])
    {
        $this->checkContentOptions($options['trLink']);

        list($tr, $trOptions) = $this->getContentOptions($options['trLink']);

        $trOptions += [
            'beforeSend' => 'AdminUi.onBeforeSendTrLink',
            'success' => 'AdminUi.onSuccessTrLink',
            'error' => 'AdminUi.onErrorAjaxLink',
        ];

        $options['ajaxLink'] = [$tr, $trOptions];

        unset($options['trLink']);

        return $options;
    }

    public function storeContentLink($content, $link)
    {
        $keyArray = $this->normalizeLink($link);

        $key = implode('.', $keyArray);

        $this->request->getSession()->write('Ui.links.' . $key, $content);
    }

    public function normalizeLink($link)
    {
        if (!is_array($link)) {
            $link = [$link];
        }

        $linkDefault = [
            'plugin' => $this->request->getParam('plugin'),
            'controller' => $this->request->getParam('controller'),
            'action' => 'index',
        ];

        $keyArray = array_merge($linkDefault, $link);

        if (isset($keyArray['controller'])) {
            $keyArray['controller'] = Inflector::pluralize(
                Inflector::classify(Inflector::underscore($keyArray['controller']))
            );
        }

        if (isset($keyArray['action'])) {
            $keyArray['action'] = Inflector::variable(Inflector::underscore($keyArray['action']));
        }

        return $keyArray;
    }

    /**
     * add class in link options if exist, btw transform any link in true link
     */
    public function addClassInLink($list, $class, $field = "class")
    {
        return collection($list)->map(function ($li) use ($class, $field) {
            list($li, $liOptions) = $this->getContentOptions($li);
            return $this->linkify($li, $liOptions, [$field => $class]);
        })->toArray();
    }

    public function isLinkExistInOptions($options)
    {
        $keys = array_keys($this->linkDefaults);
        return !collection($options)->filter(function ($v, $k) use ($keys) {
            return in_array($k, $keys, true) && $v;
        })->isEmpty();
    }
}
