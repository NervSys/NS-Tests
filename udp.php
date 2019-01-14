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

use tests\lib\base;
use ext\socket as sock;

class udp extends base
{
    public static $tz = 'server,client';

    /**
     * Server constructor
     * php api.php -c="tests/udp-server" -d="address=udp://0.0.0.0:8000"
     *
     * @param string $address
     *
     * @throws \Exception
     */
    public function server(string $address): void
    {
        $clients = [];
        $socket  = sock::new(__FUNCTION__, $address)->create();

        while (true) {
            if (0 === $socket->listen($clients)) {
                continue;
            }

            $msg = '';

            $msg_len = $socket->read($socket->source, $msg);

            echo 'Message: ' . $msg;
            echo PHP_EOL;
            echo 'Length: ' . $msg_len;
            echo PHP_EOL . PHP_EOL;
        }

        $socket->close($socket->source);
    }

    /**
     * Client constructor
     * php api.php -c="tests/udp-client" -d="address=udp://127.0.0.1:8000"
     *
     * @param string $address
     *
     * @throws \Exception
     */
    public function client(string $address): void
    {
        $socket = sock::new(__FUNCTION__, $address)->create();
        $input  = fopen('php://stdin', 'r');

        while (true) {
            echo 'Message: ';
            $data = trim(fgets($input, 65535));
            $send = $socket->send($socket->source, $data, $socket->host, $socket->port);

            echo '"' . $data . '" SEND ' . ($send ? 'Done!' : 'Failed!') . PHP_EOL;
        }

        $socket->close($socket->source);
    }
}