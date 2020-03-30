<?php

namespace QuinenCake\View\Helper;

use Cake\Core\App;
use Cake\Core\Exception\Exception;
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

    /**
     * get controller from view, apparently from cake 2
     * https://stackoverflow.com/questions/13034267/in-viewcakephp-the-proper-way-to-get-current-controller
     * @param $pControllerName
     * @return mixed
     */
    public function getController($pControllerName)
    {

        if (!isset($this->controllersArray[$pControllerName])) {
            $importRes = App::import('Controller', $pControllerName);// The same as require('controllers/users_controller.php');
            $strToEval = "\$controller = new " . $pControllerName . "Controller;";
            $evalRes = eval($strToEval);
            if ($evalRes === false) {
                throw new Exception("Eval returned an error into " . __FILE__ . " getController()");
            }
            $controller->constructClasses();// If we want the model associations, components, etc to be loaded
            $this->controllersArray[$pControllerName] = $controller;
        }

        $result = $this->controllersArray[$pControllerName];
        return $result;
    }
}
