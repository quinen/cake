<?php

namespace QuinenCake\View\Helper;

use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\View\Exception\MissingHelperException;
use QuinenLib\Tools;

trait LinkTrait
{
    private $linkDefaults = [
        'ajaxLink' => false,
        'link' => false,
        'modalLink' => false,
        'postLink' => false,
        'tabLink' => false,
        'trLink' => false,
    ];

    private $linkContent = [];

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
            'plugin' => $this->getRequest()->getParam('plugin'),
            'controller' => $this->getRequest()->getParam('controller'),
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
        return collection($list)->map(
            function ($li) use ($class, $field) {
                list($li, $liOptions) = $this->getContentOptions($li);
                return $this->linkify($li, $liOptions, [$field => $class]);
            }
        )->toArray();
    }

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
            list($this->linkContent, $linkOptions) = $this->getContentOptions($options[$key]);

            // eliminate the key no longer useful
            unset($options[$key]);

            // class merge specific
            if (isset($injectLinkOptions['class'])) {
                $linkOptions = $this->addClass($linkOptions, $injectLinkOptions['class']);
                unset($injectLinkOptions['class']);
            }

            // inject before link for surcharge, ex : confirm on postLink
            $linkOptions = $injectLinkOptions + $linkOptions + ['escape' => false];

            if ($this->linkContent === false) {
                $content = $this->tag(
                    'a',
                    $content,
                    $linkOptions
                );
            } else {
                if ($this->linkCheckAccess()) {
                    if (is_array($this->linkContent)) {
                        unset($this->linkContent['_access']);
                    }

                    $content = \call_user_func(
                        $linkCallback[$key],
                        $content,
                        $this->linkContent,
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

    private function ajaxLinkToLink($options = [])
    {
        list($ajax, $ajaxOptions) = $this->getContentOptions($options['ajaxLink']);

        $ajaxOptions += [
            'id' => Text::uuid(),
            'data-href' => $this->Url->build($ajax)
        ];

        $options['link'] = ['#', $ajaxOptions];

        unset($options['ajaxLink']);
        return $options;
    }

    private function linkCheckAccess()
    {
        try {
            $auth = $this->getView()->Auth;
            $isAuthCheck = $auth->check($this->linkContent);
        } catch (MissingHelperException $e) {
            // pas de helper Auth
            $isAuthCheck = true;
        }

        $isLinkAccess = (isset($this->linkContent['_access']) && $this->linkContent['_access']);

        return $isAuthCheck || $isLinkAccess;
    }

    public function isLinkExistInOptions($options)
    {
        $keys = array_keys($this->linkDefaults);
        return !collection($options)->filter(
            function ($v, $k) use ($keys) {
                return in_array($k, $keys, true) && $v;
            }
        )->isEmpty();
    }

    private function modalLinkToAjaxLink($options = [])
    {
        $key = 'modalLink';
        $this->checkContentOptions($options[$key]);

        list($modal, $modalOptions) = $this->getContentOptions($options[$key]);

        $modalOptions += [
            'data-link' => 'modal'
        ];

        $options['ajaxLink'] = [$modal, $modalOptions];

        unset($options[$key]);

        return $options;
    }

    private function tabLinkToAjaxLink($options = [])
    {
        list($tab, $tabOptions) = $this->getContentOptions($options['tabLink']);

        $tabOptions += [
            'data-link' => 'tab',
            'data-show-on-click' => true,
            'data-parent' => 0
        ];

        $options['ajaxLink'] = [$tab, $tabOptions];
        unset($options['tabLink']);

        return $options;
    }

    private function trLinkToAjaxLink($options = [])
    {
        $this->checkContentOptions($options['trLink']);

        list($tr, $trOptions) = $this->getContentOptions($options['trLink']);

        $trOptions += [
            'data-link' => 'tr'
        ];

        $options['ajaxLink'] = [$tr, $trOptions];

        unset($options['trLink']);

        return $options;
    }
}
