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
    <?php echo $this->Html->css('AdminUi.bootstrap3.min'); ?>
    <?= $this->fetch('css') ?>
</head>

<body>
    <?php
    echo $this->Html->div('container-fluid',
        $this->Flash->render().
        $this->fetch('content')
    );
    echo $this->Html->script('AdminUi.jquery-3.3.1.min');
    echo $this->Html->script('AdminUi.bootstrap3.min');
    ?>
    <?= $this->fetch('script') ?>
</body>

</html>