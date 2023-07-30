<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @category   Horde
 * @copyright  2014-2016 Horde LLC
 * @license    http://www.horde.org/licenses/apache ASL
 * @package    Ingo
 * @subpackage UnitTests
 */

/**
 * Common library for Ingo test cases
 *
 * @author     Jason M. Felice <jason.m.felice@gmail.com>
 * @author     Michael Slusarz <slusarz@horde.org>
 * @category   Horde
 * @copyright  2014-2016 Horde LLC
 * @ignore
 * @license    http://www.horde.org/licenses/apache ASL
 * @package    Ingo
 * @subpackage UnitTests
 */

class Ingo_Unit_TestBase extends Horde_Test_Case
{
    protected $script;
    protected $storage;

    public function setUp(): void
    {
        $injector = $this->getMockBuilder('Horde_Injector')->disableOriginalConstructor()->getMock();
        $injector->expects($this->any())
            ->method('getInstance')
            ->will($this->returnCallback(array($this, '_injectorGetInstance')));
        $GLOBALS['injector'] = $injector;

        $prefs = $this->getMockBuilder('Horde_Prefs')->disableOriginalConstructor()->getMock();
        $prefs->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue(false));
        $GLOBALS['prefs'] = $prefs;

        $registry = $this->getMockBuilder('Horde_Registry')->disableOriginalConstructor()->getMock();
        $registry->expects($this->any())
            ->method('hasMethod')
            ->will($this->returnValue(true));
        $GLOBALS['registry'] = $registry;

        $GLOBALS['session'] = $this->getMockBuilder('Horde_Session')->getMock();

        if (!defined('INGO_BASE')) {
            define('INGO_BASE', realpath(__DIR__ . '/../../..'));
        }

        $this->storage = new Ingo_Storage_Mock(array(
            'maxblacklist' => 3,
            'maxwhitelist' => 3
        ));

        $GLOBALS['conf']['spam'] = array(
            'enabled' => true,
            'char' => '*',
            'header' => 'X-Spam-Level'
        );
    }

    public function _injectorGetInstance($interface)
    {
        switch ($interface) {
        case 'Horde_Core_Factory_Identity':
            $identity = $this->getMockBuilder('Horde_Core_Prefs_Identity')->disableOriginalConstructor()->getMock();
            $identity->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('Foo'));
            $identity->expects($this->any())
                ->method('getDefaultFromAddress')
                ->will($this->returnValue('foo@example.com'));
            $identity->expects($this->any())
                ->method('getValue')
                ->will($this->returnValue('XYZ'));

            $factory = $this->getMockBuilder($interface)->disableOriginalConstructor()->getMock();
            $factory->expects($this->any())
                ->method('create')
                ->will($this->returnValue($identity));

            return $factory;

        case 'Horde_Core_Perms':
            $perms = $this->getMockBuilder('Horde_Core_Perms')->disableOriginalConstructor()->getMock();
            $perms->method('hasAppPermission')->will($this->returnValue(true));
            return $perms;
        }
    }

    protected function _enableRule($rule)
    {
        $filters = $this->storage->retrieve(Ingo_Storage::ACTION_FILTERS);
        foreach ($filters->getFilterList() as $k => $v) {
            if ($v['action'] == $rule) {
                $v['disable'] = false;
                $filters->updateRule($v, $k);
                $this->storage->store($filters);
            }
        }
    }

    protected function _assertScript($expect)
    {
        $result = $this->script->generate();
        if (empty($result[0]['script'])) {
            $this->fail("result not a script", 1);
            return;
        }

        /* Remove comments and crunch whitespace so we can have a functional
         * comparison. */
        $new = array();
        foreach (explode("\n", $result[0]['script']) as $line) {
            if (preg_match('/^\s*$/', $line)) {
                continue;
            }
            if (preg_match('/^\s*#.*$/', $line)) {
                continue;
            }
            $new[] = trim($line);
        }

        $new_script = join("\n", $new);
        $this->assertEquals($expect, $new_script);
    }

}
