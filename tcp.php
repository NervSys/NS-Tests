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

class tcp extends base
{
    public static $tz = 'server,client';

    /**
     * Server constructor
     * php api.php -c="tests/tcp-server" -d="address=tcp://0.0.0.0:8000"
     *
     * @param string $address
     *
     * @throws \Exception
     */
    public function server(string $address = 'tcp://0.0.0.0:8000'): void
    {
        $clients = [];
        $socket  = sock::new(__FUNCTION__, $address)->create();

        while (true) {
            $clients = $socket->listen($clients);

            $full_list = $socket->accept($clients);

            foreach ($clients as $key => $client) {
                $head = $msg = '';

                $head_len = $socket->read($client, $head, 4);

                echo 'Head: ' . $head;
                echo PHP_EOL;
                echo 'Length: ' . $head_len;
                echo PHP_EOL . PHP_EOL;

                if ('0000' === $head) {
                    $socket->close($client);
                    unset($full_list[$key]);

                    echo 'Client offline: ' . (int)$client;
                    echo PHP_EOL . PHP_EOL;

                    continue;
                }

                $msg_len = $socket->read($client, $msg, (int)$head);

                if (-1 === $msg_len) {
                    $socket->close($client);
                    unset($full_list[$key]);

                    echo 'Client offline: ' . (int)$client;
                    echo PHP_EOL . PHP_EOL;

                    continue;
                }

                echo 'Message: ' . $msg;
                echo PHP_EOL;
                echo 'Length: ' . $msg_len;
                echo PHP_EOL . PHP_EOL;
            }

            //Copy clients from full list
            $clients = $full_list;
        }

        $socket->close($socket->source);
    }

    /**
     * Client constructor
     * php api.php -c="tests/tcp-client" -d="address=tcp://127.0.0.1:8000"
     *
     * @param string $address
     *
     * @throws \Exception
     */
    public function client(string $address = 'tcp://127.0.0.1:8000'): void
    {
        $socket = sock::new(__FUNCTION__, $address)->create();
        $input  = fopen('php://stdin', 'r');

        while (true) {
            echo 'Message: ';
            $data     = trim(fgets($input, 65535));
            $data_len = sprintf('%04s', (string)strlen($data));

            $send = $socket->send($socket->source, $data_len . $data);

            echo '"' . $data . '" SEND ' . ($send ? 'Done!' : 'Failed!') . PHP_EOL;
        }

        $socket->close($socket->source);
    }
}