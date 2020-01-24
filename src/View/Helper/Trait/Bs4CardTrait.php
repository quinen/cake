<?php

namespace QuinenCake\View\Helper;

trait Bs4CardTrait
{
    public function cards($cards)
    {
        return implode(collection($cards)->map(function ($card) {
            return $this->card($card['content'], [
                'header' => $card['tab']
            ]);
        })->toArray());
    }

    public function card($body, $options = [])
    {
        if (is_array($body)) {
            $options = $body;
        } else {
            $options += ['body' => $body];
        }

        $options += [
            'header' => false,
            'title' => false,
            'buttons' => [],
            'body' => false,
            'footer' => false,
            'content' => false
        ];


        if (!$options['header']) {
            $options['header'] = [];

            if ($options['title']) {

                list($title, $titleOptions) = $this->getContentOptions($options['title']);

                $titleOptions += [
                    'align' => 'left'
                ];

                $titleOptions = $this->Html->addClass($titleOptions, 'h5 text-' . $titleOptions['align'] . ' mt-2');
                unset($titleOptions['align']);

                $options['header'][] = $this->Html->tag('div', $title, $titleOptions);
            }

            if ($options['buttons']) {
                $options['header'][] = $this->buttons($options['buttons'], ['class' => "float-right"]);
            }

            $options['header'] = implode($options['header']);
        }

        $header = null;
        if ($options['header']) {
            $header = $this->Html->div('card-header pr-2-5', $options['header']);
            //$header = $this->Html->div('card-header', $options['header']);
        }

        $body = null;
        if ($options['body']) {
            $body = $this->Html->div('card-body', $options['body']);
        }


        $content = null;
        if ($options['content']) {
            $content = $options['content'];
        }

        $footer = null;
        if ($options['footer']) {
            $footer = $this->Html->div('card-footer', $options['footer']);
        }

        return $this->Html->div('card', $header . $body . $content . $footer);
    }

    public function formInCard($controls, $controlsOptions = [], $cardOptions = [])
    {
        $controlsOptions += [
            'legend' => false,
            'submit' => "formInCard"
        ];

        $cardTitle = $controlsOptions['legend'];
        $controlsOptions['legend'] = false;

        $controls = $this->Form->controls($controls, $controlsOptions);

        list($submit, $submitOptions) = $this->getContentOptions($controlsOptions['submit']);
        unset($controlsOptions['submit']);

        $submitOptions = [
                'isMb3' => false
            ] + $submitOptions;

        $cardOptions += [
            'title' => $cardTitle,
            'footer' => $this->submitRow($submit, $submitOptions)
        ];


        return $this->Form->create() .
            $this->card(
                $controls, $cardOptions) .
            $this->Form->end();
    }

}