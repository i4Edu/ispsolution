<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Nas;
use RouterOS\Sohag\RouterosAPI;

class QueryInRouterController extends Controller
{
    public static function getOnlineStatus(Customer $customer)
    {
        // Assuming a customer has a relation to a NAS (router)
        $router = $customer->nas; // Adjust this based on your Customer model relationship

        if (!$router) {
            return 0; // No router associated with the customer
        }

        $config = [
            'host' => $router->nasname,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port,
            'attempts' => 1
        ];

        $api = new RouterosAPI($config);

        if ($api->connect($config['host'], $config['user'], $config['pass'])) {
            $onlineCount = 0;

            if ($customer->connection_type === 'pppoe') {
                $pppActive = $api->getMktRows('ppp_active', ['name' => $customer->username]);
                $onlineCount += count($pppActive);
            } elseif ($customer->connection_type === 'hotspot') {
                $hotspotActive = $api->getMktRows('ip_hotspot_active', ['user' => $customer->username]);
                $onlineCount += count($hotspotActive);
            }
            // Add other connection types as needed

            return $onlineCount;

        } else {
            // Log error or handle connection failure
            return 0;
        }
    }
}