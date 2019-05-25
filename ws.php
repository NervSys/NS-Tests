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

class ws
{
    public static $tz = 'server';

    //Client handshake status
    private $handshake = [];

    /**
     * Server constructor
     * php api.php -c="tests/ws-server" -d="address=tcp://0.0.0.0:8000"
     *
     * @param string $address
     */
    public function server(string $address = 'tcp://0.0.0.0:8000'): void
    {
        $clients = [];
        $stream  = socket::new('server')->bind($address)->create();

        while (true) {
            $read = $stream->listen($clients);

            $stream->accept($read, $clients);

            foreach ($read as $key => $client) {
                $msg = $stream->read($client);

                if ('' === $msg) {
                    $stream->close($client);
                    unset($clients[$key], $this->handshake[$key]);

                    echo 'Client offline: ' . (int)$client;
                    echo PHP_EOL . PHP_EOL;

                    continue;
                }

                $codes = $stream->ws_get_codes($msg);

                if (8 === $codes['opcode']) {
                    $stream->close($client);
                    unset($clients[$key], $this->handshake[$key]);

                    echo 'Client offline: ' . (int)$client;
                    echo PHP_EOL . PHP_EOL;

                    continue;
                }

                if (!isset($this->handshake[$key])) {
                    $response = $stream->ws_handshake($msg);

                    $send = $stream->send($client, $response);

                    if ($send === strlen($response)) {
                        $this->handshake[$key] = true;
                    } else {
                        $stream->close($client);
                        unset($clients[$key], $this->handshake[$key]);

                        echo 'Handshake failed: ' . (int)$client;
                        echo PHP_EOL . PHP_EOL;
                    }

                    continue;
                }

                $msg = $stream->ws_decode($msg);

                echo 'Message: ' . $msg;
                echo PHP_EOL;

                $msg = 'Message Received: ' . $msg;
                $msg = $stream->ws_encode($msg);

                //Send to all clients
                foreach ($clients as $k => $v) {
                    $send = $stream->send($v, $msg);
                    echo 'Send to ' . $k . ($send === strlen($msg) ? ' Done!' : ' Failed!') . PHP_EOL;
                }

                echo PHP_EOL;
            }
        }

        $stream->close($stream->source);
    }
}