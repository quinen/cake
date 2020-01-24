<?php

echo $this->Bs4->navbar(
    $this->Html->iconText($_menu['icon'], $_menu['text']),
    array_merge(
        $_menu['list'],
        [[
            'icon' => 'sign-out-alt',
            'text' => $this->Auth->user('display'),
            'link' => [
                'plugin' => 'AdminUi',
                'controller' => 'Users',
                'action' => 'logout'
            ]
        ]]
    ),
    [
        'theme' => 'dark',
        'bg' => 'dark'
    ]
);