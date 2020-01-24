<?php

use Cake\Event\Event;
use Cake\Event\EventManager;
use QuinenCake\Utility\Bake;

EventManager::instance()->on('Bake.beforeRender.Template.edit', function (Event $event) {
    Bake::renderTemplateElement($event, 'form_add');
});

EventManager::instance()->on('Bake.beforeRender.Template.add', function (Event $event) {
    Bake::renderTemplateElement($event, 'form_add');
});

EventManager::instance()->on('Bake.beforeRender.Template.view', function (Event $event) {
    Bake::renderTemplateElement($event, 'detail');
});

EventManager::instance()->on('Bake.beforeRender.Template.index', function (Event $event) {
    Bake::renderTemplateElement($event, 'form_search');
    Bake::renderTemplateElement($event, 'list');
});