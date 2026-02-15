<?php

namespace App\Console\Commands;

use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use RouterOS\Sohag\RouterosAPI;

class Mikmon2RadiusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mikmon2radius {router_ip} {user} {password} {port} {operator_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import existing Hotspot customers from MikroTik router to RADIUS';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $config = [
            'host' => $this->argument('router_ip'),
            'user' => $this->argument('user'),
            'pass' => $this->argument('password'),
            'port' => (int)$this->argument('port'),
            'attempts' => 1
        ];

        $api = new RouterosAPI($config);

        if ($api->connect($config['host'], $config['user'], $config['pass'])) {
            $hotspotUsers = $api->getMktRows('/ip/hotspot/user');

            foreach ($hotspotUsers as $hotspotUser) {
                if ($this->isValidHotspotUser($hotspotUser)) {
                    $package = $this->createOrLinkPackage($hotspotUser);
                    $this->createCustomer($hotspotUser, $package);
                }
            }

            $this->info('Hotspot users imported successfully.');
            return 0;
        } else {
            $this->error('Could not connect to router.');
            return 1;
        }
    }

    private function isValidHotspotUser(array $hotspotUser): bool
    {
        return isset($hotspotUser['bytes-in']) && $hotspotUser['bytes-in'] > 0;
    }

    private function createOrLinkPackage(array $hotspotUser): Package
    {
        $packageName = $hotspotUser['profile'] ?? 'default';
        return Package::firstOrCreate(
            ['name' => $packageName],
            [
                'price' => 0,
                'description' => 'Automatically created package',
                'is_active' => true,
                'tenant_id' => $this->argument('operator_id'),
                'admin_id' => $this->argument('operator_id'),
            ]
        );
    }

    private function createCustomer(array $hotspotUser, Package $package): void
    {
        $expiryDate = $this->parseExpiryDate($hotspotUser['comment'] ?? null);

        User::create([
            'name' => $hotspotUser['name'],
            'username' => $hotspotUser['mac-address'],
            'password' => bcrypt($hotspotUser['mac-address']),
            'package_id' => $package->id,
            'connection_type' => 'hotspot',
            'package_expired_at' => $expiryDate,
            'tenant_id' => $this->argument('operator_id'),
            'admin_id' => $this->argument('operator_id'),
            'is_active' => true,
        ]);

        // Create RADIUS entries
        // radcheck
        // radreply
        // This part requires more information about the RADIUS schema
    }

    private function parseExpiryDate(?string $comment): ?Carbon
    {
        if ($comment) {
            try {
                return Carbon::createFromFormat('M/d/Y H:i:s', $comment);
            } catch (\Exception $e) {
                // ignore
            }
        }
        return null;
    }
}
