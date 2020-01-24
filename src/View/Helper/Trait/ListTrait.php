<?php

namespace QuinenCake\View\Helper;

use Cake\Utility\Hash;

trait ListTrait
{
    public function list($list, $options = [], $optionsItem = [])
    {
        $options += [
            'tag' => "ul"
        ];

        $content = implode(collection($list)->map(function ($li) use ($options, $optionsItem) {
            list($content, $contentOptions) = $this->getContentOptions($li);

            // sublist
            $subList = Hash::get($contentOptions, 'list');
            unset($contentOptions['list']);
            if ($subList) {
                // add options from parent unless overwritten
                $contentOptions += $options;
                $content .= $this->list($subList, $contentOptions, $optionsItem);
            }

            // element
            list($content, $contentOptions) = $this->linkify($content, $contentOptions);
            return $this->tag('li', $content, $contentOptions + $optionsItem);
        })->toArray());

        // tag
        $tag = $options['tag'];
        unset($options['tag']);

        // list
        return $this->tag($tag, $content, $options);
    }

    public function dl($data, $maps, $options = [])
    {
        $options += [
            'tags' => ['dt', 'dd']
        ];

        // normalize mapping
        $maps = $this->normalizeMaps($maps, $data, ['from' => 'dl']);

        // transform data
        $line = $this->transformMapsWithLine($maps, $data);

        // return html
        $html = implode(collection($maps)->map(function ($map, $index) use ($line, $options) {
            $dt = $this->tag($options['tags'][0], $map['label'][0], $map['label'][1]);
            $dd = $this->tag($options['tags'][1], $line[$index][0] . '&nbsp;', $line[$index][1]);
            return $dt . $dd;
        })->toArray());

        return $this->tag('dl', $html, $options);
    }
}
