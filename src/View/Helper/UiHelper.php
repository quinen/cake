<?php

namespace QuinenCake\View\Helper;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use QuinenLib\Arrays\ContentOptionsTrait;

/**
 * Class UiHelper
 * @package QuinenCake\View\Helper
 * @property \CpaGestion\View\Helper\HtmlHelper $Html
 */
class UiHelper extends Helper
{
    use ContentOptionsTrait;

    public static $sessionKey = 'Ui';

    public $helpers = ['Html'];
    /** @var Table $table * */
    public $tables = [];

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->setTable();
    }

    /**
     * @param null $pluginController
     * @return Table|mixed
     */
    public function setTable($pluginController = null)
    {
        $pluginController = $this->getContext($pluginController);

        if (!$pluginController) {
            return false;
        }

        $table = Hash::get($this->tables, $pluginController);

        if ($table === null) {
            $table = TableRegistry::get($pluginController);
            $this->tables = Hash::insert($this->tables, $pluginController, $table);
        }

        return $table;
    }

    protected function getContext($pluginController = null)
    {
        if ($pluginController === null) {
            $pluginController = $this->Html->getCurrentContext();
        }
        return $pluginController;
    }

    public function getActionTitle($action = null, $options = [])
    {
        if ($action === null) {
            $action = $this->getView()->getRequest()->getParam('action');
        }

        $models = [
            // action
            'index' => ['icon' => 'list', 'text' => 'Liste'],
            'view' => ['icon' => 'eye', 'text' => 'Detail'],
            'edit' => ['icon' => 'pen', 'text' => 'Modifier'],
            'add' => ['icon' => 'plus', 'text' => 'Ajouter'],
            'delete' => ['icon' => 'trash', 'text' => 'Supprimer'],
            // access
            'read' => ['icon' => 'eye', 'text' => 'Lecture'],
            'save' => ['icon' => 'pen', 'text' => 'Ecriture'],
            // ne devrais pas exister
            'login' => ['icon' => 'sign-in-alt', 'text' => 'Connexion'],
            'logout' => ['icon' => 'sign-out-alt', 'text' => 'Déconnexion'],
            'password' => ['icon' => 'key', 'text' => 'Mot de Passe'],
        ];

        $options += [
            'action' => $action,
            'returnIsHtml' => true
        ];

        if (isset($models[$options['action']])) {
            $options += $models[$options['action']];
        } else {
            $options += [
                'icon' => null,
                'text' => Inflector::humanize($options['action'])
            ];
        }
        /* TODO code a externaliser

        else {
            $link = $this->Html->normalizeLink(['action' => $options['model']]);
            $text = $this->getView()->getRequest()->getSession()->read('Ui.links.' . implode('.', $link));
            $icon = false;
            $options = compact('icon', 'text');
        }*/

        if ($options['returnIsHtml']) {
            return $this->Html->iconText($options['icon'], $options['text']);
        } else {
            unset($options['returnIsHtml']);
            return $options;
        }


    }

    public function getPluginTitle($plugin = null)
    {
        if ($plugin === null) {
            $plugin = $this->getView()->getRequest()->getParam('plugin');
        }

        $sessionKey = self::$sessionKey . '.Plugins.' . $plugin;

        $pluginArray = $this->getOrSetValueInSession($sessionKey, function () use ($plugin) {
            return TableRegistry::get('AdminUi.Plugins')->findByName($plugin)->first()->toArray();
        });

        return $this->Html->iconText($pluginArray['icon'], $pluginArray['text']);
    }

    protected function getOrSetValueInSession($key, $callback)
    {
        // patch pour pluginController a null
        if (($pos = strpos($key, '..')) && $pos !== false) {
            $key = substr($key, 0, $pos + 1) .
                $this->getContext(null) .
                substr($key, $pos + 1);
        }

        if (!$this->getView()->getRequest()->getSession()->check($key)) {
            list($callable, $params) = $this->getContentOptions($callback);
            $value = call_user_func_array($callable, $params);
            $this->getView()->getRequest()->getSession()->write($key, $value);
        }

        return $this->getView()->getRequest()->getSession()->read($key);
    }

    public function getTitle($isPlural = true, $pluginController = null, $options = [])
    {
        $iconName = $this->getIconName($pluginController);
        $text = $this->getLabel($isPlural, $pluginController);
        return $this->Html->iconText($iconName, $text, $options);
    }

    // getUiFieldLabel

    public function getIconName($pluginController = null)
    {
        $sessionKey = self::$sessionKey . '.Models.' . $pluginController . '.icon';

        return $this->getOrSetValueInSession($sessionKey, function () use ($pluginController) {
            $table = $this->getTable($pluginController);
            $iconName = 'question-sign';
            if (method_exists($table, 'getUiIcon')) {
                $iconName = $table->getUiIcon();
            }
            return $iconName;
        });
    }

    public function getTable($pluginController = null)
    {
        return $this->setTable($pluginController);
    }

    public function getLabel($isPlural = true, $pluginController = null)
    {
        $sessionKey = self::$sessionKey . '.Models.' . $pluginController . '.label.' . (intval($isPlural));

        return $this->getOrSetValueInSession($sessionKey, function () use ($pluginController, $isPlural) {
            $table = $this->getTable($pluginController);
            if (method_exists($table, 'getUiLabel')) {
                return $table->getUiLabel($isPlural);
            }
            return pluginSplit($pluginController)[1];
        });
    }

    /**
     * @param null $pluginController
     * @return mixed
     */
    public function getIcon($pluginController = null)
    {
        $icon = $this->getIconName($pluginController);

        return $this->Html->icon($icon);
    }

    public function getFieldLabel($field, $pluginController = null)
    {
        $sessionKey = self::$sessionKey . '.Models.' . $pluginController . '.fields.' . $field . '.label';

        return $this->getOrSetValueInSession($sessionKey, function () use ($pluginController, $field) {
            $table = $this->getTable($pluginController);
            $label = null;

            // si une table associée, on a un champ sinon non
            if ($table && method_exists($table,'getUiFieldLabel')) {
                $label = $table->getUiFieldLabel($field);
            }

            // si pas de label, on prend le champ
            if ($label === null) {
                $label = $field;
            }
            return $label;
        });
    }

    public function getFieldFormat($field, $pluginController = null)
    {
        $sessionKey = self::$sessionKey . '.Models.' . $pluginController . '.fields.' . $field . '.format';

        return $this->getOrSetValueInSession($sessionKey, function () use ($pluginController, $field) {
            $table = $this->getTable($pluginController);
            $format = null;

            if ($table && method_exists($table,'getUiFieldFormat')) {
                $format = $table->getUiFieldFormat($field);
            }

            if ($format === null) {
                $format = true;
            }

            return $format;
        });
    }

    protected function clearSession()
    {
        return $this->getView()->getRequest()->getSession()->delete(self::$sessionKey);
    }

}
