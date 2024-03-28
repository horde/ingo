<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Ingo
 */

/**
 * A Horde_Injector based Ingo_Transport factory.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Ingo
 */
class Ingo_Factory_Transport extends Horde_Core_Factory_Base
{
    /**
     * Returns a Ingo_Transport instance.
     *
     * @param array $transport  A transport driver name and parameter hash.
     *
     * @return Ingo_Transport  The Ingo_Transport instance.
     * @throws Ingo_Exception
     */
    public function create(array $transport)
    {
        global $registry;

        $coreHooks = $GLOBALS['injector']->getInstance('Horde_Core_Hooks');
        $transportDriver = $transport['driver'];
        $transportParams = $transport['params'];

        /* Get authentication parameters. */
        try {
            $auth = $coreHooks->callHook('transport_auth', 'ingo', [$transportDriver]);
        } catch (Horde_Exception_HookNotSet $e) {
            $auth = null;
        }

        if (!is_array($auth)) {
            $auth = [];
        }

        if (!isset($auth['password'])) {
            $auth['password'] = $registry->getAuthCredential('password');
        }
        if (!isset($auth['username'])) {
            $auth['username'] = $registry->getAuth('bare');
        }
        if (!isset($auth['euser'])) {
            $auth['euser'] = Ingo::getUser(false);
        }

        // Get transport parameters. 
        try {
            $customParams = $coreHooks->callHook('transport_params', 'ingo', [$transportDriver, $transportParams]);
        } catch (Horde_Exception_HookNotSet $e) {
            $customParams = null;
        }
        if (is_array($customParams)){
            $transportParams = array_merge($transportParams, $customParams);
        }

        $class = 'Ingo_Transport_' . ucfirst($transportDriver);
        if (class_exists($class)) {
            return new $class(array_merge($auth, $transportParams));
        }

        throw new Ingo_Exception(sprintf(_("Unable to load the transport driver \"%s\"."), $class));
    }

}
