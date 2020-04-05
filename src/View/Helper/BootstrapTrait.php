<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 02/11/18
 * Time: 15:20
 */

namespace QuinenCake\View\Helper;

use Cake\Core\Exception\Exception;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use MtxEnergie\Model\Table\FClientsTable;
use QuinenLib\Arrays\MapTrait;

/*
 * @property QuinenCake\View\Helper\HtmlHelper $Html
 * @property QuinenCake\View\Helper\FormHelper $Form
 *
 * */

trait BootstrapTrait
{

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

        return collection($classes)->reduce(
            function ($reducer, $class) use ($options) {
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
            },
            $data
        );
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

    public function formatDatetime($value)
    {
        return $value;
    }

    public function formatDate($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @param array $options
     * @param array $data
     * @return mixed
     */
    public function formatInput($value, $field, $options = [], $data = [])
    {
        //debug(func_get_args());
        $options += [
            'context' => false,
            'label' => false,
            'url' => true,
            'templates' => [
                'inputContainer' => '<div class="{{required}}">{{content}}</div>'
            ]
        ];

        $optionsCreate = [
            'url' => null
        ];

        $context = $options['context'];
        unset($options['context']);

        if ($options['url'] === true) {
            // automate url
            $optionsCreate['url'] = $this->getFormatInputUrl($context);
        } else {
            $optionsCreate['url'] = $options['url'];
        }


        $form = [];
        $form[] = $this->Form->create($context, $optionsCreate);
        $form[] = $this->Form->control($field, $options);
        $form[] = $this->Form->end();

        return implode(PHP_EOL, $form);
    }

    private function getFormatInputUrl($context)
    {
        if($context instanceof Entity){
            $tableName = $context->getSource();
        } else {
            throw new Exception('need an entity to generate url');
        }
        /** @var FClientsTable $table */
        $table = TableRegistry::getTableLocator()->get($tableName);
        $pk = $table->getPrimaryKey();
        $controller = $table->getAlias();
        $action = 'edit';
        $pass0 = $context->get($pk);

        $url = compact('controller', 'action');
        $url[] = $pass0;
        return $url;
    }
}
