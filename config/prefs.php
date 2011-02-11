<?php
/**
 * See horde/config/prefs.php for documentation on the structure of this file.
 *
 * IMPORTANT: Local overrides should be placed in prefs.local.php, or
 * prefs-servername.php if the 'vhosts' setting has been enabled in Horde's
 * configuration.
 */

// This preference group will only be displayed if the configured
// Ingo_Script:: driver can create script files.
$prefGroups['script'] = array(
    'column' => _("Other Preferences"),
    'label' => _("Script Updating"),
    'desc' => _("Preferences about script updating."),
    'members' => array('auto_update'));

// The following preferences are only used for Horde_Script:: drivers that use
// scripts.

// Automatically update the script?
$_prefs['auto_update'] = array(
    'value' => 1,
    'locked' => false,
    'type' => 'checkbox',
    'desc' => _("Automatically activate the script after each change?")
);

// End script preferences

// The following preferences are only used for Horde_Script:: drivers that can
// do on-demand filtering.

// Show detailed filter status messages?
// a value of 0 = no, 1 = yes
$_prefs['show_filter_msg'] = array(
    'value' => 1,
    'locked' => false,
    'type' => 'implicit'
);

// Only filter [un]seen messages?
// Values: 0, Ingo_Script::FILTER_UNSEEN, Ingo_Script::FILTER_SEEN
$_prefs['filter_seen'] = array(
    'value' => 0,
    'locked' => false,
    'type' => 'implicit'
);

// End on-demand filtering preferences

// If NOT using the 'prefs' storage driver (see conf.php), you can comment out
// the below entries.

// Filter rules.
require_once dirname(__FILE__) . '/../lib/Storage.php';
$_prefs['rules'] = array(
    'value' => 'a:5:{i:0;a:2:{s:4:"name";s:9:"Whitelist";s:6:"action";i:' . Ingo_Storage::ACTION_WHITELIST . ';}i:1;a:3:{s:4:"name";s:8:"Vacation";s:6:"action";i:' . Ingo_Storage::ACTION_VACATION . ';s:7:"disable";b:1;}i:2;a:2:{s:4:"name";s:9:"Blacklist";s:6:"action";i:' . Ingo_Storage::ACTION_BLACKLIST . ';}i:3;a:3:{s:4:"name";s:11:"Spam Filter";s:6:"action";i:' . Ingo_Storage::ACTION_SPAM . ';s:7:"disable";b:1;}i:4;a:2:{s:4:"name";s:7:"Forward";s:6:"action";i:' . Ingo_Storage::ACTION_FORWARD . ';}}',
    'locked' => false,
    'type' => 'implicit'
);

// Blacklist.
$_prefs['blacklist'] = array(
    'value' => 'a:2:{s:1:"a";a:0:{}s:1:"f";s:0:"";}',
    // Lock this preference to disable blacklists.
    'locked' => false,
    'type' => 'implicit'
);

// Whitelist.
$_prefs['whitelist'] = array(
    'value' => 'a:0:{}',
    // Lock this preference to disable whitelists.
    'locked' => false,
    'type' => 'implicit'
);

// Vacation notices.
$_prefs['vacation'] = array(
    'value' => 'a:8:{s:9:"addresses";a:0:{}s:4:"days";i:7;s:8:"excludes";a:0:{}s:10:"ignorelist";b:1;s:6:"reason";s:0:"";s:7:"subject";s:0:"";s:5:"start";i:0;s:3:"end";i:0;}',
    // Lock this preference to disable vacation notices.
    'locked' => false,
    'type' => 'implicit'
);

// Forwarding.
$_prefs['forward'] = array(
    'value' => 'a:2:{s:1:"a";a:0:{}s:1:"k";i:0;}',
    // Lock this preference to disable forwarding.
    'locked' => false,
    'type' => 'implicit'
);

// Spam rule.
$_prefs['spam'] = array(
    'value' => 'a:2:{s:6:"folder";N;s:5:"level";i:5;}',
    // Lock this preference to disable the spam rule.
    'locked' => false,
    'type' => 'implicit'
);

/* Local overrides. */
if (file_exists(dirname(__FILE__) . '/prefs.local.php')) {
    include dirname(__FILE__) . '/prefs.local.php';
}