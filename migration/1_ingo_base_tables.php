<?php

/**
 * Create Ingo base tables.
 *
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author   Michael J. Rubinsky <mrubinsk@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Ingo
 */
class IngoBaseTables extends Horde_Db_Migration_Base
{
    /**
     * Upgrade.
     */
    public function up()
    {
        $tableList = $this->tables();

        if (!in_array('ingo_rules', $tableList)) {
            $t = $this->createTable('ingo_rules', ['autoincrementKey' => false]);
            $t->column('rule_id', 'integer', ['null' => false]);
            $t->column('rule_owner', 'string', ['limit' => 255, 'null' => false]);
            $t->column('rule_name', 'string', ['limit' => 255, 'null' => false]);
            $t->column('rule_action', 'integer', ['null' => false]);
            $t->column('rule_value', 'string', ['limit' => 255]);
            $t->column('rule_flags', 'integer');
            $t->column('rule_conditions', 'text');
            $t->column('rule_combine', 'integer');
            $t->column('rule_stop', 'integer');
            $t->column('rule_active', 'integer', ['default' => 1, 'null' => false]);
            $t->column('rule_order', 'integer', ['default' => 0, 'null' => false]);
            $t->primaryKey(['rule_id']);
            $t->end();
            $this->addIndex('ingo_rules', ['rule_owner']);
        }

        if (!in_array('ingo_lists', $tableList)) {
            $t = $this->createTable('ingo_lists', ['autoincrementKey' => false]);
            $t->column('list_owner', 'string', ['limit' => 255, 'null' => false]);
            $t->column('list_blacklist', 'integer', ['default' => 0]);
            $t->column('list_address', 'string', ['limit' => 255, 'null' => false]);
            $t->end();
            $this->addIndex('ingo_lists', ['list_owner', 'list_blacklist']);
        }

        if (!in_array('ingo_forwards', $tableList)) {
            $t = $this->createTable('ingo_forwards', ['autoincrementKey' => false]);
            $t->column('forward_owner', 'string', ['limit' => 255, 'null' => false]);
            $t->column('forward_addresses', 'text');
            $t->column('forward_keep', 'integer', ['default' => 0, 'null' => false]);
            $t->end();
        }

        if (!in_array('ingo_vacations', $tableList)) {
            $t = $this->createTable('ingo_vacations', ['autoincrementKey' => false]);
            $t->column('vacation_owner', 'string', ['limit' => 255, 'null' => false]);
            $t->column('vacation_addresses', 'text');
            $t->column('vacation_subject', 'string', ['limit' => 255]);
            $t->column('vacation_reason', 'text');
            $t->column('vacation_days', 'integer', ['default' => 7]);
            $t->column('vacation_start', 'integer');
            $t->column('vacation_end', 'integer');
            $t->column('vacation_excludes', 'text');
            $t->column('vacation_ignorelists', 'integer', ['default' => 1]);
            $t->primaryKey(['vacation_owner']);
            $t->end();
        }

        if (!in_array('ingo_spam', $tableList)) {
            $t = $this->createTable('ingo_spam', ['autoincrementKey' => false]);
            $t->column('spam_owner', 'string', ['limit' => 255, 'null' => false]);
            $t->column('spam_level', 'integer', ['default' => 5]);
            $t->column('spam_folder', 'string', ['limit' => 255]);
            $t->primaryKey(['spam_owner']);
            $t->end();
        }
        if (!in_array('ingo_shares', $tableList)) {
            $t = $this->createTable('ingo_shares', ['autoincrementKey' => false]);
            $t->column('share_id', 'integer', ['null' => false]);
            $t->column('share_name', 'string', ['limit' => 255, 'null' => false]);
            $t->column('share_owner', 'string', ['limit' => 255, 'null' => false]);
            $t->column('share_flags', 'integer', ['default' => 0, 'null' => false]);
            $t->column('perm_creator', 'integer', ['default' => 0, 'null' => false]);
            $t->column('perm_default', 'integer', ['default' => 0, 'null' => false]);
            $t->column('perm_guest', 'integer', ['default' => 0, 'null' => false]);
            $t->column('attribute_name', 'string', ['limit' => 255, 'null' => false]);
            $t->column('attribute_desc', 'string', ['limit' => 255]);
            $t->primaryKey(['share_id']);
            $t->end();

            $this->addIndex('ingo_shares', ['share_name']);
            $this->addIndex('ingo_shares', ['share_owner']);
            $this->addIndex('ingo_shares', ['perm_creator']);
            $this->addIndex('ingo_shares', ['perm_default']);
            $this->addIndex('ingo_shares', ['perm_guest']);
        }

        if (!in_array('ingo_shares_groups', $tableList)) {
            $t = $this->createTable('ingo_shares_groups');
            $t->column('share_id', 'integer', ['null' => false]);
            $t->column('group_uid', 'string', ['limit' => 255, 'null' => false]);
            $t->column('perm', 'integer', ['null' => false]);
            $t->end();

            $this->addIndex('ingo_shares_groups', ['share_id']);
            $this->addIndex('ingo_shares_groups', ['group_uid']);
            $this->addIndex('ingo_shares_groups', 'perm');
        }

        if (!in_array('ingo_shares_users', $tableList)) {
            $t = $this->createTable('ingo_shares_users');
            $t->column('share_id', 'integer', ['null' => false]);
            $t->column('user_uid', 'string', ['limit' => 255, 'null' => false]);
            $t->column('perm', 'integer', ['null' => false]);
            $t->end();

            $this->addIndex('ingo_shares_users', ['share_id']);
            $this->addIndex('ingo_shares_users', ['user_uid']);
            $this->addIndex('ingo_shares_users', ['perm']);
        }

    }

    /**
     * Downgrade
     *
     */
    public function down()
    {
        $this->dropTable('ingo_rules');
        $this->dropTable('ingo_lists');
        $this->dropTable('ingo_forwards');
        $this->dropTable('ingo_vacations');
        $this->dropTable('ingo_spam');
        $this->dropTable('ingo_shares');
        $this->dropTable('ingo_shares_groups');
        $this->dropTable('ingo_shares_users');
    }

}
