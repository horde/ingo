<?php

/**
 * Copyright 2015-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/apache ASL
 * @package   Ingo
 */

/**
 * A rule that deals with an address list.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/apache ASL
 * @package   Ingo
 *
 * @property-read string[] $addresses                  The list of addresses.
 * @property-read Horde_Mail_Rfc822_List $addressList  The list of addresses.
 * @property-write string[]|string $addresses          A list of addresses.
 */
class Ingo_Rule_Addresses extends Ingo_Rule implements Countable
{
    /**
     * Address list.
     *
     * @var Horde_Mail_Rfc822_List
     */
    protected $_addr;

    /**
     * Permission name.
     *
     * @var string
     */
    protected $_perm = null;

    /**
     */
    public function __construct()
    {
        $this->_addr = new Horde_Mail_Rfc822_List();
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
            case 'addresses':
                return $this->_addressList()->bare_addresses;
            case 'addressList':
                return $this->_addressList();
        }
    }

    /**
     */
    public function __set($name, $data)
    {
        switch ($name) {
            case 'addresses':
                $this->_addr = new Horde_Mail_Rfc822_List();
                $this->addAddresses(
                    is_array($data) ? $data : preg_split("/\s+/", $data)
                );
                break;
            case 'addressList':
                if (!($data instanceof Horde_Mail_Rfc822_List)) {
                    throw new InvalidArgumentException('Value for addressList is not a Horde_Mail_Rfc822_List object');
                }
                $this->_addr = $data;
                break;
        }
    }

    /**
     * Ensure $_addr is a usable list (guards incomplete unserialize).
     *
     * @return Horde_Mail_Rfc822_List
     */
    protected function _addressList()
    {
        if (!($this->_addr instanceof Horde_Mail_Rfc822_List)) {
            $this->_addr = new Horde_Mail_Rfc822_List();
        }

        return $this->_addr;
    }

    /**
     * Add addresses to the current address list.
     *
     * @param mixed $add  Addresses to add.
     *
     * @throws Ingo_Exception
     */
    public function addAddresses($to_add)
    {
        global $injector;

        $addr = clone $this->_addressList();

        $addr->add($to_add);
        $addr->unique();

        $max = is_null($this->_perm)
            ? false
            : $injector->getInstance('Horde_Core_Perms')->hasAppPermission(
                Ingo_Perms::getPerm($this->_perm)
            );

        if (($max !== true) && !empty($max)) {
            $addr_count = count($addr);
            if ($addr_count > $max) {
                throw $this->_setAddressesException($addr_count, $max);
            }
        }

        $this->_addr = $addr;
    }

    /**
     * @return Ingo_Excception
     */
    protected function _setAddressesException($addr_count, $max)
    {
        return new Ingo_Exception();
    }

    /* Countable method. */

    /**
     */
    public function count(): int
    {
        return count($this->_addressList());
    }

}
