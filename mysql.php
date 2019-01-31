<?php

/**
 * Nervsys test suites
 *
 * Copyright 2016-2019 秋水之冰 <27206617@qq.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace tests;

use ext\pdo_mysql;
use tests\lib\base;

class mysql extends base
{
    public static $tz = [
        'insert' => '',
        'incr'   => 'test_id',
        'update' => 'test_id',
        'select' => 'test_id',
        'check'  => 'test_changes,test_data',
        'delete' => 'test_id'
    ];

    private $mysql = null;

    /**
     * mpc constructor.
     */
    public function __construct()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS  `ns_test` (
              `test_id` CHAR(16) NOT NULL COMMENT "ID",
              `test_hash` CHAR(32) NOT NULL COMMENT "HASH",
              `test_text` VARCHAR(512) NOT NULL COMMENT "Content",
              `test_time` INT(10) UNSIGNED NOT NULL COMMENT "Time",
              `test_count` INT(10) UNSIGNED NOT NULL COMMENT "Count",
              PRIMARY KEY (`test_id`),
              INDEX (`test_hash`),
              INDEX (`test_time`),
              INDEX (`test_count`)
            ) ENGINE=MYISAM DEFAULT CHARSET=utf8mb4 COMMENT "Test Table";';

        $this->mysql = pdo_mysql::new()->config(['db' => 'test']);
        $this->mysql->exec($sql);
    }

    /**
     * Insert tests
     */
    public function insert(): void
    {
        $test_id = [];

        for ($i = 0; $i < 10; ++$i) {
            $text      = hash('sha256', uniqid(mt_rand()));
            $test_id[] = $id = substr(hash('md5', uniqid(mt_rand())), 0, 16);

            $data = [
                'test_id'    => $id,
                'test_hash'  => hash('md5', $text),
                'test_text'  => $text,
                'test_time'  => time(),
                'test_count' => $i,
            ];

            $res = $this->mysql->insert('ns_test')->value($data)->execute();
            parent::chk_eq('insert: ' . $i, [$res, true]);
        }

        parent::$data['test_id'] = &$test_id;
    }

    /**
     * Update tests
     *
     * @param array $test_id
     */
    public function incr(array $test_id): void
    {
        $i = 0;

        foreach ($test_id as $id) {
            $data  = ['test_count' => mt_rand()];
            $where = [['test_id', $id]];

            $res = $this->mysql->update('ns_test')->incr($data)->where($where)->execute();
            parent::chk_eq('update: ' . $i, [$res, true]);

            ++$i;
        }
    }

    /**
     * Update tests
     *
     * @param array $test_id
     */
    public function update(array $test_id): void
    {
        $i       = 0;
        $changes = [];

        foreach ($test_id as $id) {
            $count = mt_rand();
            $text  = hash('sha256', uniqid(mt_rand()));
            $hash  = hash('md5', $text);

            $changes[$id] = [
                'test_hash'  => $hash,
                'test_text'  => $text,
                'test_count' => $count
            ];

            $data = [
                'test_hash'  => $hash,
                'test_text'  => $text,
                'test_count' => $count,
            ];

            $where = [['test_id', $id]];

            $res = $this->mysql->update('ns_test')->value($data)->where($where)->execute();
            parent::chk_eq('update: ' . $i, [$res, true]);

            ++$i;
        }

        parent::$data['test_changes'] = &$changes;
    }

    /**
     * Select tests
     *
     * @param array $test_id
     */
    public function select(array $test_id): void
    {
        parent::$data['test_data'] = $res = $this->mysql
            ->select('ns_test')
            ->field('test_id', 'test_hash', 'test_text', 'test_count')
            ->where([['test_id', 'IN', $test_id]])
            ->order(['test_time' => 'ASC'])
            ->fetch();

        parent::chk_eq('select', [count($res), count($test_id)]);
    }

    /**
     * Data Check tests
     *
     * @param array $test_changes
     * @param array $test_data
     */
    public function check(array $test_changes, array $test_data): void
    {
        $i = 0;

        foreach ($test_data as $data) {
            $id = $data['test_id'];
            unset($data['test_id']);

            if (!isset($test_changes[$id])) {
                parent::chk_eq('check: ' . $i, [$data, null]);
            }

            parent::chk_eq('check: ' . $i, [$data, $test_changes[$id]]);

            ++$i;
        }
    }

    /**
     * Delete tests
     *
     * @param array $test_id
     */
    public function delete(array $test_id): void
    {
        $res = $this->mysql
            ->delete('ns_test')
            ->where([['test_id', 'IN', $test_id]])
            ->execute();

        parent::chk_eq('delete', [$res, true]);
    }

    /**
     * Drop test table
     */
    public function __destruct()
    {
        $this->mysql->exec('DROP TABLE IF EXISTS  `ns_test`;');
    }
}