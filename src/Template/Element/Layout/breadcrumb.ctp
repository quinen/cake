<?php

/** @var \App\View\AppView $this */
/** @property \QuinenCake\View\Helper\UiHelper $this->Ui */

$showPlugin = (isset($showPlugin) ? $showPlugin : true);
$showController = (isset($showController) ? $showController : true);

$plugin = $this->request->getParam('plugin');
$controller = $this->request->getParam('controller');
$action = $this->request->getParam('action');

// accueil
$this->Breadcrumbs->add($this->Html->iconText('home', 'Accueil'), '/');

// plugin
if ($showPlugin) {

    $this->Breadcrumbs->add($this->Ui->getPluginTitle($plugin), [
        'plugin' => $plugin,
        'controller' => null,
        'action' => null
    ]);
}

// current context could be altered before
$context = $this->Html->getDefaultContext();
if (isset($breadcrumbControllers[$context])) {
    if ($breadcrumbControllers[$context]) {
        $controllerTitle = $breadcrumbControllers[$context];
    } else {
        $showController = false;
    }
} else {
    $controllerTitle = $this->Ui->getTitle(true, $context);
}
if ($showController) {
    $this->Breadcrumbs->add($controllerTitle, [
        'controller' => $controller,
        'action' => 'index'
    ]);
}

// action
$this->Breadcrumbs->add($this->Ui->getActionTitle($this->request->getParam('action')));

//echo $this->Breadcrumbs->render();