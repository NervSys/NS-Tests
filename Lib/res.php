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

namespace app\UnitTest\Lib;

/**
 * Class res
 *
 * @package app\UnitTest\Lib
 */
class res
{
    /**
     * Check data equality
     *
     * @param string $name
     * @param array  $data
     */
    public static function chk(string $name, array $data): void
    {
        echo '[' . $name . ']: ' . "\t";

        if (is_object($data[0])) {
            $data[0] = (array)$data[0];
        }

        if (is_object($data[1])) {
            $data[1] = (array)$data[1];
        }

        if (is_array($data[0])) {
            ksort($data[0]);
            $data[0] = json_encode($data[0]);
        }

        if (is_array($data[1])) {
            ksort($data[1]);
            $data[1] = json_encode($data[1]);
        }

        echo $data[0] !== $data[1]
            ? 'Failed! (' . (string)$data[0] . ' !== ' . (string)$data[1] . ')'
            : 'PASSED!';

        echo PHP_EOL;
    }
}