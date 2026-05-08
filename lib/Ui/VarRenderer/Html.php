<?php

/**
 * Copyright 2006-2026 Horde LLC (http://www.horde.org/)
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
 * Extension of Horde's variable renderer that support Ingo's folders variable
 * type.
 *
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Ingo
 */
class Ingo_Ui_VarRenderer_Html extends Horde_Core_Ui_VarRenderer_Html
{
    public function __construct($params = [])
    {
        parent::__construct($params);
    }

    protected function _renderVarInput_ingo_form_type_folders($form, $var, $vars)
    {
        return Ingo_Flist::select($var->getFolder(), 'folder');
    }

    protected function _renderVarInput_ingo_form_type_longemail($form, $var, $vars)
    {
        return $this->_renderVarInput_longtext($form, $var, $vars);
    }
}
