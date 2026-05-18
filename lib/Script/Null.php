<?php

/**
 * Copyright 2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Ingo
 */

/**
 * The Ingo_Script_Null class represents a development/testing backend that
 * stores rules but never deploys them to any mail server.
 *
 * This driver is useful for:
 * - UI development without requiring mail server infrastructure
 * - Testing rule management features
 * - Demonstrating Ingo capabilities
 *
 * Rules are stored normally using Ingo's storage layer but are never actually
 * deployed. The "script" output is a human-readable description of what the
 * rules would do if they were deployed.
 *
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Ingo
 */
class Ingo_Script_Null extends Ingo_Script_Base
{
    /**
     * A list of driver features.
     *
     * @var array
     */
    protected $_features = [
        /* Can tests be case sensitive? */
        'case_sensitive' => true,
        /* Does the driver support setting IMAP flags? */
        'imap_flags' => true,
        /* Can this driver perform on demand filtering? */
        'on_demand' => false,
        /* Does the driver require a script file to be generated? */
        'script_file' => false,
        /* Does the driver support the stop-script option? */
        'stop_script' => true,
        /* Does the driver support vacation start and end on time level? */
        'vacation_time' => true,
    ];

    /**
     * The list of actions allowed (implemented) for this driver.
     * Null driver supports all action types.
     *
     * @var array
     */
    protected $_actions = [
        'Ingo_Rule_User_Discard',
        'Ingo_Rule_User_FlagOnly',
        'Ingo_Rule_User_Keep',
        'Ingo_Rule_User_Move',
        'Ingo_Rule_User_MoveKeep',
        'Ingo_Rule_User_Notify',
        'Ingo_Rule_User_Redirect',
        'Ingo_Rule_User_RedirectKeep',
        'Ingo_Rule_User_Reject',
    ];

    /**
     * The categories of filtering allowed.
     * Null driver supports all system rule types.
     *
     * @var array
     */
    protected $_categories = [
        'Ingo_Rule_System_Blacklist',
        'Ingo_Rule_System_Forward',
        'Ingo_Rule_System_Spam',
        'Ingo_Rule_System_Vacation',
        'Ingo_Rule_System_Whitelist',
    ];

    /**
     * Which form fields are supported in each category by this driver?
     * Null driver supports all vacation features.
     *
     * @var array
     */
    protected $_categoryFeatures = [
        'Ingo_Rule_System_Vacation' => [
            'period',
            'subject',
            'reason',
            'addresses',
            'excludes',
            'ignorelist',
            'days',
            'time',
        ],
    ];

    /**
     * The list of tests allowed (implemented) for this driver.
     * Null driver supports all test types.
     *
     * @var array
     */
    protected $_tests = [
        'contains',
        'not contain',
        'is',
        'not is',
        'begins with',
        'not begins with',
        'ends with',
        'not ends with',
        'exists',
        'not exist',
        'less than',
        'less than or equal to',
        'equal',
        'not equal',
        'greater than',
        'greater than or equal to',
        'regex',
        'not regex',
        'matches',
        'not matches',
    ];

    /**
     * The types of tests allowed (implemented) for this driver.
     * Null driver supports all test categories.
     *
     * @var array
     */
    protected $_types = [
        Ingo_Rule_User::TEST_HEADER,
        Ingo_Rule_User::TEST_SIZE,
        Ingo_Rule_User::TEST_BODY,
    ];

    /**
     * Generates a human-readable description of the filtering rules.
     *
     * The null driver does not generate actual mail filtering scripts.
     * Instead, it produces a readable text description of what the rules
     * would do if they were deployed.
     *
     * @return string  Human-readable description of rules
     */
    public function generate()
    {
        if (!isset($this->_params['storage'])) {
            return "No storage backend configured.\n";
        }

        $output = [];
        $output[] = "Mail Filtering Rules (Null Driver - Not Active)";
        $output[] = str_repeat('=', 60);
        $output[] = '';
        $output[] = 'NOTE: This is a development/testing driver. Rules are stored';
        $output[] = 'but NOT deployed to any mail server. No actual filtering occurs.';
        $output[] = '';

        // Load filters using iterator
        $filters = Ingo_Storage_FilterIterator_Skip::create(
            $this->_params['storage'],
            $this->_params['skip'] ?? []
        );

        $ruleCount = 0;
        foreach ($filters as $rule) {
            if ($rule->disable) {
                continue;
            }

            $class = get_class($rule);

            switch ($class) {
                case 'Ingo_Rule_System_Blacklist':
                    $output[] = $this->_formatBlacklist($rule);
                    break;

                case 'Ingo_Rule_System_Whitelist':
                    $output[] = $this->_formatWhitelist($rule);
                    break;

                case 'Ingo_Rule_System_Vacation':
                    $output[] = $this->_formatVacation($rule);
                    break;

                case 'Ingo_Rule_System_Forward':
                    $output[] = $this->_formatForward($rule);
                    break;

                case 'Ingo_Rule_System_Spam':
                    $output[] = $this->_formatSpam($rule);
                    break;

                default:
                    // User-defined custom rules
                    if (in_array($class, $this->_actions)) {
                        $ruleCount++;
                        $output[] = $this->_formatUserRule($rule, $ruleCount);
                    }
                    break;
            }
        }

        if ($ruleCount === 0 && count($output) === 5) {
            $output[] = 'No filtering rules configured.';
            $output[] = '';
        }

        return implode("\n", $output);
    }

    /**
     * Format a blacklist rule for display.
     *
     * @param Ingo_Rule_System_Blacklist $rule  The blacklist rule
     *
     * @return string  Formatted description
     */
    protected function _formatBlacklist($rule)
    {
        $output = [];
        $output[] = 'BLACKLIST:';

        if (!empty($rule->addresses)) {
            $output[] = '  Blocked addresses:';
            foreach ($rule->addresses as $address) {
                $output[] = '    - ' . $address;
            }
        } else {
            $output[] = '  (No addresses blocked)';
        }

        $output[] = '  Action: ' . $this->_getBlacklistAction($rule);
        $output[] = '';

        return implode("\n", $output);
    }

    /**
     * Get blacklist action description.
     *
     * @param Ingo_Rule_System_Blacklist $rule  The blacklist rule
     *
     * @return string  Action description
     */
    protected function _getBlacklistAction($rule)
    {
        if (!empty($rule->folder)) {
            return 'Move to folder "' . $rule->folder . '"';
        }
        return 'Delete message';
    }

    /**
     * Format a whitelist rule for display.
     *
     * @param Ingo_Rule_System_Whitelist $rule  The whitelist rule
     *
     * @return string  Formatted description
     */
    protected function _formatWhitelist($rule)
    {
        $output = [];
        $output[] = 'WHITELIST:';

        if (!empty($rule->addresses)) {
            $output[] = '  Always allow addresses:';
            foreach ($rule->addresses as $address) {
                $output[] = '    - ' . $address;
            }
        } else {
            $output[] = '  (No addresses whitelisted)';
        }

        $output[] = '';

        return implode("\n", $output);
    }

    /**
     * Format a vacation rule for display.
     *
     * @param Ingo_Rule_System_Vacation $rule  The vacation rule
     *
     * @return string  Formatted description
     */
    protected function _formatVacation($rule)
    {
        $output = [];
        $output[] = 'VACATION AUTO-REPLY:';

        if (!empty($rule->start) && !empty($rule->end)) {
            $output[] = '  Period: ' . date('Y-m-d', $rule->start)
                       . ' to ' . date('Y-m-d', $rule->end);
        }

        if (!empty($rule->subject)) {
            $output[] = '  Subject: ' . $rule->subject;
        }

        if (!empty($rule->reason)) {
            $output[] = '  Message:';
            $lines = explode("\n", $rule->reason);
            foreach ($lines as $line) {
                $output[] = '    ' . $line;
            }
        }

        if (!empty($rule->addresses)) {
            $output[] = '  Reply from: ' . implode(', ', $rule->addresses);
        }

        if (!empty($rule->days)) {
            $output[] = '  Reply interval: Every ' . $rule->days . ' days';
        }

        $output[] = '';

        return implode("\n", $output);
    }

    /**
     * Format a forward rule for display.
     *
     * @param Ingo_Rule_System_Forward $rule  The forward rule
     *
     * @return string  Formatted description
     */
    protected function _formatForward($rule)
    {
        $output = [];
        $output[] = 'FORWARD:';

        if (!empty($rule->addresses)) {
            $output[] = '  Forward to:';
            foreach ($rule->addresses as $address) {
                $output[] = '    - ' . $address;
            }
        } else {
            $output[] = '  (No forward addresses configured)';
        }

        if (!empty($rule->keep)) {
            $output[] = '  Keep copy: Yes';
        } else {
            $output[] = '  Keep copy: No';
        }

        $output[] = '';

        return implode("\n", $output);
    }

    /**
     * Format a spam rule for display.
     *
     * @param Ingo_Rule_System_Spam $rule  The spam rule
     *
     * @return string  Formatted description
     */
    protected function _formatSpam($rule)
    {
        $output = [];
        $output[] = 'SPAM FILTERING:';

        if (!empty($rule->folder)) {
            $output[] = '  Action: Move to folder "' . $rule->folder . '"';
        } else {
            $output[] = '  Action: Delete message';
        }

        $output[] = '';

        return implode("\n", $output);
    }

    /**
     * Format a user-defined rule for display.
     *
     * @param Ingo_Rule_User $rule   The user rule
     * @param integer $number        Rule number
     *
     * @return string  Formatted description
     */
    protected function _formatUserRule($rule, $number)
    {
        $output = [];
        $output[] = 'RULE ' . $number . ': ' . $rule->name;

        // Format conditions
        if (!empty($rule->conditions)) {
            $output[] = '  When:';
            $combine = ($rule->combine == Ingo_Rule_User::COMBINE_ALL) ? 'AND' : 'OR';

            foreach ($rule->conditions as $index => $condition) {
                $condStr = '    ';
                if ($index > 0) {
                    $condStr .= $combine . ' ';
                }
                $condStr .= $this->_formatCondition($condition);
                $output[] = $condStr;
            }
        }

        // Format action
        $output[] = '  Then: ' . $this->_formatAction($rule);

        if ($rule->stop) {
            $output[] = '  Stop: Yes (stop processing further rules)';
        }

        $output[] = '';

        return implode("\n", $output);
    }

    /**
     * Format a single condition for display.
     *
     * @param array $condition  The condition array
     *
     * @return string  Formatted condition
     */
    protected function _formatCondition($condition)
    {
        $field = $condition['field'] ?? 'Unknown';
        $match = $condition['match'] ?? 'is';
        $value = $condition['value'] ?? '';

        return $field . ' ' . $match . ' "' . $value . '"';
    }

    /**
     * Format an action for display.
     *
     * @param Ingo_Rule_User $rule  The rule with action
     *
     * @return string  Formatted action
     */
    protected function _formatAction($rule)
    {
        $class = get_class($rule);

        switch ($class) {
            case 'Ingo_Rule_User_Move':
                return 'Move to folder "' . $rule->value . '"';

            case 'Ingo_Rule_User_MoveKeep':
                return 'Copy to folder "' . $rule->value . '"';

            case 'Ingo_Rule_User_Discard':
                return 'Delete message';

            case 'Ingo_Rule_User_Redirect':
                return 'Redirect to ' . $rule->value;

            case 'Ingo_Rule_User_RedirectKeep':
                return 'Redirect to ' . $rule->value . ' (keep copy)';

            case 'Ingo_Rule_User_Reject':
                return 'Reject with message: "' . $rule->value . '"';

            case 'Ingo_Rule_User_FlagOnly':
                return 'Set IMAP flag: ' . $rule->value;

            case 'Ingo_Rule_User_Keep':
                return 'Keep in INBOX';

            case 'Ingo_Rule_User_Notify':
                return 'Send notification to ' . $rule->value;

            default:
                return 'Unknown action';
        }
    }

    /**
     * Performs on-demand filtering.
     *
     * The null driver does not perform any actual filtering since rules
     * are never deployed to a mail server. This method is a no-op.
     *
     * @param integer $change  The timestamp of the latest rule change.
     */
    protected function _perform($change)
    {
        // Null driver does nothing - rules are not deployed
        // This is intentionally a no-op
    }

    /**
     * Is perform() available?
     *
     * The null driver cannot perform on-demand filtering since rules
     * are never actually deployed.
     *
     * @return boolean  Always false for null driver.
     */
    public function canPerform()
    {
        // Null driver cannot perform on-demand filtering
        return false;
    }

    /**
     * Activates or deactivates scripts.
     *
     * The null driver does not deploy scripts, so activation is a no-op.
     * This method succeeds silently to avoid breaking the normal Ingo
     * workflow.
     *
     * @param boolean $activate     Activate the script?
     * @param boolean $auto_update  Only update if auto_update is active?
     */
    public function activate($activate = true, $auto_update = true)
    {
        // Null driver does nothing - rules are not deployed
        // Succeed silently to avoid breaking normal workflow
    }
}
