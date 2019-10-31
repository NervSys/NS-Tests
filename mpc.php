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

use app\tests\lib\res;
use ext\mpc as base_mpc;

class mpc extends base_mpc
{
    public $tz = [
        'child',
        'test_job_1',
        'test_job_10',
        'test_job_100',
        'test_job_1s',
        'test_job_10s',
        'test_job_100s',
        'test_job_1000s_100p'
    ];

    private $item  = '';
    private $child = 'tests/mpc-child';

    /**
     * mpc constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->item = hash('sha256', uniqid(mt_rand(), true));
    }

    /**
     * MPC test child process
     *
     * @param string $value
     * @param int    $sleep
     *
     * @return string
     */
    public function child(string $value = '', int $sleep = 0): string
    {
        if (0 < $sleep) {
            sleep($sleep);
        }

        return $value;
    }

    /**
     * Test 1 job
     */
    public function test_job_1(): void
    {
        $time = microtime(true);

        $result = $this->add(['c' => $this->child, 'd' => ['value' => $this->item]])->go();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;
        res::chk_eq('1 job', [$result[0]['data'], $this->item]);
        echo PHP_EOL;
    }

    /**
     * Test 10 jobs
     */
    public function test_job_10(): void
    {
        $time = microtime(true);

        $data = [];
        $jobs = 10;

        for ($i = 0; $i < $jobs; ++$i) {
            $data[] = $this->item . $i;
            $this->add(['c' => $this->child, 'd' => ['value' => $this->item . $i]]);
        }

        $result = $this->go();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';

        echo PHP_EOL;
        res::chk_eq('10 jobs', [array_column($result, 'data'), $data]);
        echo PHP_EOL;
    }

    /**
     * Test 100 jobs
     */
    public function test_job_100(): void
    {
        $time = microtime(true);

        $data = [];
        $jobs = 100;

        for ($i = 0; $i < $jobs; ++$i) {
            $data[] = $this->item . $i;
            $this->add(['c' => $this->child, 'd' => ['value' => $this->item . $i]]);
        }

        $result = $this->go();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';

        echo PHP_EOL;
        res::chk_eq('100 jobs', [array_column($result, 'data'), $data]);
        echo PHP_EOL;
    }

    /**
     * Test 1 sleep job
     */
    public function test_job_1s(): void
    {
        $time = microtime(true);

        $result = $this->add(['c' => $this->child, 'd' => ['value' => $this->item, 'sleep' => 1]])->go();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;
        res::chk_eq('1 sleep job', [$result[0]['data'], $this->item]);
        echo PHP_EOL;
    }

    /**
     * Test 10 sleep jobs
     */
    public function test_job_10s(): void
    {
        $time = microtime(true);

        $data = [];
        $jobs = 10;

        for ($i = 0; $i < $jobs; ++$i) {
            $data[] = $this->item . $i;
            $this->add(['c' => $this->child, 'd' => ['value' => $this->item . $i, 'sleep' => 1]]);
        }

        $result = $this->go();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';

        echo PHP_EOL;
        res::chk_eq('10 sleep jobs', [array_column($result, 'data'), $data]);
        echo PHP_EOL;
    }

    /**
     * Test 100 sleep jobs
     */
    public function test_job_100s(): void
    {
        $time = microtime(true);

        $data = [];
        $jobs = 100;

        for ($i = 0; $i < $jobs; ++$i) {
            $data[] = $this->item . $i;
            $this->add(['c' => $this->child, 'd' => ['value' => $this->item . $i, 'sleep' => 1]]);
        }

        $result = $this->go();

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';

        echo PHP_EOL;
        res::chk_eq('100 sleep jobs', [array_column($result, 'data'), $data]);
        echo PHP_EOL;
    }

    /**
     * Test 1000 sleep jobs using 100 processes
     */
    public function test_job_1000s_100p(): void
    {
        $time = microtime(true);

        $data = [];
        $jobs = 1000;

        for ($i = 0; $i < $jobs; ++$i) {
            $data[] = $this->item . $i;
            $this->add(['c' => $this->child, 'd' => ['value' => $this->item . $i, 'sleep' => 1]]);
        }

        $result = $this->go(true, 100);

        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';

        echo PHP_EOL;
        res::chk_eq('1000 sleep jobs/100 processes', [array_column($result, 'data'), $data]);
        echo PHP_EOL;
    }
}