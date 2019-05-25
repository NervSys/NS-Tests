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

use ext\socket;

class udp
{
    public static $tz = 'server,client,broadcast';

    /**
     * Server constructor
     * php api.php -c="tests/udp-server" -d="address=udp://0.0.0.0:8000"
     *
     * @param string $address
     */
    public function server(string $address = 'udp://0.0.0.0:8000'): void
    {
        $clients = [];
        $stream  = socket::new('server')->bind($address)->create();

        while (true) {
            if (0 === $stream->listen($clients)) {
                continue;
            }

            $msg = $stream->read($stream->source);

            echo 'Message: ' . $msg;
            echo PHP_EOL;
        }

        $stream->close($stream->source);
    }

    /**
     * Client constructor
     * php api.php -c="tests/udp-client" -d="address=udp://127.0.0.1:8000"
     *
     * @param string $address
     */
    public function client(string $address = 'udp://127.0.0.1:8000'): void
    {
        $stream = socket::new('client')->bind($address)->create();
        $input  = fopen('php://stdin', 'r');

        while (true) {
            echo 'Message: ';

            if ('' === $data = trim(fgets($input, 65535))) {
                continue;
            }

            $send = $stream->send($stream->source, $data);

            echo '"' . $data . '" SEND ' . ($send ? 'Done!' : 'Failed!') . PHP_EOL;
        }

        $stream->close($stream->source);
    }

    /**
     * Server constructor
     * php api.php -c="tests/udp-broadcast" -d="udp://255.255.255.255:8000"
     *
     * @param string $address
     */
    public function broadcast(string $address = 'udp://255.255.255.255:8000'): void
    {
        socket::new('broadcast')
            ->msg('UDP Broadcasting: Hello World!')
            ->bind($address)
            ->timeout(2)
            ->create();
    }
}