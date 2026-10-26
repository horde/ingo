<?php

/**
 * Copyright 2014-2026 Horde LLC (http://www.horde.org/)
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
 * @coversNothing
 */

namespace Horde\Ingo\Test\Unit;
use PHPUnit\Framework\TestCase;


abstract class TestBase extends TestCase
{
    protected $script;
    protected $storage;

    protected function setUp(): void
    {
        $injector = $this->getMockBuilder('Horde_Injector')
                 ->disableOriginalConstructor()
                 ->getMock();

        $injector->method('getInstance')
         ->willReturnCallback(fn(...$args) => $this->_injectorGetInstance(...$args));

        $GLOBALS['injector'] = $injector;

        $prefs = $this->getMockBuilder('Horde_Prefs')
                 ->disableOriginalConstructor()
                 ->getMock();

        $prefs->method('getValue')
            ->willReturn(false);

        $GLOBALS['prefs'] = $prefs;

        $registry = $this->getMockBuilder('Horde_Registry')
                 ->disableOriginalConstructor()
                 ->getMock();

        $registry->method('hasMethod')
             ->willReturn(true);

        $GLOBALS['registry'] = $registry;

        $GLOBALS['session'] = $this->createMock('Horde_Session');

        if (!defined('INGO_BASE')) {
            define('INGO_BASE', realpath(__DIR__ . '/../../..'));
        }

        $this->storage = new \Ingo_Storage_Memory();

        $GLOBALS['conf']['spam'] = [
            'enabled' => true,
            'char' => '*',
            'header' => 'X-Spam-Level',
        ];
    }

    public function _injectorGetInstance($interface)
    {
        switch ($interface) {
            case 'Horde_Core_Factory_Identity':
                $identity = $this->getMockBuilder('Horde_Core_Prefs_Identity')
                     ->disableOriginalConstructor()
                     ->getMock();

                $identity->method('getName')
                         ->willReturn('Foo');

                $identity->method('getDefaultFromAddress')
                         ->willReturn('foo@example.com');

                $identity->method('getValue')
                         ->willReturn('XYZ');

                $factory = $this->getMockBuilder($interface)
                     ->disableOriginalConstructor()
                     ->getMock();

                $factory->method('create')
                        ->willReturn($identity);

                return $factory;

            case 'Horde_Core_Hooks':
                $hooks = $this->getMockBuilder('Horde_Core_Hooks')
                     ->disableOriginalConstructor()
                     ->getMock();

                $hooks->method('callHook')
                      ->willReturnCallback(fn(...$args) => $this->_hooksCallback(...$args));

                return $hooks;

            case 'Horde_Core_Perms':
                $perms = $this->getMockBuilder('Horde_Core_Perms')
                     ->disableOriginalConstructor()
                     ->getMock();

                $perms->method('hasAppPermission')
                      ->willReturn(true);

                return $perms;
        }
    }

    public function _hooksCallback()
    {
        throw new \Horde_Exception_HookNotSet();
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
        $new = [];
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
