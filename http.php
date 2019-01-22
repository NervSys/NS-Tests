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

namespace tests;

use ext\socket;
use tests\lib\base;

class http extends base
{
    public static $tz = 'server,client';

    /**
     * Server constructor
     * php api.php -c="tests/http-server" -d="address=tcp://0.0.0.0:80"
     *
     * @param string $address
     */
    public function server(string $address = 'tcp://0.0.0.0:80'): void
    {
        $clients = [];
        $stream  = socket::new('server')->bind($address)->create();

        while (true) {
            $read = $stream->listen($clients);

            $stream->accept($read, $clients);

            foreach ($read as $key => $client) {
                $msg = $stream->read($client);

                echo 'Message: ';
                echo $msg;
                echo PHP_EOL;

                $stream->send($client, 'Request Received: ' . PHP_EOL . $msg);

                $stream->close($client);
                unset($clients[$key]);
            }
        }

        $stream->close($stream->source);
    }
}