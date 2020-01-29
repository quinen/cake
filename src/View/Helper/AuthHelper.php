<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/29/20
 * Time: 8:05 AM
 */

namespace QuinenCake\View\Helper;

use Cake\View\Helper;

class AuthHelper extends Helper
{
    const SESSION_KEY = 'Auth.User';
    public $helpers = [];

    /*
     * Methode a surcharger pour implementer sa propre logique d'authentification
     *
     * */
    public function check($url)
    {
        return $this->isUser();
    }

    public function isUser()
    {
        return !empty($this->user());
    }

    public function user($key = null)
    {
        $fullKey = $this->getSessionKey($key);
        return $this->getView()->getRequest()->getSession()->read($fullKey);
    }

    public function getSessionKey($key = null, $prefix = self::SESSION_KEY)
    {
        if ($key === null) {
            return $prefix;
        } else {
            return $prefix . '.' . $key;
        }
    }
}
