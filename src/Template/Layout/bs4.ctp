<?php
/**
 * @var \App\View\AppView $this
 */
?>
<!doctype html>
<html lang="en">

<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?= $this->Html->meta('icon') ?>
    <?= $this->fetch('meta') ?>
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?php

    // CSS  ////////////////////////////////////////////////////////////////////

    echo $this->Html->css([
        'AdminUi.bootstrap4.min',
        'AdminUi.bootstrap4-surcharge',
        'AdminUi.fontawesome5.all.min',
        'AdminUi.select2.min',
        'AdminUi.select2-bootstrap4.min',
    ]);
    echo $this->fetch('css');
    ?>
</head>
<body style="font-size: 0.8rem;">
<?php
$showMenu = (isset($showMenu) ? $showMenu : ($this->request->getParam('action') !== 'login'));


if ($showMenu) {
    echo $this->element('Layout/menu');

// controller titles custom
    $breadcrumbControllers = [
        'AdminUi.Test' => $this->Html->iconText('vial', 'Tests')
    ];
    echo $this->element('Layout/breadcrumb', compact(['breadcrumbControllers']));
}

echo '<br/>';
echo $this->Html->div('container-fluid',
    $this->Flash->render() .
    $this->fetch('content')
);
echo $this->element('AdminUi.Layout/loading');
echo $this->element('AdminUi.Layout/modal');
// JS   ////////////////////////////////////////////////////////////////////////

// librairies
echo $this->Html->script([
    'AdminUi.jquery/jquery-1.12.4.min',
    'AdminUi.bootstrap4/bootstrap.bundle.min',
    // calcul de date en javascript  + affichagge de difference en humain
    'AdminUi.moment/moment-2.22.2.min',
    'AdminUi.select2/select2-4.0.5.min'
]);

// current app
echo $this->Html->scriptsForCurrentPluginControllerAction(['subFolder' => 'AdminUi']);
echo $this->fetch('script');
?>
</body>

</html>