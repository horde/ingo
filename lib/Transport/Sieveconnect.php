<?php
/**
 * Copyright 2017-2018 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author   Ralf Lang <lang@gb1-systems.de>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Ingo
 */

/**
 * Ingo_Transport_Sieveconnect implements an Ingo transport driver to
 * delegate ManageSieve connections to an external sieve-connect script
 *
 * @author   Mike Cochrane <mike@graftonhall.co.nz>
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Ingo
 */
class Ingo_Transport_Sieveconnect extends Ingo_Transport_Base
{
    /**
     */
    protected $_supportShares = true;

    /**
     * Constructor.
     */
    public function __construct(array $params = array())
    {
        parent::__construct(array_merge(array(
            'admin'      => '',
            'debug'      => false,
            'euser'      => '',
            'hostspec'   => 'localhost',
            'logintype'  => 'PLAIN',
            'port'       => 4190,
            'scriptname' => 'ingo',
            'usetls'     => true,
            'script'     => '/usr/bin/sieve-connect',
            'tmpdir'     => $GLOBALS['conf']['tmpdir']
        ), $params));
    }

    /**
     * Sets a script running on the backend.
     *
     * @param array $script  The filter script information. Passed elements:
     *                       - 'name': (string) the script name.
     *                       - 'recipes': (array) the filter recipe objects.
     *                       - 'script': (string) the filter script.
     *
     * @throws Ingo_Exception
     */
    public function setScriptActive($script)
    {
        $scriptFile = $this->_params['tmpdir'] . '/transport' . md5($this->_params['username'] . $script['name']);
        file_put_contents($scriptFile, $script['script']);
        $cmd = sprintf("echo '%s' | %s -s %s -p %s -u %s --upload  --localsieve %s --remotesieve %s ; echo '%s' | %s -s %s -p %s -u %s --activate --remotesieve %s",
            $this->_params['password'],
            $this->_params['script'],
            $this->_params['hostspec'],
            $this->_params['port'],
            $this->_params['username'],
            $scriptFile,
            $script['name'],
            $this->_params['password'],
            $this->_params['script'],
            $this->_params['hostspec'],
            $this->_params['port'],
            $this->_params['username'],
            $script['name']
        );
        if (!empty($this->_params['debug'])) {
            file_put_contents($scriptFile . 'debug', $cmd);
        }
        shell_exec($cmd);
        unlink($scriptFile);
    }

    /**
     * Returns the content of the currently active script.
     *
     * @return array  Keys 'name' (string) the active script, 'script' (string)  The complete ruleset of the specified user.
     * @throws Ingo_Exception
     * @throws Horde_Exception_NotFound
     */
    public function getScript()
    {
        $cmdList = sprintf("echo '%s' | %s -s %s -p %s -u %s --list | grep ACTIVE",
            $this->_params['password'],
            $this->_params['script'],
            $this->_params['hostspec'],
            $this->_params['port'],
            $this->_params['username']
        );
        $files = shell_exec($cmdList);
        list(, $name,) = explode('"', $files, 3);
        $cmdDownload = sprintf("echo '%s' | %s -s %s -p %s -u %s --download  --localsieve - --remotesieve %s",
            $this->_params['password'],
            $this->_params['script'],
            $this->_params['hostspec'],
            $this->_params['port'],
            $this->_params['username'],
            $name
        );
        $rules = shell_exec($cmdDownload);

        return array('name' => $name, 'script' => $rules);
    }
}
