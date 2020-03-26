<?php

$_menu = isset($_menu) ? $_menu : [];
$_menuTitle = isset($_menuTitle) ? $_menuTitle : [$this->Html->icon('home'), ['link' => '/']];
$_menuOptions = isset($_menuOptions) ? $_menuOptions : ['theme' => 'dark', 'bg' => 'dark'];
////////////////////////////////////////////////////////////////////////////
//  /!\ si menu vide penser a ajouter des accés avec Auth ou _access !!   //
////////////////////////////////////////////////////////////////////////////
echo $this->Bs4->navbar($_menuTitle, $_menu, $_menuOptions);