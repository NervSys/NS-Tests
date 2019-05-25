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

namespace app\tests;

use ext\redis_cache;
use app\tests\lib\res;

class cache extends redis_cache
{
    public static $tz = [
        'test_set'             => '',
        'test_get'             => '',
        'test_del_get'         => '',
        'test_set_persist'     => '',
        'test_get_persist'     => '',
        'test_del_get_persist' => '',
        'test_set_life'        => '',
        'test_get_life'        => '',
        'test_get_life_over'   => ''
    ];

    private $redis_cache;

    private $cache_data = [];
    private $cache_key  = 'test';

    /**
     * cache constructor.
     */
    public function __construct()
    {
        $this->cache_data = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand(),
            hash('sha256', uniqid(mt_rand(), true)),
            hash('sha256', uniqid(mt_rand(), true)),
            hash('sha256', uniqid(mt_rand(), true)),
            hash('sha256', uniqid(mt_rand(), true)),
            hash('sha256', uniqid(mt_rand(), true))
        ];

        $this->redis_cache = parent::connect();
        $this->redis_cache->del($this->cache_key);
    }

    /**
     * Test set
     */
    public function test_set(): void
    {
        $set = $this->redis_cache->set($this->cache_key, $this->cache_data, 600);
        res::chk_eq('set', [$set, true]);
    }

    /**
     * Test get
     */
    public function test_get(): void
    {
        $get = $this->redis_cache->get($this->cache_key);
        res::chk_eq('get', [$get, $this->cache_data]);
    }

    /**
     * Test del_get
     */
    public function test_del_get(): void
    {
        $this->redis_cache->del($this->cache_key);

        $get = $this->redis_cache->get($this->cache_key);
        res::chk_eq('del_get', [$get, []]);
    }

    /**
     * Test set
     */
    public function test_set_persist(): void
    {
        $set = $this->redis_cache->set($this->cache_key, $this->cache_data, 0);
        res::chk_eq('set_persist', [$set, true]);
    }

    /**
     * Test get
     */
    public function test_get_persist(): void
    {
        $get = $this->redis_cache->get($this->cache_key);
        res::chk_eq('get_persist', [$get, $this->cache_data]);
    }

    /**
     * Test del_get
     */
    public function test_del_get_persist(): void
    {
        $this->redis_cache->del($this->cache_key);

        $get = $this->redis_cache->get($this->cache_key);
        res::chk_eq('del_get_persist', [$get, []]);
    }

    /**
     * Test set
     */
    public function test_set_life(): void
    {
        $set = $this->redis_cache->set($this->cache_key, $this->cache_data, 3);
        res::chk_eq('set_life', [$set, true]);
    }

    /**
     * Test get
     */
    public function test_get_life(): void
    {
        $get = $this->redis_cache->get($this->cache_key);
        res::chk_eq('get_life', [$get, $this->cache_data]);
    }

    /**
     * Test del_get
     */
    public function test_get_life_over(): void
    {
        sleep(3);

        $get = $this->redis_cache->get($this->cache_key);
        res::chk_eq('get_life_over', [$get, []]);
    }
}