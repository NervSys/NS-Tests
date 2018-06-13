<?php

/**
 * Queue Tests
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

use ext\mpc;
use ext\redis;
use tests\start;
use ext\redis_queue;

class test_queue extends start
{
    public static $tz = [
        'queue_start'   => [],
        'queue_process' => ['value']
    ];

    /**
     * Queue tests
     */
    public static function go(): void
    {
        echo 'Queue Tests:';
        echo PHP_EOL;
        echo '========================================';
        echo PHP_EOL;
        echo 'Start queue process, please wait...';
        echo PHP_EOL;
        echo '========================================';
        echo PHP_EOL;

        //Call MPC to run queue process
        mpc::$wait = false;

        mpc::begin();
        mpc::add(strtr(__CLASS__, '\\', '/') . '-queue_start');
        mpc::commit();


        //Build queue process function
        $cmd = strtr(__CLASS__, '\\', '/') . '-queue_process';


        //Test "queue rand add"
        $add = redis_queue::add('test_' . mt_rand(1, 100), $cmd, ['cmd' => &$cmd, 'value' => true]);
        self::chk_eq('Queue Add (1 job)', [$add, 1]);

        //Sleep for idle time
        sleep(redis_queue::$idle_wait);

        //Check queue jobs
        $jobs = redis_queue::show_queue();
        self::chk_eq('Queue Job Done (1 job)', [array_sum($jobs), 0]);


        //Get fail count
        $fail_rec = redis_queue::show_fail(0, 1)['len'];

        //Add fail queue
        redis_queue::add('test_' . mt_rand(1, 100), $cmd, ['cmd' => &$cmd, 'value' => false]);

        //Sleep for idle time
        sleep(redis_queue::$idle_wait);

        //Check fail count now
        $fail_now = redis_queue::show_fail(0, 1)['len'];
        self::chk_eq('Queue Job Fail (1 fail)', [$fail_now - $fail_rec, 1]);


        //Test 1000 rand jobs
        $left = $jobs = 1000;
        for ($i = 0; $i < $jobs; ++$i) redis_queue::add('test_' . mt_rand(1, 100), $cmd, ['cmd' => &$cmd, 'value' => true, 'data' => hash('sha256', uniqid(mt_rand(), true))]);

        do {
            //Wait for process
            sleep(redis_queue::$idle_wait);

            //Copy left jobs
            if ($jobs < $left) {
                $left = $jobs;
            }

            //Read queue list
            $queue = redis_queue::show_queue();

            //Count jobs
            $jobs = 0;
            foreach ($queue as $key => $value) {
                $jobs += redis::connect()->lLen($key);
            }
        } while (0 < $jobs && $left > $jobs);

        self::chk_eq('Queue Process (1000 jobs)', [$jobs, 0]);


        //Test 10000 rand jobs
        $left = $jobs = 10000;
        for ($i = 0; $i < $jobs; ++$i) redis_queue::add('test_' . mt_rand(1, 100), $cmd, ['cmd' => &$cmd, 'value' => true, 'data' => hash('sha256', uniqid(mt_rand(), true))]);

        do {
            //Wait for process
            sleep(redis_queue::$idle_wait);

            //Copy left jobs
            if ($jobs < $left) {
                $left = $jobs;
            }

            //Read queue list
            $queue = redis_queue::show_queue();

            //Count jobs
            $jobs = 0;
            foreach ($queue as $key => $value) {
                $jobs += redis::connect()->lLen($key);
            }
        } while (0 < $jobs && $left > $jobs);

        self::chk_eq('Queue Process (10000 jobs)', [$jobs, 0]);


        //Stop queue process
        redis_queue::stop();
    }

    /**
     * Root queue process
     *
     * @throws \Exception
     */
    public static function queue_start(): void
    {
        //Stop queue process
        redis_queue::stop();

        //Start root process
        redis_queue::start();
    }

    /**
     * Queue data process
     *
     * @param bool $value
     *
     * @return bool
     * @throws \Exception
     */
    public static function queue_process(bool $value): bool
    {
        return $value;
    }
}