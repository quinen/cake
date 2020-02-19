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

    //https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    // integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    echo $this->Html->css([
        'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css',
        'QuinenCake.bootstrap4-surcharge',
        'https://pro.fontawesome.com/releases/v5.12.0/css/all.css',
        'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css',
        'QuinenCake.select2-bootstrap4.min',
    ]);
    echo $this->fetch('css');
    ?>
</head>
<body style="font-size: 0.8rem;">
<?php

$showMenu = (isset($showMenu) ? $showMenu : ($this->request->getParam('action') !== 'login'));

if ($showMenu) {
    echo $this->element($this->getPlugin().'.Layout/menu');

// controller titles custom
    $breadcrumbControllers = [
        'AdminUi.Test' => $this->Html->iconText('vial', 'Tests')
    ];
    //echo $this->element('Layout/breadcrumb', compact(['breadcrumbControllers']));
}

echo '<br/>';
echo $this->Html->div('container-fluid',
    $this->Flash->render() .
    $this->fetch('content')
);
//echo $this->element('AdminUi.Layout/loading');
//echo $this->element('AdminUi.Layout/modal');
// JS   ////////////////////////////////////////////////////////////////////////

// librairies
echo $this->Html->script([
    'https://code.jquery.com/jquery-3.4.1.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js',
    'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js',
    // calcul de date en javascript  + affichagge de difference en humain
    'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js',
    'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js'
]);

// current app
echo $this->Html->script('QuinenCake.QuinenCake.min');
echo $this->Html->scriptsForCurrentPluginControllerAction();
echo $this->fetch('script');
?>
</body>

</html>
