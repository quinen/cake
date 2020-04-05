<?php
/**
 * @author Laurent DESMONCEAUX <laurent@quinen.net>
 * @created 31/08/2018
 * aller plus loin que https://github.com/quinen/cakephp3-plugin/blob/dev/src/Template/Element/Pages/icons.ctp
 * penser a autoswitcher le style en fonction d ela valeur soumise
 * @version 1.0
 *
 */

namespace QuinenCake\View\Helper;

/**
 *
 * @var \Cake\View\Helper\HtmlHelper $this
 */
trait GlyphiconTrait
{
    public function glyphicon($name, $options = [])
    {
        $options += [];

        // create icon
        $options = $this->addClass($options, 'glyphicon glyphicon-' . $name);

        return $this->Html->tag('i', "", $options).'&nbsp;';
    }
}
