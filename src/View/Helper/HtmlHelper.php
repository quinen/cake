<?php

namespace QuinenCake\View\Helper;

use Cake\View\Helper\HtmlHelper as BaseHelper;

/**
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class HtmlHelper extends BaseHelper
{
    use HtmlTrait;
    use FontAwesome5Trait {
        fa5 as icon;
        fa5Stacked as iconStacked;
    }

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->setDefaultContext(
            $this->getView()->getRequest()->getParam('plugin') . '.' .
            $this->getView()->getRequest()->getParam('controller')
        );
    }
}
