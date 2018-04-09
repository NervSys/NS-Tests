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

use ext\redis;
use tests\start;
use ext\redis_cache;

class test_cache extends start
{
    /**
     * Redis Cache tests
     */
    public static function go(): void
    {
        echo 'Redis Cache Tests:';
        echo PHP_EOL;
        echo '========================================';
        echo PHP_EOL;

        //Test Redis
        try {
            redis::connect();
        } catch (\Throwable $exception) {
            echo 'Redis Connect Failed!';
            return;
        }

        //Build rand data
        $data = [
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

        //========== NO cache name ==========

        //Set cache life
        redis_cache::$life = 600;

        //Clean cache name
        redis_cache::$name = null;

        //Delete cache
        redis_cache::del();

        //Test "set"
        $set = redis_cache::set($data);
        self::chk_eq('Cache Set', [$set, true]);

        //Test "get"
        $get = redis_cache::get();
        self::chk_eq('Cache Get', [json_encode($get), json_encode($data)]);

        //Delete cache
        redis_cache::del();

        //Test "get after delete"
        $get = redis_cache::get();
        self::chk_eq('Cache Del->Get', [json_encode($get), json_encode([])]);


        //Set cache life
        redis_cache::$life = 0;

        //Clean cache name
        redis_cache::$name = null;

        //Delete cache
        redis_cache::del();

        //Test "set persistent cache"
        $set = redis_cache::set($data);
        self::chk_eq('Cache Set (persistent)', [$set, true]);

        //Test "get persistent cache"
        $get = redis_cache::get();
        self::chk_eq('Cache Get (persistent)', [json_encode($get), json_encode($data)]);

        //Delete cache
        redis_cache::del();

        //Test "get after delete on persistent cache"
        $get = redis_cache::get();
        self::chk_eq('Cache Del->Get (persistent)', [json_encode($get), json_encode([])]);


        //Set cache life
        redis_cache::$life = 1;

        //Clean cache name
        redis_cache::$name = null;

        //Delete cache
        redis_cache::del();

        //Test "set cache for 1s"
        $set = redis_cache::set($data);
        self::chk_eq('Cache Set (life = 1s)', [$set, true]);

        //Sleep 2s
        sleep(2);

        //Test "get outdated cache"
        $get = redis_cache::get();
        self::chk_eq('Cache Get (outdated)', [json_encode($get), json_encode([])]);


        //========== With cache name ==========

        //Set cache life
        redis_cache::$life = 600;

        //Set cache name
        redis_cache::$name = 'cache:test';

        //Delete cache with name
        redis_cache::del();

        //Test "set with name"
        $set = redis_cache::set($data);
        self::chk_eq('Cache Set (with name)', [$set, true]);

        //Test "get with name"
        $get = redis_cache::get();
        self::chk_eq('Cache Get (with name)', [json_encode($get), json_encode($data)]);

        //Delete cache with name
        redis_cache::del();

        //Test "get after delete with name"
        $get = redis_cache::get();
        self::chk_eq('Cache Del->Get (with name)', [json_encode($get), json_encode([])]);


        //Set cache life to persistent
        redis_cache::$life = 0;

        //Set cache name
        redis_cache::$name = 'cache:test';

        //Delete cache
        redis_cache::del();

        //Test "set persistent cache"
        $set = redis_cache::set($data);
        self::chk_eq('Cache Set (with name, persistent)', [$set, true]);

        //Test "get persistent cache"
        $get = redis_cache::get();
        self::chk_eq('Cache Get (with name, persistent)', [json_encode($get), json_encode($data)]);

        //Delete cache
        redis_cache::del();

        //Test "get after delete on persistent cache"
        $get = redis_cache::get();
        self::chk_eq('Cache Del->Get (with name, persistent)', [json_encode($get), json_encode([])]);


        //Set cache life
        redis_cache::$life = 1;

        //Set cache name
        redis_cache::$name = 'cache:test';

        //Delete cache
        redis_cache::del();

        //Test "set cache for 1s"
        $set = redis_cache::set($data);
        self::chk_eq('Cache Set (with name, life = 1s)', [$set, true]);

        //Sleep 2s
        sleep(2);

        //Test "get outdated cache"
        $get = redis_cache::get();
        self::chk_eq('Cache Get (with name, outdated)', [json_encode($get), json_encode([])]);
    }
}