<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 05/11/18
 * Time: 15:57
 */


return [
    'first' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',

    'prevActive' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
    'prevDisabled' => '<li class="page-item disabled"><a class="page-link" href="{{url}}">{{text}}</a></li>',

    'current' => '<li class="page-item active"><a class="page-link" href="{{url}}">{{text}}</a></li>',
    'number' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',

    'nextActive' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
    'nextDisabled' => '<li class="page-item disabled"><a class="page-link" href="{{url}}">{{text}}</a></li>',

    'last' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',

    'counterRange' => '<li class="page-item disabled"><a class="page-link"> Elements {{start}} à {{end}} / {{count}}</a></li>',
    'counterPages' => '<li class="page-item disabled"><a class="page-link"> Page {{page}} / {{pages}}</a></li>',

    'ellipsis' => '<li class="ellipsis">&hellip;</li>',

    'sort' => '<a href="{{url}}">{{text}}</a>',
    'sortAsc' => '<a class="asc" href="{{url}}">{{text}}</a>&nbsp;<i class="fas fa-angle-up fa-lg"></i>',
    'sortDesc' => '<a class="desc" href="{{url}}">{{text}}</a>&nbsp;<i class="fas fa-angle-down fa-lg"></i>',
    'sortAscLocked' => '<a class="asc locked" href="{{url}}">{{text}}</a>',
    'sortDescLocked' => '<a class="desc locked" href="{{url}}">{{text}}</a>',
];