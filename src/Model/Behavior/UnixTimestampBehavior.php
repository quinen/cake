<?php

namespace QuinenCake\Model\Behavior;

use Cake\I18n\Time;
use Cake\ORM\Behavior\TimestampBehavior;
use DateTime;


class UnixTimestampBehavior extends TimestampBehavior
{
    /**
     * Get or set the timestamp to be used
     *
     * Set the timestamp to the given DateTime object, or if not passed a new DateTime object
     * If an explicit date time is passed, the config option `refreshTimestamp` is
     * automatically set to false.
     *
     * @param \DateTime $ts Timestamp
     * @param bool $refreshTimestamp If true timestamp is refreshed.
     * @return \Cake\I18n\Time
     */
    public function timestamp(DateTime $ts = null, $refreshTimestamp = false)
    {
        if ($ts) {
            if ($this->_config['refreshTimestamp']) {
                $this->_config['refreshTimestamp'] = false;
            }
            $this->_ts = new Time($ts);
        } elseif ($this->_ts === null || $refreshTimestamp) {
            $this->_ts = new Time();
        }

        return intval($this->_ts->toUnixString());
    }

}