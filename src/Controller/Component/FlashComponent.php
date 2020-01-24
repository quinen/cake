<?php
/**
 * personalisation du call pour bootstrap 4 default centralisé
 */

namespace QuinenCake\Controller\Component;

use Cake\Controller\Component\FlashComponent as BaseComponent;
use Cake\Http\Exception\InternalErrorException;
use Cake\Utility\Inflector;


/**
 * The CakePHP FlashComponent provides a way for you to write a flash variable
 * to the session from your controllers, to be rendered in a view with the
 * FlashHelper.
 *
 * @method void success(string $message, array $options = []) Set a message using "success" element
 * @method void error(string $message, array $options = []) Set a message using "error" element
 */
class FlashComponent extends BaseComponent
{
    protected $alertStyles = [
        'primary',
        'secondary',
        'success',
        'error' => 'danger',
        'warning',
        'info',
        'light',
        'dark'
    ];

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'key' => 'flash',
        'element' => 'QuinenCake.default',
        'params' => [],
        'clear' => false,
        'duplicate' => true
    ];

    /**
     * Magic method for verbose flash methods based on element names.
     *
     * For example: $this->Flash->success('My message') would use the
     * success.ctp element under `src/Template/Element/Flash` for rendering the
     * flash message.
     *
     * If you make consecutive calls to this method, the messages will stack (if they are
     * set with the same flash key)
     *
     * Note that the parameter `element` will be always overridden. In order to call a
     * specific element from a plugin, you should set the `plugin` option in $args.
     *
     * For example: `$this->Flash->warning('My message', ['plugin' => 'PluginName'])` would
     * use the warning.ctp element under `plugins/PluginName/src/Template/Element/Flash` for
     * rendering the flash message.
     *
     * @param string $name Element name to use.
     * @param array $args Parameters to pass when calling `FlashComponent::set()`.
     * @return void
     * @throws \Cake\Http\Exception\InternalErrorException If missing the flash message.
     */
    public function __call($name, $args)
    {
        //debug($args);die();
        if (count($args) < 1) {
            throw new InternalErrorException('Flash message missing.');
        }

        $element = $this->getConfig('element');
        $params = [];

        $class = Inflector::underscore($name);

        if (is_string($class) && isset($this->alertStyles[$class])) {
            // si la classe existe en transformation ex: error devient class css danger
            $params['class'] = $this->alertStyles[$class];
        } elseif (in_array($class, $this->alertStyles)) {
            // si la valeur existe
            $params['class'] = $class;
        }

        $escape = false;

        $options = compact('element', 'params', 'escape');

        if (isset($args[1])) {
            $options = $args[1] + $options;
        }

        $this->set($args[0], $options);
    }
}
