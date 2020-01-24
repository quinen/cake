<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 05/12/18
 * Time: 16:53
 */

return [
    //'wrapper' => '<ul{{attrs}}>{{content}}</ul>',
    'wrapper' => '<nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 {{attrs.class}}" {{attrs}}>{{content}}</ol></nav>',
    //'item' => '<li{{attrs}}><a href="{{url}}"{{innerAttrs}}>{{title}}</a></li>{{separator}}',
    'item' => '<li class="breadcrumb-item {{attrs.class}}" {{attrs}}><a href="{{url}}"{{innerAttrs}}>{{title}}</a></li>{{separator}}',
    'itemWithoutLink' => '<li class="breadcrumb-item {{attrs.class}}" {{attrs}}><span{{innerAttrs}}>{{title}}</span></li>{{separator}}',
    'separator' => '<li{{attrs}}><span{{innerAttrs}}>{{separator}}</span></li>'
];