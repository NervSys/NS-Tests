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
use core\lib\stc\factory;
use core\lib\std\log;
use ext\queue as redis_queue;

class queue extends redis_queue
{
    public $tz = [
        'test_add',
        'test_fail',
        'test_unique',
        'test_job_100',
        'test_job_1000',
        'test_job_10000',
        'test_delay_10'
    ];

    private $process  = '/' . queue::class . '-process';
    private $root_cmd = 'tests/lib/lib_queue-start';

    /**
     * queue constructor.
     */
    public function __construct()
    {
        //Start root process
        \ext\mpc::new()
            ->add(['c' => $this->root_cmd])
            ->go(false, 1);

        //Init queue instance
        parent::__construct();
    }

    /**
     * Queue test process
     *
     * @param string $rand
     * @param bool   $bool
     *
     * @return bool
     */
    public function process(string $rand, bool $bool): bool
    {
        if (10 === strlen($rand)) {
            factory::build(log::class)->alert('delay', [['time' => date('Y-m-d H:i:s', $rand)]]);
        }

        unset($rand);
        return $bool;
    }

    /**
     * Test add 1 job
     */
    public function test_add(): void
    {
        $add = $this->add(
            $this->process,
            [
                'rand' => hash('sha256', uniqid(mt_rand(), true)),
                'bool' => true
            ],
            'test'
        );

        res::chk_eq('add 1 job', [$add, 1]);
    }

    /**
     * Test add 1 fail job
     */
    public function test_fail(): void
    {
        $left = $this->show_fail(0, 1);

        $this->add(
            $this->process,
            [
                'rand' => hash('sha256', uniqid(mt_rand(), true)),
                'bool' => false
            ],
            'test_' . mt_rand(1, 10)
        );

        while (0 < $this->chk_job()) ;

        $remain = $this->show_fail(0, 1);

        res::chk_eq('add 1 fail job', [$remain['len'] - $left['len'], 1]);
    }

    /**
     * @return int
     */
    private function chk_job(): int
    {
        do {
            //Jobs
            $jobs = 0;

            //Read queue list
            $queue = $this->show_queue();

            //Count jobs
            foreach ($queue as $key => $item) {
                $jobs += $this->show_length($key);
            }

            if (0 === $jobs) {
                return 0;
            }

            sleep(ceil(log1p($jobs + 1)));

            //Left jobs
            $left = 0;

            //Read queue list
            $queue = $this->show_queue();

            //Count left jobs
            foreach ($queue as $key => $item) {
                $left += $this->show_length($key);
            }
        } while (0 < $jobs && 0 < $left && $left < $jobs);

        return $left < $jobs ? $left : $jobs;
    }

    /**
     * Test add 1 unique job
     */
    public function test_unique(): void
    {
        $this->add(
            $this->process,
            [
                'rand' => hash('sha256', uniqid(mt_rand(), true)),
                'bool' => true
            ],
            'unique',
            self::TYPE_UNIQUE,
            60
        );

        $add = $this->add(
            $this->process,
            [
                'rand' => hash('sha256', uniqid(mt_rand(), true)),
                'bool' => true
            ],
            'unique',
            self::TYPE_UNIQUE,
            60
        );

        res::chk_eq('add 1 unique job', [$add, -1]);
        echo PHP_EOL;
    }

    /**
     * Test 100 jobs
     *
     * @param int $jobs
     */
    public function test_job_100(int $jobs = 100): void
    {
        for ($i = 0; $i < $jobs; ++$i) {
            $this->add(
                $this->process,
                [
                    'rand' => hash('sha256', uniqid(mt_rand(), true)),
                    'bool' => true
                ],
                'test_' . mt_rand(1, 10)
            );
        }

        $time = microtime(true);

        res::chk_eq($jobs . ' jobs done', [$this->chk_job(), 0]);
        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;
        echo PHP_EOL;
    }

    /**
     * Test 1000 jobs
     *
     * @param int $jobs
     */
    public function test_job_1000(int $jobs = 1000): void
    {
        for ($i = 0; $i < $jobs; ++$i) {
            $this->add(
                $this->process,
                [
                    'rand' => hash('sha256', uniqid(mt_rand(), true)),
                    'bool' => true
                ],
                'test_' . mt_rand(1, 10)
            );
        }

        $time = microtime(true);

        res::chk_eq($jobs . ' jobs done', [$this->chk_job(), 0]);
        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;
        echo PHP_EOL;
    }

    /**
     * Test 10000 jobs
     *
     * @param int $jobs
     */
    public function test_job_10000(int $jobs = 10000): void
    {
        for ($i = 0; $i < $jobs; ++$i) {
            $this->add(
                $this->process,
                [
                    'rand' => hash('sha256', uniqid(mt_rand(), true)),
                    'bool' => true
                ],
                'test_' . mt_rand(1, 10)
            );
        }

        $time = microtime(true);

        res::chk_eq($jobs . ' jobs done', [$this->chk_job(), 0]);
        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;
        echo PHP_EOL;
    }

    /**
     * Test 10000 jobs
     *
     * @param int $jobs
     */
    public function test_delay_10(int $jobs = 10): void
    {
        factory::build(log::class)->alert('delay', [['start' => time()]]);
        for ($i = 0; $i < $jobs; ++$i) {
            $this->add(
                $this->process,
                [
                    'rand' => time() + 12,
                    'bool' => true
                ],
                'test_' . mt_rand(1, 10),
                parent::TYPE_DELAY,
                12
            );

            sleep(5);
        }

        $time = microtime(true);

        res::chk_eq($jobs . ' jobs done', [$this->chk_job(), 0]);
        echo 'Time Taken: ' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;
        echo PHP_EOL;
    }
}