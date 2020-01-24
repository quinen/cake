<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/28/19
 * Time: 4:25 PM
 */
/* @var \App\View\AppView $this */

$content = (isset($content) ? $content : '&nbsp;');
$title = (isset($title) ? $title : false);
$isFade = (isset($isFade) ? $isFade : true);
$isClose = (isset($isClose) ? $isClose : true);
$size = (isset($size) ? $size : 'xl');

echo $this->Bs4->modal(
    $content
    , [
        'id' => 'linkModal',
    ] + compact(['title', 'isFade', 'isClose', 'size'])
);