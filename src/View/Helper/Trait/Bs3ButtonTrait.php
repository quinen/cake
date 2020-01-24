<?php

namespace QuinenCake\View\Helper;

trait Bs3ButtonTrait
{
    use BsButtonTrait {
        getButtonColor as getBootstrapButtonColor;
        getButtonModels as getBootstrapButtonModels;
    }

    protected function getButtonColor($options)
    {

        $options += [
            'color' => 'default',
        ];

        return $this->getBootstrapButtonColor($options);
    }

    protected function getButtonModels($model = null)
    {

        $buttonModels = [

        ];

        if ($model !== null) {
            return Hash::get($buttonModels + $this->getBootstrapButtonModels(), $model, false);
        }
        return $buttonModels + $this->getBootstrapButtonModels();
    }

}
