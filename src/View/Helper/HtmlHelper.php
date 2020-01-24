<?php

namespace QuinenCake\View\Helper;

use Cake\View\Helper\HtmlHelper as BaseHelper;
use QuinenLib\Arrays\CurrentContextTrait;

/**
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class HtmlHelper extends BaseHelper
{
    use HtmlTrait;
    use FontAwesome5Trait {
        fa5 as icon;
    }
    use CurrentContextTrait;

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->setDefaultContext(
            $this->getView()->getRequest()->getParam('plugin') . '.' .
            $this->getView()->getRequest()->getParam('controller')
        );
    }
}
