<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 02/11/18
 * Time: 13:24
 */

namespace QuinenCake\View\Helper;

use Cake\Core\Plugin;
use QuinenLib\Arrays\MapTrait;


trait HtmlTrait
{
    use MapTrait {
        getMapCommonOptions as getMapCommonOptionsBase;
    }
    use IconTrait;
    use LinkTrait;
    use ListTrait;
    use TableTrait;

    public function getMapCommonOptions()
    {
        return [
            'class' => false
        ];
    }

    public function getMapFormat($map, $options = [])
    {
        $options += [
        ];
        $map['format'] = $this->getView()->Ui->getFieldFormat($map['field'][0], $this->getCurrentContext());
        return $map;
    }

    public function getMapLabel($field, $options = [])
    {
        $options += [
        ];
        return $this->getView()->Ui->getFieldLabel($field, $this->getCurrentContext());

    }

    /**
     * @param $value
     * @param array $options
     * @param array $data
     * @return mixed|string
     */
    public function formatBoolean($value, $options = [])
    {
        $options += [
            'callback' => false,
            'valueFalse' => $this->iconText('times', "Non"),
            'valueTrue' => $this->iconText('check', "Oui")
        ];

        if ($options['callback']) {
            $value = $options['callback']($value);
        }

        return ($value ? $options['valueTrue'] : $options['valueFalse']);
    }

    public function implode($value, $separator = '')
    {
        return implode($separator, $value);
    }

    /**
     * if($this->Html->isCurrentPluginControllerAction(['MtxMtpro.ProDossiers.partenaires'])){
     */

    public function isCurrentPluginControllerAction(array $list = [])
    {
        $pca = implode('.', [
            $this->getView()->getRequest()->getParam('plugin'),
            $this->getView()->getRequest()->getParam('controller'),
            $this->getView()->getRequest()->getParam('action')
        ]);

        return in_array($pca, $list);
    }

    public function scriptsForCurrentPluginControllerAction($options = [])
    {
        $options += [
            'subFolder' => false
        ];

        $subFolder = false;
        if ($options['subFolder']) {
            $subFolder = $options['subFolder'] . '/';
        }
        $plugin = $this->getView()->getRequest()->getParam('plugin');
        $controller = $this->getView()->getRequest()->getParam('controller');
        $action = $this->getView()->getRequest()->getParam('action');
        $min = 'min';

        $files = [
            implode('.', [$plugin, $subFolder . $plugin, $min]),
            implode('.', [$plugin, $subFolder . $plugin, $controller, $min]),
            implode('.', [$plugin, $subFolder . $plugin, $controller, $action, $min])
        ];

        $script = array_map(function ($file) {

            $arrayFile = pluginSplit($file);
            $filePath = Plugin::path($arrayFile[0]) . 'webroot' . DS . 'js' . DS . $arrayFile[1] . '.js';

            if (file_exists($filePath)) {
                return $this->script($file);
            }
        }, $files);

        return implode($script);

    }

    public function calcPercentage($fields, $precision = 0)
    {
        if ($fields[0] == 0) {
            $fields = [1, 0];
        }

        return $this->getView()->Number->toPercentage($fields[1] / $fields[0] * 100, $precision);
    }

    public function formatUrl($value, $options = [])
    {
        $options += [
            'showText' => true
        ];

        if (empty($value)) {
            $urlValue = $this->icon('times', ['style' => 'font-size:2em;color:darkred;']);
        } else {
            $urlValue = $this->link(
                $this->icon('external-link-alt', ['style' => 'font-size:2em;']),
                $value,
                [
                    'escape' => false,
                    'target' => '_blank'
                ]
            );
            $urlValue .= ($options['showText'] ? '&nbsp;' . $this->formatShorten($value) : '');

        }

        return $urlValue;
    }

    public function formatShorten($value, $options = [])
    {
        $options += [
            'maxlength' => 64
        ];

        $newValue = $value;
        if (strlen($value) > $options['maxlength']) {
            $len = intval($options['maxlength'] / 2);
            $newValue = $this->tag("span", substr($value, 0, $len) . ' ... ' . substr($value, -$len),
                array('title' => h($value)));
        }

        return $newValue;
    }

    public function span($class = null, $text = null, array $options = [])
    {
        if (!empty($class)) {
            $options['class'] = $class;
        }

        return $this->tag('span', $text, $options);
    }
}
