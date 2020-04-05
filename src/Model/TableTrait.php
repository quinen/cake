<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 20/12/18
 * Time: 15:01
 */

namespace QuinenCake\Model;


use Cake\ORM\Query;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

trait TableTrait
{
    /**
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findPeriode(Query $query, $options = [])
    {
        $options += [
            'start' => false,
            'end' => false,
            'field' => false
        ];

        if (!$options['field']) {
            // TODO : throw an NoFieldForPeriodSubmittedException
            return $query;
        }

        if ($options['start']) {
            $query->where([
                $options['field'] . ' >=' => $options['start']
            ]);
        }

        if ($options['end']) {
            $query->where([
                $options['field'] . ' <' => $query->func()->dateAdd($options['end'], 1, 'DAY', ['date']),
            ]);
        }
        return $query;
    }


    public function simpleInsert($data)
    {
        return $this->simpleSave(null, $data);
    }

    public function simpleSave($entity, $data)
    {
        if ($entity === null) {
            $entity = $this->newEntity();
        }
        $this->patchEntity($entity, $data);
        return $this->save($entity);
    }


    /**
     * find list always ordered by display field
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findList(Query $query, array $options = [])
    {
        return parent::findList($query, $options)
            ->order([$this->aliasField($this->getDisplayField()) => 'ASC']);
    }

    public function findSortDirection(Query $query, array $options = [], array $defaults = [])
    {
        $sort = Hash::get($options, 'sort');
        if ($sort !== null) {
            $sort = explode('.', $sort);
            $nbSort = count($sort);
            if ($nbSort === 1) {
                $sort = $this->aliasField($sort[0]);
            } else {
                $sortClassIndex = $nbSort - 2;
                $sort[$sortClassIndex] = Inflector::pluralize(Inflector::classify($sort[$sortClassIndex]));
                $sort = $sort[$sortClassIndex] . '.' . $sort[$sortClassIndex + 1];
            }
            $query->order([$sort => $options['direction']]);
        } else {
            $query->order($defaults);
        }
    }
}