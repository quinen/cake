<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/24/20
 * Time: 3:33 PM
 */

namespace QuinenCake\Utility;


use App\Application;
use Cake\Console\CommandRunner;
use Cake\Core\App;
use Cake\Event\Event;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;

class Bake
{
    /**
     * @param Event $event
     * @param $element
     */
    public static function renderTemplateElement(Event $event, $element)
    {

        /** @var \Bake\View\BakeView $view */
        $view = $event->getSubject();

        $pluginModel = $view->viewVars['modelClass'];
        $plugin = $view->viewVars['plugin'];
        $theme = $view->getTheme();

        /** @var \Cake\ORM\Table $model */
        $model = TableRegistry::get($pluginModel);

        $modelName = $model->getAlias();
        $connectionName = $model->getConnection()->configName();

        $argv = [
            'cake',
            'bake',
            'template',
            $modelName,
            '../Element/' . $element,
            $element,
            '-p',
            $plugin,
            '-c',
            $connectionName,
            '-t',
            $theme,
            '-f'    // force
        ];

        $runner = new CommandRunner(new Application(rtrim(CONFIG, DS)), 'cake');
        $runner->run($argv);

        // move file to element folder
        $pluginPath = current(App::path('Template', $plugin)); // with slash at the end
        $controllerFolder = $modelName . DS;
        $elementFolder = 'Element' . DS . $modelName . DS;
        $ext = '.ctp';

        $oldFile = $pluginPath . $controllerFolder . $element . $ext;

        $elementObj = new Folder($pluginPath . $elementFolder, true);
        $newFile = $elementObj->path . $element . $ext;

        return rename($oldFile, $newFile);
    }
}