<?php

namespace App\Http\Controllers;

use App\Models\Nas;
use Illuminate\Http\Request;
use RouterOS\Sohag\RouterosAPI;

class RouterConfigurationController extends Controller
{
    public function create(Nas $router)
    {
        return view('routers.configuration.create', compact('router'));
    }

    public function store(Request $request, Nas $router)
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
            // 1. RADIUS Settings
            $api->addMktRows('radius', [
                [
                    'address' => config('radius.server_ip'),
                    'authentication-port' => config('radius.authentication_port'),
                    'accounting-port' => config('radius.accounting_port'),
                    'secret' => $router->secret,
                    'service' => 'hotspot,ppp',
                    'timeout' => '3s',
                    'require-message-auth' => 'no',
                ]
            ]);

            // 2. System Identity (Optional)
            if ($request->change_system_identity) {
                $identity = auth()->user()->company_in_native_lang . '-' . $router->shortname;
                $api->ttyWirte('/system/identity/set', ['name' => $identity]);
            }

            // 3. Firewall NAT Rules (Hotspot)
            $api->addMktRows('/ip/firewall/nat', [
                [
                    'chain' => 'pre-hotspot',
                    'dst-address-type' => '!local',
                    'hotspot' => 'auth',
                    'action' => 'accept',
                    'comment' => 'bypassed auth',
                ]
            ]);

            // 4. Walled Garden (Hotspot)
            $api->addMktRows('/ip/hotspot/walled-garden/ip', [
                [
                    'action' => 'accept',
                    'dst-address' => config('radius.server_ip'),
                    'comment' => 'Radius Server',
                ]
            ]);

            // 5. Hotspot Server Settings
            $api->ttyWirte('/ip/hotspot/set', [
                'idle-timeout' => '5m',
                'keepalive-timeout' => 'none',
                'login-timeout' => 'none',
            ]);

            // 6. Hotspot Profile Settings
            $api->ttyWirte('/ip/hotspot/profile/set', [
                'login-by' => 'mac,cookie,http-chap,http-pap,mac-cookie',
                'mac-auth-mode' => 'mac-as-username-and-password',
                'http-cookie-lifetime' => '6h',
                'split-user-domain' => 'no',
                'use-radius' => 'yes',
                'radius-accounting' => 'yes',
                'radius-interim-update' => '5m',
                'nas-port-type' => 'wireless-802.11',
                'radius-mac-format' => 'XX:XX:XX:XX:XX:XX',
            ]);

            // 7. Hotspot User Profile Settings
            $onLoginScript = ':foreach n in=[/queue simple find comment=priority_1] do={ /queue simple move $n [:pick [/queue simple find] 0] }';
            $onLogoutScript = '/ip hotspot host remove [find where address=$address and !authorized and !bypassed]';
            $api->ttyWirte('/ip/hotspot/user/profile/set', [
                'idle-timeout' => 'none',
                'keepalive-timeout' => '2m',
                'queue-type' => 'hotspot-default',
                'on-login' => $onLoginScript,
                'on-logout' => $onLogoutScript,
            ]);

            // 8. PPPoE Server Settings
            $api->ttyWirte('/ppp/profile/set', ['default' => ['local-address' => '10.0.0.1']]);
            $api->ttyWirte('/interface/pppoe-server/server/set', [
                'authentication' => 'pap,chap',
                'one-session-per-host' => 'yes',
                'default-profile' => 'default',
            ]);

            // 9. PPP AAA Settings
            $api->ttyWirte('/ppp/aaa/set', [
                'interim-update' => '5m',
                'use-radius' => 'yes',
                'accounting' => 'yes',
            ]);

            // 10. PPP Profile On-Up Script
            $onUpScript = ':local sessions [/ppp active print count-only where name=$user]; :if ( $sessions > 1) do={ :log info ("disconnecting " . $user  ." duplicate" ); /ppp active remove [find where (name=$user && uptime<00:00:30 )]; }';
            $api->ttyWirte('/ppp/profile/set', ['default' => ['on-up' => $onUpScript]]);

            // 11. Suspended Users Pool
            $api->addMktRows('/ip/pool', [
                [
                    'name' => 'suspended-pool',
                    'ranges' => '100.65.96.0/20',
                ]
            ]);

            // 12. RADIUS Incoming
            $api->ttyWirte('/radius/incoming/set', ['accept' => 'yes']);

            // 13. SNMP Configuration
            $api->ttyWirte('/snmp/set', ['enabled' => 'yes']);
            $api->addMktRows('/snmp/community', [['name' => 'billing']]);

            // 14. Firewall Rules for Suspended Pool
            $api->addMktRows('/ip/firewall/filter', [
                [
                    'chain' => 'forward',
                    'src-address' => '100.65.96.0/20',
                    'action' => 'drop',
                    'comment' => 'drop suspended pool',
                ],
                [
                    'chain' => 'input',
                    'src-address' => '100.65.96.0/20',
                    'action' => 'drop',
                    'comment' => 'drop suspended pool',
                ]
            ]);

            return redirect()->route('routers.show', $router)->with('success', 'Router configured successfully!');
        } else {
            return redirect()->route('routers.show', $router)->with('error', 'Could not connect to router!');
        }
    }
}
