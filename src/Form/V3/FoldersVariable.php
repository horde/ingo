<?php

namespace Horde\Ingo\Form\V3;

use Horde\Util\Variables;
use Horde\Form\V3\BaseVariable;
use Horde_Variables;

/**
 * Copyright 2012-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Ingo
 */

/**
 * Dummy class to hold the select box created by {@link Ingo_Flist::select()}.
 *
 * @see Horde_Core_Ui_VarRenderer_Ingo
 * @see Ingo_Flist::select()
 */

class FoldersVariable extends BaseVariable
{
    public $_folder;
    public $newFolderSet;

    /**
     * Return the legacy type name so Ingo_Ui_VarRenderer_Html's
     * _renderVarInput_ingo_form_type_folders() is still dispatched to after
     * the class was moved from Ingo\Form\V3 to Horde\Ingo\Form\V3.
     */
    public function getTypeName(): string
    {
        return 'ingo_form_type_folders';
    }

    public function isValid(Horde_Variables|Variables $vars, $value): bool
    {
        if ($this->newFolderSet || strlen($value)) {
            return true;
        }

        return $this->invalid(_("A target folder is required."));
    }

    public function getFolder()
    {
        return $this->_folder;
    }

    public function setFolder($folder)
    {
        $this->_folder = $folder;
    }

}
