<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 02/11/18
 * Time: 15:20
 */

namespace QuinenCake\View\Helper;

use Cake\Utility\Inflector;
use QuinenLib\Arrays\ContentOptionsTrait;
use QuinenLib\Arrays\MapTrait;

trait BootstrapTrait
{
    use ContentOptionsTrait;
    use MapTrait;

    public function linkify($content, array $options = [], array $optionsLink = [])
    {
        if (isset($options['link'])) {

            list($link, $linkOptions) = $this->getContentOptions($options['link']);

            $linkOptions = $this->addClassFromBooleanOptions(
                $linkOptions,
                ['active', 'disabled']
            );
            $options['link'] = [$link, $linkOptions];
        }
        return $this->Html->linkify($content, $options, $optionsLink);
    }

    /**
     * @param $data
     * @param $classes
     * @param array $options
     * @return array
     */
    public function addClassFromBooleanOptions($data, $classes, $options = [])
    {
        $options += [
            'field' => "class",
            'prefix' => ""
        ];

        return collection($classes)->reduce(function ($reducer, $class) use ($options) {
            $booleanString = "is" . Inflector::camelize($class);

            // ex table options for width 100%
            if ($options['prefix'] == $class) {
                $classString = $class;
            } else {
                $classString = trim(implode("-", [$options['prefix'], $class]), "-");
            }

            if (isset($reducer[$booleanString]) && $reducer[$booleanString]) {
                $reducer = $this->Html->addClass($reducer, $classString, $options['field']);
            }
            unset($reducer[$booleanString]);
            return $reducer;
        }, $data);

    }

    public function table($datas, $maps = [], $options = [])
    {
        $options += [
            'isBordered' => true,
            'isHover' => true,
            'isStriped' => true,
            'isResponsive' => true,
            'isTable' => true,
            'emptyOptions' => [],
            'mapCallbackClass' => $this,
            'paginatorFormat' => '<ul class="pagination justify-content-center mt-3">' .
                '{{counterPages}}{{first}}{{prev}}{{numbers}}{{next}}{{last}}{{counterRange}}' .
                '</ul>'
        ];

        $options = $this->addClassFromBooleanOptions(
            $options,
            ['bordered', 'hover', 'striped', 'table'],
            ['prefix' => "table"]
        );

        $isResponsive = false;
        if ($options['isResponsive']) {
            $isResponsive = true;
        }
        unset($options['isResponsive']);

        $options['emptyOptions'] = $this->Html->addClass($options['emptyOptions'], 'text-center');

        $table = $this->Html->table($datas, $maps, $options);

        if ($isResponsive) {
            $table = $this->Html->div('table-responsive', $table);
        }

        return $table;
    }
}