<?php
/**
 * @var \App\View\AppView $this
 */

use Cake\Utility\Hash;

$class = Hash::get($params, 'class', "danger");

if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="alert alert-<?= h($class) ?> alert-dismissible fade show" role="alert">
    <?php
    if (is_array($message)) {
        echo $this->Html->list($message, ['class' => 'list-unstyled']);
    } else {
        echo $message;
    }
    ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
