<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 26/11/18
 * Time: 19:00
 */

namespace QuinenCake\View\Helper;


use Cake\View\Helper\PaginatorHelper as BaseHelper;

class PaginatorHelper extends BaseHelper
{

    public function prev($title = null, array $options = [])
    {
        $options += [
            'escape' => false
        ];

        if ($title === null) {
            $title = $this->Html->icon('angle-left');
        }

        return parent::prev($title, $options);
    }

    public function first($first = null, array $options = [])
    {

        $options += [
            'escape' => false
        ];

        if ($first === null) {
            $first = $this->Html->icon('angle-double-left');
        }
        return parent::first($first, $options);
    }

    public function next($title = null, array $options = [])
    {
        $options += [
            'escape' => false
        ];

        if ($title === null) {
            $title = $this->Html->icon('angle-right');
        }
        return parent::next($title, $options);
    }

    public function last($last = null, array $options = [])
    {
        $options += [
            'escape' => false
        ];

        if ($last === null) {
            $last = $this->Html->icon('angle-double-right');
        }
        return parent::last($last, $options);
    }
}