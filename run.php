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

namespace app\UnitTest;

use ext\conf;
use ext\file;

/**
 * Class run
 *
 * @package app\UnitTest
 */
class run
{
    /**
     * run constructor.
     */
    public function __construct()
    {
        //Get unit name from argv
        $module = array_slice($_SERVER['argv'], 2);

        //Fetch all modules from files
        if (empty($module)) {
            $file_list = file::get_list(__DIR__ . '/Unit', '*.php');

            foreach ($file_list as $file) {
                $module[] = substr(basename($file), 0, -4);
            }
        }

        //Load conf
        conf::load('app/UnitTest', 'conf');

        //Run test
        $this->start($module);
    }

    /**
     * Start test
     *
     * @param $module
     */
    private function start(array $module): void
    {
        foreach ($module as $module) {
            $class  = '\\app\\UnitTest\\Unit\\' . $module;
            $object = new $class();

            echo 'Start testing ' . $module . '...' . PHP_EOL;

            foreach ($object->test_list as $fn) {
                $object->$fn();
            }

            unset($object);
            echo PHP_EOL;
        }
    }
}