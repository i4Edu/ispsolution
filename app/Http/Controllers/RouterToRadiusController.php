<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Nas;
use RouterOS\Sohag\RouterosAPI;

class RouterToRadiusController extends Controller
{
    public static function transfer(Nas $router, Customer $customer)
    {
        $config = [
            'host' => $router->nasname,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port,
            'attempts' => 1
        ];

        $api = new RouterosAPI($config);

        if ($api->connect($config['host'], $config['user'], $config['pass'])) {
            // Find customer in /ppp secret and disable
            $pppSecret = $api->getMktRows('ppp_secret', ['name' => $customer->username]);

            if (!empty($pppSecret)) {
                $api->editMktRow('ppp_secret', $pppSecret[0], ['disabled' => 'yes']);
            }

            // Disconnect active sessions
            $pppActive = $api->getMktRows('ppp_active', ['name' => $customer->username]);
            foreach ($pppActive as $activeSession) {
                $api->removeMktRows('ppp_active', [$activeSession]);
            }

            return true;
        } else {
            // Log error or handle connection failure
            return false;
        }
    }
}
