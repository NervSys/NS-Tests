<?php

/**
 * Cache Tests
 *
 * Copyright 2018 秋水之冰 <27206617@qq.com>
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

namespace tests\module;

use tests\start;
use ext\redis_cache;

class test_cache extends start
{
    /**
     * Redis Cache test
     */
    public static function go(): void
    {
        echo 'Redis Cache Test Starts:';
        echo PHP_EOL;
        echo 'Make sure to start Redis first!';
        echo PHP_EOL;
        echo PHP_EOL;

        $data = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand()
        ];


        redis_cache::del();

        $set = redis_cache::set($data);
        self::chk_eq('Cache Set', [$set, true]);


        $get = redis_cache::get();
        self::chk_eq('Cache Get', [json_encode($get), json_encode($data)]);


        redis_cache::$name = 'cache:test_key';

        redis_cache::del();

        $set = redis_cache::set($data);
        self::chk_eq('Cache Set (with key)', [$set, true]);


        $get = redis_cache::get();
        self::chk_eq('Cache Get (with key)', [json_encode($get), json_encode($data)]);


        redis_cache::del();

        $set = redis_cache::set($data);
        self::chk_eq('Cache Set', [$set, true]);

        redis_cache::del();

        $get = redis_cache::get();
        self::chk_eq('Cache Get-Del', [json_encode($get), json_encode([])]);


        redis_cache::del();

        $set = redis_cache::set($data);
        self::chk_eq('Cache Set (with key)', [$set, true]);

        redis_cache::del();

        $get = redis_cache::get();
        self::chk_eq('Cache Get-Del (with key)', [json_encode($get), json_encode([])]);


        redis_cache::$life = 0;
        redis_cache::$name = null;

        redis_cache::del();

        $set = redis_cache::set($data);
        self::chk_eq('Cache Set (persistent)', [$set, true]);

        $get = redis_cache::get();
        self::chk_eq('Cache Get (persistent)', [json_encode($get), json_encode($data)]);

        redis_cache::del();

        $get = redis_cache::get();
        self::chk_eq('Cache Get-Del (persistent)', [json_encode($get), json_encode([])]);
    }
}