<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 05/11/18
 * Time: 09:20
 * @var \QuinenCake\View\Helper\FormHelper $this
 */

namespace QuinenCake\View\Helper;

use QuinenLib\Arrays\ContentOptionsTrait;

trait Bs3FormTrait
{
    use ContentOptionsTrait;

    public function submitRow($submit = "Valider", $options = [])
    {
        $options += [
            'buttons' => []
        ];
        if (is_array($submit)) {
            $submitOptions = $submit;
        } else {
            $submitOptions = ['text' => $submit];
        }

        $submitOptions = $submitOptions + ['button' => 'submit'];

        $buttons = [
            $this->getView()->Bs3->button('reset'),
            $this->getView()->Bs3->button($submitOptions)
        ];

        $htmlButtons = implode('&nbsp;', array_merge($buttons, $options['buttons']));


        return $this->getView()->Html->div(
            'row',
            $this->getView()->Html->div(
                'col-md-offset-2 col-md-10',
                $htmlButtons
            )
        );
    }
}
