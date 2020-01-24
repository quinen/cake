<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 20/12/18
 * Time: 13:52
 */

namespace QuinenCake\Model;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Table;

trait NextPrimaryKeyTrait
{
    /**
     * @param Event $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $this->generateNextPrimaryKey($event, $entity);
        return true;
    }

    protected function generateNextPrimaryKey(Event $event, EntityInterface $entity)
    {
        /* @var Table $subject */
        $subject = $event->getSubject();
        $primaryKey = $subject->getPrimaryKey();
        $primaryKeyType = $subject->getSchema()->getColumnType($primaryKey);

        // si pas de cle on la genere
        if (empty($entity->get($primaryKey)) && $primaryKeyType !== 'uuid') {
            // recupere la derniere valeur
            $lastEntity = $subject->find()->order([$primaryKey => "DESC"])->first();
            $lastPrimaryKeyValue = 0;
            if ($lastEntity !== null) {
                $lastPrimaryKeyValue = $lastEntity->get($primaryKey);
            }

            $entity->set($primaryKey, ++$lastPrimaryKeyValue);
        }
    }
}