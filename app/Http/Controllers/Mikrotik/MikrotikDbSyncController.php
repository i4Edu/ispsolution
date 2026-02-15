<?php

namespace App\Http\Controllers\Mikrotik;

use App\Http\Controllers\Controller;
use App\Models\MikrotikIpPool; // Assuming these models exist
use App\Models\MikrotikPppProfile; // Assuming these models exist
use App\Models\MikrotikPppSecret; // Assuming these models exist
use App\Models\CustomerImportRequest; // Assuming this model exists
use Illuminate\Http\Request;
use RouterOS\Sohag\RouterosAPI;
use Carbon\Carbon;

class MikrotikDbSyncController extends Controller
{
    public static function sync(CustomerImportRequest $customer_import_request)
    {
        $router = $customer_import_request->nas; // Assuming CustomerImportRequest has a relation to Nas

        if (!$router) {
            // Handle error: No router associated with the import request
            return false;
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
            // Delete old imports related to this request (if any)
            MikrotikIpPool::where('customer_import_request_id', $customer_import_request->id)->delete();
            MikrotikPppProfile::where('customer_import_request_id', $customer_import_request->id)->delete();
            MikrotikPppSecret::where('customer_import_request_id', $customer_import_request->id)->delete();

            // 1. Import IP Pools from MikroTik
            $ip4pools = $api->getMktRows('ip_pool');
            while ($ip4pool = array_shift($ip4pools)) {
                $ranges = self::parseIpPool($ip4pool['ranges']);

                $ip_pool = new MikrotikIpPool();
                $ip_pool->customer_import_request_id = $customer_import_request->id;
                $ip_pool->tenant_id = $customer_import_request->tenant_id;
                $ip_pool->admin_id = $customer_import_request->admin_id;
                $ip_pool->nas_id = $customer_import_request->nas_id;
                $ip_pool->name = $ip4pool['name'];
                $ip_pool->ranges = $ranges;
                $ip_pool->save();
            }

            // 2. Import PPP Profiles from MikroTik (excludes default profile)
            $ppp_profiles = $api->getMktRows('ppp_profile', ['default' => 'no']);
            while ($ppp_profile = array_shift($ppp_profiles)) {
                $mikrotik_ppp_profile = new MikrotikPppProfile();
                $mikrotik_ppp_profile->customer_import_request_id = $customer_import_request->id;
                $mikrotik_ppp_profile->tenant_id = $customer_import_request->tenant_id;
                $mikrotik_ppp_profile->admin_id = $customer_import_request->admin_id;
                $mikrotik_ppp_profile->nas_id = $customer_import_request->nas_id;
                $mikrotik_ppp_profile->name = $ppp_profile['name'];
                $mikrotik_ppp_profile->local_address = $ppp_profile['local-address'] ?? '';
                $mikrotik_ppp_profile->remote_address = $ppp_profile['remote-address'] ?? '';
                $mikrotik_ppp_profile->save();
            }

            // 3. Import PPP Secrets from MikroTik
            $now = Carbon::now()->timestamp;
            $file = 'ppp-secret-backup-by-billing' . $now;
            $api->ttyWirte('/ppp/secret/export', ['file' => $file]);

            $query = [];
            // Assuming customer_import_request has a property like 'import_disabled_user'
            if (property_exists($customer_import_request, 'import_disabled_user') && $customer_import_request->import_disabled_user == 'no') {
                $query = ['disabled' => 'no'];
            }

            $secrets = $api->getMktRows('ppp_secret', $query);
            while ($secret = array_shift($secrets)) {
                $mikrotik_ppp_secret = new MikrotikPppSecret();
                $mikrotik_ppp_secret->customer_import_request_id = $customer_import_request->id;
                $mikrotik_ppp_secret->tenant_id = $customer_import_request->tenant_id;
                $mikrotik_ppp_secret->admin_id = $customer_import_request->admin_id;
                $mikrotik_ppp_secret->nas_id = $customer_import_request->nas_id;
                $mikrotik_ppp_secret->name = $secret['name'];
                $mikrotik_ppp_secret->password = $secret['password'];
                $mikrotik_ppp_secret->profile = $secret['profile'] ?? '';
                $mikrotik_ppp_secret->comment = json_encode($secret['comment'] ?? '', JSON_PARTIAL_OUTPUT_ON_ERROR);
                $mikrotik_ppp_secret->disabled = $secret['disabled'] ?? '';
                $mikrotik_ppp_secret->save();
            }

            return true;
        } else {
            // Log error or handle connection failure
            return false;
        }
    }

    // Helper function for IP range parsing, as provided in the document
    private static function parseIpPool($rangesString)
    {
        // This is a placeholder. Actual implementation would involve complex parsing
        // of MikroTik's varied IP range formats into CIDR.
        // For example: "10.0.0.1-10.0.0.50,10.0.1.1-10.0.1.50" -> "10.0.0.0/23"
        // "192.168.1.0/24" -> "192.168.1.0/24"
        // "172.16.0.1-172.16.0.254" -> "172.16.0.0/24"
        return $rangesString; // Returning as is for now
    }
}
