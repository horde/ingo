<?php

/**
 * Copyright 2026 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author     Torben Dannhauer <torben@dannhauer.de>
 * @category   Horde
 * @copyright  2026 The Horde Project
 * @license    http://www.horde.org/licenses/apache ASL
 * @package    Ingo
 * @subpackage UnitTests
 */

use PHPUnit\Framework\TestCase;

/**
 * Ensure prefs/Mongo unserialize allowlist includes nested mail objects.
 *
 * Regression for TypeError on count() when Horde_Mail_Rfc822_List became
 * __PHP_Incomplete_Class after the ZDI-20-1051 allowed_classes lockdown.
 */
class Ingo_Unit_StorageUnserializeTest extends TestCase
{
    public function testWhitelistRoundtripPreservesAddresses()
    {
        $rule = new Ingo_Rule_System_Whitelist();
        $ref = new ReflectionProperty(Ingo_Rule_Addresses::class, '_addr');
        $ref->setAccessible(true);
        $ref->setValue(
            $rule,
            new Horde_Mail_Rfc822_List(['alice@example.com', 'bob@example.com'])
        );

        $serialized = serialize([$rule]);
        $rules = unserialize($serialized, [
            'allowed_classes' => Ingo_Storage::unserializeAllowedClasses(),
        ]);

        $this->assertCount(1, $rules);
        $this->assertInstanceOf(Ingo_Rule_System_Whitelist::class, $rules[0]);
        $this->assertSame(2, count($rules[0]));
        $this->assertSame(
            ['alice@example.com', 'bob@example.com'],
            $rules[0]->addresses
        );
        $this->assertInstanceOf(
            Horde_Mail_Rfc822_List::class,
            $rules[0]->addressList
        );
    }

    public function testAllowlistIncludesNestedMailClasses()
    {
        $allowed = Ingo_Storage::unserializeAllowedClasses();

        $this->assertContains('Horde_Mail_Rfc822_List', $allowed);
        $this->assertContains('Horde_Mail_Rfc822_Address', $allowed);
        $this->assertContains('Horde_Mail_Rfc822_Group', $allowed);
        $this->assertContains('Horde_Mail_Rfc822_GroupList', $allowed);
        $this->assertContains('Ingo_Rule_System_Whitelist', $allowed);
        $this->assertContains('Ingo_Rule_System_Blacklist', $allowed);
        $this->assertContains('Ingo_Rule_System_Vacation', $allowed);
    }
}
