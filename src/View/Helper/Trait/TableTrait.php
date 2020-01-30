<?php
/**
 * @var \Cake\View\View $this
 */

namespace QuinenCake\View\Helper;

use Cake\Collection\Collection;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\View\Helper\PaginatorHelper;

trait TableTrait
{
    /**
     * @param Query|ResultSet|array $datas données de la table
     * @param array $maps mapping des colonnes
     * @param array $options
     * @return mixed
     */
    public function table($datas, $maps = [], $options = [])
    {

        $optionsDefault = [
            'isPaginator' => true,
            'isSort' => true,
            'paginatorFormat' => '{{counterPages}}<br/>' .
                '{{first}}&nbsp;{{prev}}&nbsp;{{numbers}}&nbsp;{{next}}&nbsp;{{last}}<br/>' .
                '{{counterRange}}',
            'isThead' => true,
            'isTfoot' => true,
            'theadOptions' => null,
            'tfootOptions' => [],
            'trOptions' => [],
            'empty' => 'Aucun resultat',
            'emptyOptions' => [],
            'mapCallbackClass' => $this,
            'context' => null
        ];

        $options += $optionsDefault;

        //  Init Context    ////////////////////////////////////////////////////
        $isDataResultSet = $datas instanceof ResultSet;
        $isDataQuery = $datas instanceof Query;
        $isDataCollection = $datas instanceof Collection;
        if ($isDataResultSet || $isDataQuery || $isDataCollection) {
            if ($isDataResultSet || $isDataCollection) {
                $firstData = $datas->first();
            } elseif ($isDataQuery) {
                $firstData = $datas->cleanCopy()->first();
            }
            if ($firstData != null) {
                $this->setCurrentContext($firstData->getSource());
            }
            $datas = $datas->toArray();
        } else {
            $this->setCurrentContext($options['context']);
        }

        // Normalize Mapping    ////////////////////////////////////////////////
        $firstLine = [];
        if (isset($datas[0])) {
            $firstLine = $datas[0];
        }

        $mapsOptions = [
            'callbackClass' => $options['mapCallbackClass']
        ];

        $maps = $this->normalizeMaps($maps, $firstLine, $mapsOptions);
        // ce chiffre peut etre faux si label = [_,['colspan'=>n]]
        // $nbColumns + (n-1)
        $nbColumns = count($maps);

        // Pagination   ////////////////////////////////////////////////////////
        if (
            $options['isPaginator'] &&
            ($paginatorParams = $this->getView()->Paginator->params()) &&
            $paginatorParams
        ) {
            // confirmation pagination utile ? si params et nb > nb/page
            if ($paginatorParams['count'] <= $paginatorParams['perPage']) {
                $options['isPaginator'] = false;
            }
        } else {
            $options['isPaginator'] = false;
            $options['isSort'] = false;
        }

        // Tbody    ////////////////////////////////////////////////////////////
        // get value from field and format it
        $tbodyCollection = $this->transformMapsWithDatas($maps, $datas, $options['trOptions']);
        $tbodyArray = $tbodyCollection->toArray();


        $tbody = null;
        $isTbody = !empty($tbodyArray);
        if (!$isTbody) {

            $tbodyHtml = $this->tag('tr', $this->tag('th',
                $options['empty'],
                [
                    'colspan' => $nbColumns
                ] + $options['emptyOptions']
            ));
        } else {
            // eliminate rownum data and add td option
            $tbodyArray = $this->transformCellsForRowspan($maps, $tbodyArray);
            $tbodyArray = $this->transformCellsForColspan($maps, $tbodyArray);
            // write array in html
            $tbodyHtml = $this->tableRows($tbodyArray);
        }
        $tbody = $this->tag('tbody', $tbodyHtml);

        // Thead    ////////////////////////////////////////////////////////////
        $thead = null;
        if ($isTbody && $options['isThead']) {
            $thead = $this->getTableThead($maps, $options);
        }

        //  Tfoot   ////////////////////////////////////////////////////////////
        $tfoot = null;
        if ($options['isTfoot']) {
            if ($options['isPaginator']) {

                /** @var PaginatorHelper $paginator */
                $paginator = $this->getView()->Paginator;

                $formats = [
                    'first' => $paginator->first(),
                    'prev' => $paginator->prev(),
                    'numbers' => $paginator->numbers(),
                    'next' => $paginator->next(),
                    'last' => $paginator->last(),
                    'counterPages' => $paginator->counter('pages'),
                    'counterRange' => $paginator->counter('range'),
                    'limitControl' => $paginator->limitControl([], null, [
                        'label' => false
                    ]),
                ];
                $tfoot = \template($options['paginatorFormat'], $formats);
            } elseif (($nbRows = count($tbodyArray)) && $nbRows) {
                $tfoot .= __n('1 résultat ', $nbRows . ' résultats', $nbRows);
            }

            // enrobage
            if ($tfoot) {
                $tfoot = $this->tag('tfoot', $this->tag('tr', $this->tag('td',
                    $tfoot,
                    ['colspan' => $nbColumns, 'class' => ''])
                ), $options['tfootOptions']);
            }
        }

        $isPaginator = $options['isPaginator'];

        $options = array_diff_key($options, $optionsDefault);

        $table = $this->tag('table', $thead . $tbody . $tfoot, $options);

        if ($isPaginator) {
            $table = $this->div('paginator-container', $table);
        }

        return $table;
    }

    protected function transformCellsForRowspan($maps, $rows)
    {
        //debug([json_encode($maps), $rows]);
        $nbMaps = count($maps);
        $nbRows = count($rows);
        // pour chaque mapping
        for ($x = 0; $x < $nbMaps; $x++) {
            if ($maps[$x]['rowspan']) {
                // pour chaque ligne
                for ($y = 0; $y < $nbRows; $y++) {
                    $rowspan = 0;
                    do {
                        // ya t'il une valeur apres ?
                        $isNext = isset($rows[$y + $rowspan][0][$x][0]);
                        if ($isNext) {
                            // cette valeur est elle egale ?
                            $isEqual = $rows[$y + $rowspan][0][$x][0] == $rows[$y][0][$x][0];
                            if ($isEqual) {
                                $rowspan++;
                            }
                        }
                    } while ($isNext && $isEqual);

                    // manipulation de la cellule premiere
                    if ($rowspan > 1) {
                        $rows[$y][0][$x][1] += ['rowspan' => $rowspan];
                        // on elimine les lignes suivantes inutiles
                        while (--$rowspan > 0) {
                            unset($rows[$y + $rowspan][0][$x]);
                        }
                    }
                }
            }
        }
        return $rows;
    }

    protected function transformCellsForColspan($maps, $rows)
    {
        $nbMaps = count($maps);
        $nbRows = count($rows);

        // pour chaque ligne
        // pour chaque colonne
        // si colonne options colspan exist
        // on zappe n columns
        // fin si

        for ($y = 0; $y < $nbRows; $y++) {
            for ($x = 0; $x < $nbMaps; $x++) {
                if (isset($rows[$y][0][$x][1]['colspan'])) {
                    $colspan = $rows[$y][0][$x][1]['colspan'];
                    while (--$colspan > 0) {
                        unset($rows[$y][0][$x + $colspan]);
                    }
                }
            }
        }

        return $rows;
    }

    /**
     * copie de la methode $this->tableCells sans les options odd/even et integration d'un callback pour les
     * options des lignes TR
     * @param $data
     * @return string
     */
    protected function tableRows($data)
    {
        if (empty($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }

        $out = [];
        foreach ($data as $line) {
            list($line, $lineOptions) = $this->getContentOptions($line);
            $cellsOut = $this->_renderCells($line);
            $out[] = $this->tableRow(implode(' ', $cellsOut), $lineOptions);
        }

        return implode("\n", $out);
    }

    /**
     * @param $maps
     * @param array $options
     * @return mixed
     */
    protected function getTableThead($maps, $options = [])
    {
        $thead = [];

        $hasZones = collection($maps)->some(function ($map) {
            return isset($map['zone']);
        });

        if ($hasZones) {
            $zonesColspanned = $this->getTableTheadGroup($maps, ['fieldKey' => 'zone']);
            $thead[] = $this->tableHeaders($zonesColspanned, $options['theadOptions']);
        }

        $hasGroups = collection($maps)->some(function ($map) {
            return isset($map['group']);
        });

        if ($hasGroups) {
            $groupsColspanned = $this->getTableTheadGroup($maps, []);
            $thead[] = $this->tableHeaders($groupsColspanned, $options['theadOptions']);
        }

        $labelsColspanned = $this->getTableTheadLabel($maps, $options);

        $thead[] = $this->tableHeaders($labelsColspanned, $options['theadOptions']);

        return $this->tag('thead', implode($thead));
    }

    protected function getTableTheadGroup($maps, $options = [])
    {
        $options += [
            'fieldKey' => 'group'
        ];

        $groups = [];
        $nbMaps = count($maps);

        for ($i = 0; $i < $nbMaps; $i++) {

            if ($maps[$i]['hide']) {
                continue;
            }

            if (isset($maps[$i][$options['fieldKey']]) && $maps[$i][$options['fieldKey']] !== false) {
                list($group, $groupOptions) = $maps[$i][$options['fieldKey']];

                // if isset groupOptions.colspan set colspan
                // else colspan = 1
                if (!isset($groupOptions['colspan'])) {
                    $groupOptions['colspan'] = 1;
                }

                // i = 1
                $n = 1;

                // check if n+i has the same label
                // si oui i++
                while (isset($maps[$i + $n][$options['fieldKey']]) && $maps[$i + $n][$options['fieldKey']][0] === $group) {
                    unset($maps[$i + $n][$options['fieldKey']]);
                    $n++;
                }
                // si non colspan = i
                // ou plutot on zappe jusqu'au prochain non colspan
                $i += $groupOptions['colspan'] + $n - 2;
                $groupOptions['colspan'] += $n - 1;

                // if colspan == 1 then unset
                $groups[] = [$group => $groupOptions];
            } else {
                $groups[] = ['&nbsp;' => false];
            }
        }
        return $groups;
    }

    protected function getTableTheadLabel($maps, $options)
    {

        $labels = collection($maps)->reduce(function ($reducer, $map) use ($options) {

            if ($map['hide']) {
                return $reducer;
            }

            list($label, $labelOptions) = $map['label'];
            list($field, $fieldOptions) = $map['field'];

            if ($label === false) {
                $reducer[] = ['&nbsp;' => false];
                return $reducer;
            }

            if ($options['isSort'] && (!isset($map['isSort']) || $map['isSort'])) {
                if (!is_scalar($field)) {
                    $field = $field[0];
                }
                $reducer[] = [$this->getView()->Paginator->sort($field, $label, ['escape' => false]) => $labelOptions];
            } else {
                $reducer[] = [$label => $labelOptions];
            }

            return $reducer;

        }, []);

        // correctif sur les colspan
        return $this->getTableTheadColspan($labels);
    }

    protected function getTableTheadColspan($labels)
    {
        $colspan = 1;
        $labelsColspan = [];
        foreach ($labels as $label) {
            if ($colspan-- > 1) {
                continue;
            }

            $colspan = 1;
            if (isset(reset($label)['colspan'])) {
                $colspan = reset($label)['colspan'];
            }

            $labelsColspan[] = $label;
        }
        return $labelsColspan;
    }
    /*
        protected function getTablePaginator($options = [])
        {
            $options += [
                'template' => '{{counter}}{{limitControl}}',
                'format' => ' {{start}}-{{end}} de {{count}} résultats',
                'ulOptions' => [],
                'liOptions' => [],
                'aOptions' => [],
            ];

            $paginator = $this->getView()->Paginator;


            $counter = $this->tag('ul',
                $paginator->first('&lt;&lt;', ['escape' => false]) .
                $paginator->prev('&lt;', ['escape' => false]) .
                $this->tag(
                    'li',
                    $this->link($paginator->counter(
                        ['format' => $options['format']], ['escape' => false]
                    ), '#', $options['aOptions'])
                    ,
                    $options['liOptions']
                ) .
                $paginator->next('&gt;', ['escape' => false]) .
                $paginator->last('&gt;&gt;', ['escape' => false])
                , $options['ulOptions']
            );

            $limitControl = $paginator->limitControl([
                //5 => 5, 20 => 20,
            ], null, [
                'label' => false,
                'empty' => "Resultats / pages"
            ]);

            $html = template($options['template'], compact('counter', 'limitControl'));

            return $html;
        }
    */
}
