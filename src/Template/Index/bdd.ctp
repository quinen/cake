<?php

echo $this->Bs4->table($bdds, [
    [
        'field' => 'connection',
        'rowspan' => true
    ],
    [
        'field' => 'table',
        'rowspan' => true
    ],
    [
        'field' => 'field'
    ],
    [
        'field' => 'type'
    ],
    [
        'field' => 'length'
    ],
    [
        'field' => 'null',
        'format' => function ($n) {
            if ($n) {
                return 'NULL';
            }

        }
    ],
    [
        'field' => 'default'
    ],
    [
        'field' => 'collate'
    ],
    [
        'field' => 'fixed',
        'format' => function ($f) {
            if ($f) {
                return $this->Bs4->valueTrue();
            }
        }
    ],
    [
        'field' => 'unsigned',
        'format' => function ($f) {
            if ($f === false) {
                return $this->Bs4->valueFalse();
            }
        }
    ],
    [
        'field' => 'autoIncrement'
    ],

    [
        'field' => 'comment'
    ],
    [
        'field' => 'precision'
    ],
    [
        'field' => 'desc'
    ],

]);
