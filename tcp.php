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

class tcp
{
    public static $tz = 'server,client';

    /**
     * Server constructor
     * php api.php -c="tests/tcp-server" -d="address=tcp://0.0.0.0:8000"
     *
     * @param string $address
     */
    public function server(string $address = 'tcp://0.0.0.0:8000'): void
    {
        $clients = [];
        $stream  = socket::new('server')->bind($address)->start();

        while (true) {
            $read = $stream->listen($clients);

            $stream->accept($read, $clients);

            foreach ($read as $key => $client) {
                $head = (int)$stream->read($client, 4);

                if (0 === $head) {
                    $stream->close($client);
                    unset($clients[$key]);

                    echo 'Client offline: ' . (int)$client;
                    echo PHP_EOL . PHP_EOL;

                    continue;
                }

                $msg = $stream->read($client, $head);

                if ('' === $msg) {
                    $stream->close($client);
                    unset($clients[$key]);

                    echo 'Client offline: ' . (int)$client;
                    echo PHP_EOL . PHP_EOL;

                    continue;
                }

                echo 'Message: ' . $msg;
                echo PHP_EOL;
            }
        }

        $stream->close($stream->source);
    }

    /**
     * Client constructor
     * php api.php -c="tests/tcp-client" -d="address=tcp://127.0.0.1:8000"
     *
     * @param string $address
     */
    public function client(string $address = 'tcp://127.0.0.1:8000'): void
    {
        $stream = socket::new('client')->bind($address)->start();
        $input  = fopen('php://stdin', 'r');

        while (true) {
            echo 'Message: ';

            if ('' === $data = trim(fgets($input, 65535))) {
                continue;
            }

            $data_len = sprintf('%04s', (string)strlen($data));

            $send = $stream->send($stream->source, $data_len . $data);

            echo '"' . $data . '" SEND ' . ($send ? 'Done!' : 'Failed!') . PHP_EOL;
        }

        $stream->close($stream->source);
    }
}