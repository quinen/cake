<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 05/11/18
 * Time: 15:57
 */


return [
    'first' => '<a href="{{url}}">{{text}}</a>',

    'prevActive' => '<a rel="prev" href="{{url}}">{{text}}</a>',
    'prevDisabled' => '',

    'current' => '&nbsp;<b>{{text}}</b>&nbsp;',
    'number' => '&nbsp;<a href="{{url}}">{{text}}</a>&nbsp;',

    'nextActive' => '<a rel="next" href="{{url}}">{{text}}</a>',
    'nextDisabled' => '',

    'last' => '<a href="{{url}}">{{text}}</a>',

    'counterRange' => 'Elements {{start}} - {{end}} de {{count}} résultats',
    'counterPages' => 'Page {{page}} / {{pages}}',

    'ellipsis' => '<li class="ellipsis">&hellip;</li>',

    'sort' => '<a href="{{url}}">{{text}}</a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}}</a>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}}</a>',
    'sortAscLocked' => '<a class="asc locked" href="{{url}}">{{text}}</a>',
    'sortDescLocked' => '<a class="desc locked" href="{{url}}">{{text}}</a>',
];
/*
return [
    'nextActive' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
    'nextDisabled' => '<li class="page-item disabled"><a class="page-link" href="" onclick="return false;">{{text}}</a></li>',
    'prevActive' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
    'prevDisabled' => '<li class="page-item disabled"><a class="page-link" href="" onclick="return false;">{{text}}</a></li>',
    'first' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
    'last' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
];
*/