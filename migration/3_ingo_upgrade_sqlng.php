<?php

/**
 * Adds tables for the Sqlng share driver.
 *
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Ingo
 */
class IngoUpgradeSqlng extends Horde_Db_Migration_Base
{
    /**
     * Upgrade.
     */
    public function up()
    {
        $t = $this->createTable('ingo_sharesng', ['autoincrementKey' => 'share_id']);
        $t->column('share_name', 'string', ['limit' => 255, 'null' => false]);
        $t->column('share_owner', 'string', ['limit' => 255]);
        $t->column('share_flags', 'integer', ['default' => 0, 'null' => false]);
        $t->column('perm_creator_' . Horde_Perms::SHOW, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_creator_' . Horde_Perms::READ, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_creator_' . Horde_Perms::EDIT, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_creator_' . Horde_Perms::DELETE, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_default_' . Horde_Perms::SHOW, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_default_' . Horde_Perms::READ, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_default_' . Horde_Perms::EDIT, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_default_' . Horde_Perms::DELETE, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_guest_' . Horde_Perms::SHOW, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_guest_' . Horde_Perms::READ, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_guest_' . Horde_Perms::EDIT, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_guest_' . Horde_Perms::DELETE, 'boolean', ['default' => false, 'null' => false]);
        $t->column('attribute_name', 'string', ['limit' => 255, 'null' => false]);
        $t->column('attribute_desc', 'string', ['limit' => 255]);
        $t->end();

        $this->addIndex('ingo_sharesng', ['share_name']);
        $this->addIndex('ingo_sharesng', ['share_owner']);
        $this->addIndex('ingo_sharesng', ['perm_creator_' . Horde_Perms::SHOW]);
        $this->addIndex('ingo_sharesng', ['perm_creator_' . Horde_Perms::READ]);
        $this->addIndex('ingo_sharesng', ['perm_creator_' . Horde_Perms::EDIT]);
        $this->addIndex('ingo_sharesng', ['perm_creator_' . Horde_Perms::DELETE]);
        $this->addIndex('ingo_sharesng', ['perm_default_' . Horde_Perms::SHOW]);
        $this->addIndex('ingo_sharesng', ['perm_default_' . Horde_Perms::READ]);
        $this->addIndex('ingo_sharesng', ['perm_default_' . Horde_Perms::EDIT]);
        $this->addIndex('ingo_sharesng', ['perm_default_' . Horde_Perms::DELETE]);
        $this->addIndex('ingo_sharesng', ['perm_guest_' . Horde_Perms::SHOW]);
        $this->addIndex('ingo_sharesng', ['perm_guest_' . Horde_Perms::READ]);
        $this->addIndex('ingo_sharesng', ['perm_guest_' . Horde_Perms::EDIT]);
        $this->addIndex('ingo_sharesng', ['perm_guest_' . Horde_Perms::DELETE]);

        $t = $this->createTable('ingo_sharesng_groups', ['autoincrementKey' => false]);
        $t->column('share_id', 'integer', ['null' => false]);
        $t->column('group_uid', 'string', ['limit' => 255, 'null' => false]);
        $t->column('perm_' . Horde_Perms::SHOW, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_' . Horde_Perms::READ, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_' . Horde_Perms::EDIT, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_' . Horde_Perms::DELETE, 'boolean', ['default' => false, 'null' => false]);
        $t->end();

        $this->addIndex('ingo_sharesng_groups', ['share_id']);
        $this->addIndex('ingo_sharesng_groups', ['group_uid']);
        $this->addIndex('ingo_sharesng_groups', ['perm_' . Horde_Perms::SHOW]);
        $this->addIndex('ingo_sharesng_groups', ['perm_' . Horde_Perms::READ]);
        $this->addIndex('ingo_sharesng_groups', ['perm_' . Horde_Perms::EDIT]);
        $this->addIndex('ingo_sharesng_groups', ['perm_' . Horde_Perms::DELETE]);

        $t = $this->createTable('ingo_sharesng_users', ['autoincrementKey' => false]);
        $t->column('share_id', 'integer', ['null' => false]);
        $t->column('user_uid', 'string', ['limit' => 255, 'null' => false]);
        $t->column('perm_' . Horde_Perms::SHOW, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_' . Horde_Perms::READ, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_' . Horde_Perms::EDIT, 'boolean', ['default' => false, 'null' => false]);
        $t->column('perm_' . Horde_Perms::DELETE, 'boolean', ['default' => false, 'null' => false]);
        $t->end();

        $this->addIndex('ingo_sharesng_users', ['share_id']);
        $this->addIndex('ingo_sharesng_users', ['user_uid']);
        $this->addIndex('ingo_sharesng_users', ['perm_' . Horde_Perms::SHOW]);
        $this->addIndex('ingo_sharesng_users', ['perm_' . Horde_Perms::READ]);
        $this->addIndex('ingo_sharesng_users', ['perm_' . Horde_Perms::EDIT]);
        $this->addIndex('ingo_sharesng_users', ['perm_' . Horde_Perms::DELETE]);

        $this->dataUp();
    }

    /**
     * Downgrade
     */
    public function down()
    {
        $this->dropTable('ingo_sharesng');
        $this->dropTable('ingo_sharesng_groups');
        $this->dropTable('ingo_sharesng_users');
    }

    public function dataUp()
    {
        $whos = ['creator', 'default', 'guest'];
        $perms = [Horde_Perms::SHOW,
            Horde_Perms::READ,
            Horde_Perms::EDIT,
            Horde_Perms::DELETE];

        $sql = 'INSERT INTO ingo_sharesng (share_id, share_name, share_owner, share_flags, attribute_name, attribute_desc';
        $count = 0;
        foreach ($whos as $who) {
            foreach ($perms as $perm) {
                $sql .= ', perm_' . $who . '_' . $perm;
                $count++;
            }
        }
        $sql .= ') VALUES (?, ?, ?, ?, ?, ?' . str_repeat(', ?', $count) . ')';

        foreach ($this->select('SELECT * FROM ingo_shares') as $share) {
            $values = [$share['share_id'],
                $share['share_name'],
                $share['share_owner'],
                $share['share_flags'],
                $share['attribute_name'],
                $share['attribute_desc']];
            foreach ($whos as $who) {
                foreach ($perms as $perm) {
                    $values[] = (bool) ($share['perm_' . $who] & $perm);
                }
            }
            $this->insert($sql, $values, null, 'share_id', $share['share_id']);
        }

        foreach (['user', 'group'] as $what) {
            $sql = 'INSERT INTO ingo_sharesng_' . $what . 's (share_id, ' . $what . '_uid';
            $count = 0;
            foreach ($perms as $perm) {
                $sql .= ', perm_' . $perm;
                $count++;
            }
            $sql .= ') VALUES (?, ?' . str_repeat(', ?', $count) . ')';

            foreach ($this->select('SELECT * FROM ingo_shares_' . $what . 's') as $share) {
                $values = [$share['share_id'],
                    $share[$what . '_uid']];
                foreach ($perms as $perm) {
                    $values[] = (bool) ($share['perm'] & $perm);
                }
                $this->insert($sql, $values);
            }
        }
    }
}
