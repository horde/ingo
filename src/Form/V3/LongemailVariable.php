<?php

namespace Ingo\Form\V3;

use Horde\Form\V3\LongtextVariable;
use Horde_Variables;

/**
 * Copyright 2013-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @category  Horde
 * @copyright 2013-2026 Horde LLC
 * @license   http://www.horde.org/licenses/apache ASL
 * @package   Ingo
 */

/**
 * Do address validation on e-mail forms.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @author    yann@pleiades.fr.eu.org
 * @category  Horde
 * @copyright 2013-2026 Horde LLC
 * @license   http://www.horde.org/licenses/apache ASL
 * @package   Ingo
 */
class LongemailVariable extends LongtextVariable
{
    /**
     */
    public function isValid(Horde_Variables $vars, $value): bool
    {
        $value = trim((string)$value);

        if ($value === '') {
            if ($this->isRequired()) {
                return $this->invalid(_("This field is required."));
            }
            return true;
        }

        $invalid = [];
        $rfc822 = $GLOBALS['injector']->getInstance('Horde_Mail_Rfc822');

        foreach (explode("\n", $value) as $address) {
            try {
                $rfc822->parseAddressList($address, [
                    'validate' => true,
                ]);
            } catch (Horde_Mail_Exception $e) {
                $invalid[] = $address;
            }
        }

        if ($invalid) {
            return $this->invalid(sprintf(
                ngettext(
                    _("\"%s\" is not a valid email address."),
                    _("\"%s\" are not valid email addresses."),
                    count($invalid)
                ),
                implode(', ', $invalid)
            ));
        }

        return true;
    }

}
