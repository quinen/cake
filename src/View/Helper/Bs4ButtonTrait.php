<?php

namespace QuinenCake\View\Helper;

use Cake\Utility\Hash;

trait Bs4ButtonTrait
{
    use BsButtonTrait {
        getButtonModels as getBootstrapButtonModels;
    }

    protected function getButtonModels($model = null)
    {

        $buttonModels = [
            'create' => [
                    //'color' => 'outline-success'
                ] + $this->getBootstrapButtonModels('create'),
            'read' => [
                    'icon' => 'eye',
                    //'color' => 'outline-info'
                ] + $this->getBootstrapButtonModels('read'),
            'update' => [
                    'icon' => 'pencil-alt',
                    //'color' => 'outline-warning'
                ] + $this->getBootstrapButtonModels('update'),
            'delete' => [
                    //'color' => 'outline-danger'
                ] + $this->getBootstrapButtonModels('delete'),
            'export' => ['icon' => 'file-export'] + $this->getBootstrapButtonModels('export'),

            'reset' => ['icon' => 'redo'] + $this->getBootstrapButtonModels('reset'),
            'submit' => ['icon' => 'check'] + $this->getBootstrapButtonModels('submit'),

            'outline-warning' => ['color' => 'outline-warning'] + $this->getBootstrapButtonModels('warning'),
            'outline-success' => ['color' => 'outline-success'] + $this->getBootstrapButtonModels('success'),
            'outline-danger' => ['color' => 'outline-danger'] + $this->getBootstrapButtonModels('danger'),
            'outline-secondary' => ['color' => 'outline-secondary']+ $this->getBootstrapButtonModels('secondary'),
            'outline-light' => ['color' => 'outline-light'],
            //*/

        ];

        if ($model !== null) {
            return Hash::get($buttonModels + $this->getBootstrapButtonModels(), $model, false);
        }
        return $buttonModels + $this->getBootstrapButtonModels();
    }
}
