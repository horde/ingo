<?php

/**
 * Copyright 2012-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/apache ASL
 * @package   Ingo
 */

/**
 * Ingo_Storage API implementation to save Ingo data via the Horde preferences
 * system.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @author    Jan Schneider <jan@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/apache ASL
 * @package   Ingo
 */
class Ingo_Storage_Prefs extends Ingo_Storage
{
    /**
     */
    protected function _loadFromBackend()
    {
        if ($rules = @unserialize($this->_prefs()->getValue('rules'), ['allowed_classes' => [
            'Ingo_Rule',
            'Ingo_Rule_Addresses',
            'Ingo_Rule_System_Blacklist',
            'Ingo_Rule_System_Forward',
            'Ingo_Rule_System_Spam',
            'Ingo_Rule_System_Vacation',
            'Ingo_Rule_System_Whitelist',
            'Ingo_Rule_User',
            'Ingo_Rule_User_Discard',
            'Ingo_Rule_User_FlagOnly',
            'Ingo_Rule_User_Keep',
            'Ingo_Rule_User_Move',
            'Ingo_Rule_User_MoveKeep',
            'Ingo_Rule_User_Notify',
            'Ingo_Rule_User_Redirect',
            'Ingo_Rule_User_RedirectKeep',
            'Ingo_Rule_User_Reject',
        ]])) {
            $this->_rules = $rules;
        }
    }

    /**
     */
    protected function _removeUserData($user)
    {
        $this->_prefs($user)->remove('rules');
    }

    /**
     */
    protected function _storeBackend($action, $rule)
    {
        switch ($action) {
            case self::STORE_ADD:
                if (!strlen($rule->uid)) {
                    $rule->uid = strval(new Horde_Support_Randomid());
                }
                break;
        }

        $this->_prefs()->setValue('rules', serialize($this->_rules));
    }

    /**
     * Get prefs object to use for storage.
     *
     * @param string $user  Username to use (if not default).
     *
     * @return Horde_Prefs  Prefs object.
     */
    protected function _prefs($user = null)
    {
        global $injector;

        return $injector->getInstance('Horde_Core_Factory_Prefs')->create('ingo', [
            'cache' => false,
            'user' => is_null($user) ? Ingo::getUser() : $user,
        ]);
    }

}
