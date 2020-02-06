<?php

/**
 * Nervsys UnitTest
 *
 * Copyright 2016-2020 秋水之冰 <27206617@qq.com>
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

namespace app\UnitTest\Unit;

use app\UnitTest\Lib\res;
use ext\cache as unit_cache;
use ext\conf;
use ext\redis;

class cache
{
    public $test_list = [
        'set',
        'get',
        'del_get',
        'set_persist',
        'get_persist',
        'del_get_persist',
        'set_life',
        'get_life_valid',
        'get_life_invalid',
    ];

    private $cache_data = [];
    private $cache_key  = 'UnitTest';

    private $unit_cache;

    /**
     * cache constructor.
     */
    public function __construct()
    {
        $this->unit_cache = unit_cache::new(redis::create(conf::get('redis'))->connect());

        $this->unit_cache->del($this->cache_key);

        $this->cache_data = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand(),
            hash('md5', uniqid(mt_rand(), true)),
            hash('md5', uniqid(mt_rand(), true)),
            hash('sha1', uniqid(mt_rand(), true)),
            hash('sha256', uniqid(mt_rand(), true)),
            hash('sha256', uniqid(mt_rand(), true))
        ];
    }

    /**
     * Test set
     */
    public function set(): void
    {
        $set = $this->unit_cache->set($this->cache_key, $this->cache_data, 600);
        res::chk(__FUNCTION__, [$set, true]);
    }

    /**
     * Test get
     */
    public function get(): void
    {
        $get = $this->unit_cache->get($this->cache_key);
        res::chk(__FUNCTION__, [$get, $this->cache_data]);
    }

    /**
     * Test del_get
     */
    public function del_get(): void
    {
        $this->unit_cache->del($this->cache_key);

        $get = $this->unit_cache->get($this->cache_key);
        res::chk(__FUNCTION__, [$get, []]);
    }

    /**
     * Test set
     */
    public function set_persist(): void
    {
        $set = $this->unit_cache->set($this->cache_key, $this->cache_data, 0);
        res::chk(__FUNCTION__, [$set, true]);
    }

    /**
     * Test get
     */
    public function get_persist(): void
    {
        $get = $this->unit_cache->get($this->cache_key);
        res::chk(__FUNCTION__, [$get, $this->cache_data]);
    }

    /**
     * Test del_get
     */
    public function del_get_persist(): void
    {
        $this->unit_cache->del($this->cache_key);

        $get = $this->unit_cache->get($this->cache_key);
        res::chk(__FUNCTION__, [$get, []]);
    }

    /**
     * Test set
     */
    public function set_life(): void
    {
        $set = $this->unit_cache->set($this->cache_key, $this->cache_data, 1);
        res::chk(__FUNCTION__, [$set, true]);
    }

    /**
     * Test get
     */
    public function get_life_valid(): void
    {
        $get = $this->unit_cache->get($this->cache_key);
        res::chk(__FUNCTION__, [$get, $this->cache_data]);
    }

    /**
     * Test del_get
     */
    public function get_life_invalid(): void
    {
        sleep(1);

        $get = $this->unit_cache->get($this->cache_key);
        res::chk(__FUNCTION__, [$get, []]);
    }
}