<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 15/11/18
 * Time: 14:32
 */

namespace QuinenCake\View\Widget;


use Cake\I18n\Date;
use Cake\View\Form\ContextInterface;
use Cake\View\Widget\BasicWidget;


class DateTimeWidget extends BasicWidget
{
    public function render(array $data, ContextInterface $context)
    {
        $data += [
            'val' => null
        ];

        if ($data['val'] instanceof \DateTime) {
            $data['val'] = (new Date($data['val']))->toDateString();
        }

        return parent::render($data, $context);
    }
}
