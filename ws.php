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

class ws extends base
{
    public static $tz = 'server,client';

    //Client handshake status
    private $handshake = [];

    /**
     * Server constructor
     * php api.php -c="tests/ws-server" -d="address=tcp://0.0.0.0:8000"
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
                $msg = '';

                $msg_len = $socket->read($client, $msg);

                if (8 >= $msg_len) {
                    $socket->close($client);
                    unset($full_list[$key], $this->handshake[$key]);

                    echo 'Client offline: ' . (int)$client;
                    echo PHP_EOL . PHP_EOL;

                    continue;
                }

                if (!isset($this->handshake[$key])) {
                    $response = $socket->ws_handshake($msg);
                    $send     = $socket->send($client, $response);

                    if ($send) {
                        $this->handshake[$key] = true;
                    } else {
                        $socket->close($client);
                        unset($full_list[$key], $this->handshake[$key]);

                        echo 'Handshake failed: ' . (int)$client;
                        echo PHP_EOL . PHP_EOL;
                    }

                    continue;
                }

                $msg = $socket->ws_decode($msg);

                echo 'Message: ' . $msg;
                echo PHP_EOL;

                $msg = 'Message Received: ' . $msg;
                $msg = $socket->ws_encode($msg);

                $send = $socket->send($client, $msg);
                echo 'Return ' . ($send ? 'Done!' : 'Failed!') . PHP_EOL;
                echo PHP_EOL;
            }

            //Copy clients from full list
            $clients = $full_list;
        }

        $socket->close($socket->source);
    }
}