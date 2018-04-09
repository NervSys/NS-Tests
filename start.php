<?php

/**
 * NervSys Tests Script
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

namespace tests;

use ext\file;
use core\ctr\router;

class start
{
    public static $tz = [
        'run' => ['tests']
    ];

    /**
     * Check data equality
     *
     * @param string $name
     * @param array  $data
     */
    protected static function chk_eq(string $name, array $data): void
    {
        echo $name . ': ' . ($data[0] === $data[1] ? 'PASSED!' : 'Failed! ' . (string)$data[0] . ' !== ' . (string)$data[1]);
        echo PHP_EOL;
    }

    /**
     * Check greater than
     *
     * @param string $name
     * @param array  $data
     */
    protected static function chk_gt(string $name, array $data): void
    {
        echo $name . ': ' . ($data[0] > $data[1] ? 'PASSED!' : 'Failed! ' . $data[0] . ' <= ' . $data[1]);
        echo PHP_EOL;
    }

    /**
     * Check less than
     *
     * @param string $name
     * @param array  $data
     */
    protected static function chk_lt(string $name, array $data): void
    {
        echo $name . ': ' . ($data[0] < $data[1] ? 'PASSED!' : 'Failed! ' . $data[0] . ' >= ' . $data[1]);
        echo PHP_EOL;
    }

    /**
     * Initial test
     */
    public static function init(): void
    {
        if (empty(router::$data)) {
            $list = file::get_list(__DIR__ . '/module/', '*.php');
            foreach ($list as $file) router::$data['tests'][] = substr(basename($file), 0, -4);
        } else {
            router::$data['tests'] = array_keys(router::$data);
            foreach (router::$data['tests'] as $key => $value) router::$data['tests'][$key] = 'test_' . $value;
        }
    }

    /**
     * Run test
     *
     * @param array $tests
     */
    public static function run(array $tests): void
    {
        foreach ($tests as $test) {
            try {
                $space = '\\' . __NAMESPACE__ . '\\module\\' . $test;

                if (!class_exists($space)) throw new \Exception('Test Module [' . $space . '] NOT found!');
                if (!method_exists($space, 'go')) throw new \Exception('Test Module [' . $space . '] ERROR!');

                forward_static_call([$space, 'go']);
            } catch (\Throwable $exception) {
                echo $exception->getMessage();
            }

            echo PHP_EOL . PHP_EOL;
        }
    }
}