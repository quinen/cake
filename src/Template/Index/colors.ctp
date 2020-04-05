<?php



echo $this->Bs4->row(collection($colors)->map(function($color){
    return [$color['rgb'],['class'=>'col-1','style'=>'background-color:'.$color['rgb']]];
})->toArray());