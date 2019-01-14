<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 1/13/2019
 * Time: 7:35 PM
 */

namespace tests;

use tests\lib\base;
use ext\socket as sock;

class socket extends base
{
    public static $tz = 'server,client';

    /**
     * Server constructor
     * php api.php -c="tests/socket-server" -d="run_as=server&address=tcp://0.0.0.0:8000"
     *
     * @param string $run_as
     * @param string $address
     *
     * @throws \Exception
     */
    public function server(string $run_as, string $address): void
    {
        $clients = [];
        $socket  = sock::new($run_as, $address)->create();

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
     * php api.php -c="tests/socket-client" -d="run_as=client&address=tcp://127.0.0.1:8000"
     *
     * @param string $run_as
     * @param string $address
     *
     * @throws \Exception
     */
    public function client(string $run_as, string $address): void
    {
        $socket = sock::new($run_as, $address)->create();
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