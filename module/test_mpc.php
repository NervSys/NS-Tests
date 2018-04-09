<?php

/**
 * MPC Tests
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
use tests\start;

class test_mpc extends start
{
    public static $tz = [
        'mpc_process' => ['value', 'sleep']
    ];

    /**
     * MPC test
     */
    public static function go(): void
    {
        echo 'MPC Tests:';
        echo PHP_EOL;
        echo '========================================';
        echo PHP_EOL;
        echo 'Make sure to configure [CLI] section in "/core/conf.ini" if needed!';
        echo PHP_EOL;
        echo '========================================';
        echo PHP_EOL;

        //Build correct child command
        $cmd = strtr(__CLASS__, '\\', '/') . '-mpc_process';

        //Build rand data string
        $string = hash('sha256', uniqid(mt_rand(), true));

        //========== Non-sleep jobs ==========

        //Test 1 job
        $time = microtime(true);

        mpc::begin();
        mpc::add($cmd, ['value' => $string, 'sleep' => 0]);
        $result = mpc::commit();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;
        self::chk_eq('MPC (1 job)', [$result[0]['data'], $string]);
        echo PHP_EOL;


        //Test 10 jobs
        $time = microtime(true);

        mpc::begin();

        $data = [];
        for ($i = 0; $i < 10; ++$i) {
            $data[$i] = $string . '-' . $i;
            mpc::add($cmd, ['value' => $data[$i], 'sleep' => 0]);
        }

        $result = mpc::commit();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;

        //Check data
        $pass = true;
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) || $result[$key]['data'] !== $value) {
                $pass = false;
                break;
            }
        }

        self::chk_eq('MPC (10 jobs)', [$pass, true]);
        echo PHP_EOL;


        //Test 20 jobs
        $time = microtime(true);

        mpc::begin();

        $data = [];
        for ($i = 0; $i < 20; ++$i) {
            $data[$i] = $string . '-' . $i;
            mpc::add($cmd, ['value' => $data[$i], 'sleep' => 0]);
        }

        $result = mpc::commit();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;

        //Check data
        $pass = true;
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) || $result[$key]['data'] !== $value) {
                $pass = false;
                break;
            }
        }

        self::chk_eq('MPC (20 jobs)', [$pass, true]);
        echo PHP_EOL;


        //Test 50 jobs
        $time = microtime(true);

        mpc::begin();

        $data = [];
        for ($i = 0; $i < 50; ++$i) {
            $data[$i] = $string . '-' . $i;
            mpc::add($cmd, ['value' => $data[$i], 'sleep' => 0]);
        }

        $result = mpc::commit();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;

        //Check data
        $pass = true;
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) || $result[$key]['data'] !== $value) {
                $pass = false;
                break;
            }
        }

        self::chk_eq('MPC (50 jobs)', [$pass, true]);
        echo PHP_EOL;


        //Test 100 jobs
        $time = microtime(true);

        mpc::begin();

        $data = [];
        for ($i = 0; $i < 100; ++$i) {
            $data[$i] = $string . '-' . $i;
            mpc::add($cmd, ['value' => $data[$i], 'sleep' => 0]);
        }

        $result = mpc::commit();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;

        //Check data
        $pass = true;
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) || $result[$key]['data'] !== $value) {
                $pass = false;
                break;
            }
        }

        self::chk_eq('MPC (100 jobs)', [$pass, true]);
        echo PHP_EOL;


        //========== Sleep jobs ==========

        //Test 1 job (1s sleep)
        $time = microtime(true);

        mpc::begin();
        mpc::add($cmd, ['value' => $string, 'sleep' => 1]);
        $result = mpc::commit();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;
        self::chk_eq('MPC (1 job, 1s * 1 = 1s)', [$result[0]['data'], $string]);
        echo PHP_EOL;


        //Test 10 jobs (1s sleep for each job)
        $time = microtime(true);

        mpc::begin();

        $data = [];
        for ($i = 0; $i < 10; ++$i) {
            $data[$i] = $string . '-' . $i;
            mpc::add($cmd, ['value' => $data[$i], 'sleep' => 1]);
        }

        $result = mpc::commit();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;

        //Check data
        $pass = true;
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) || $result[$key]['data'] !== $value) {
                $pass = false;
                break;
            }
        }

        self::chk_eq('MPC (10 jobs, 1s * 10 = 10s)', [$pass, true]);
        echo PHP_EOL;


        //Test 20 jobs (1s sleep for each job)
        $time = microtime(true);

        mpc::begin();

        $data = [];
        for ($i = 0; $i < 20; ++$i) {
            $data[$i] = $string . '-' . $i;
            mpc::add($cmd, ['value' => $data[$i], 'sleep' => 0]);
        }

        $result = mpc::commit();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;

        //Check data
        $pass = true;
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) || $result[$key]['data'] !== $value) {
                $pass = false;
                break;
            }
        }

        self::chk_eq('MPC (20 jobs, 1s * 20 = 20s)', [$pass, true]);
        echo PHP_EOL;


        //Test 50 jobs (1s sleep for each job)
        $time = microtime(true);

        mpc::begin();

        $data = [];
        for ($i = 0; $i < 50; ++$i) {
            $data[$i] = $string . '-' . $i;
            mpc::add($cmd, ['value' => $data[$i], 'sleep' => 0]);
        }

        $result = mpc::commit();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;

        //Check data
        $pass = true;
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) || $result[$key]['data'] !== $value) {
                $pass = false;
                break;
            }
        }

        self::chk_eq('MPC (50 jobs, 1s * 50 = 50s)', [$pass, true]);
        echo PHP_EOL;


        //Test 100 jobs (1s sleep for each job)
        $time = microtime(true);

        mpc::begin();

        $data = [];
        for ($i = 0; $i < 100; ++$i) {
            $data[$i] = $string . '-' . $i;
            mpc::add($cmd, ['value' => $data[$i], 'sleep' => 0]);
        }

        $result = mpc::commit();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;

        //Check data
        $pass = true;
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) || $result[$key]['data'] !== $value) {
                $pass = false;
                break;
            }
        }

        self::chk_eq('MPC (100 jobs, 1s * 100 = 100s)', [$pass, true]);
    }

    /**
     * MPC callable function for API
     *
     * @param string $value
     * @param int    $sleep
     *
     * @return string
     */
    public static function mpc_process(string $value, int $sleep = 0): string
    {
        if (0 < $sleep) sleep($sleep);

        return $value;
    }
}