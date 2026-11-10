<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class DatabaseHealthCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:health-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely checks database connectivity and SSL encryption without exposing credentials';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Testing database connectivity...');

        try {
            $pdo = DB::connection()->getPdo();

            // Run a lightweight ping query
            DB::select('SELECT 1');

            $driver = DB::connection()->getDriverName();
            $this->info("Database Connection: SUCCESSFUL (Driver: {$driver})");

            // Verify SSL status if MySQL
            if ($driver === 'mysql') {
                $sslStatus = DB::select("SHOW STATUS LIKE 'Ssl_cipher'");
                $cipher = ! empty($sslStatus) ? ($sslStatus[0]->Value ?? $sslStatus[0]->value ?? '') : '';

                if (! empty($cipher)) {
                    $this->info("SSL Encryption: ACTIVE (Cipher: {$cipher})");
                } else {
                    $this->warn('SSL Encryption: NOT DETECTED ON CURRENT CONNECTION');
                }
            }

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Database Connection: FAILED');

            // Sanitize error message to prevent password or credential leakage
            $safeMessage = preg_replace('/password=[^;\s&]+/i', 'password=******', $e->getMessage());
            $this->line("Reason: {$safeMessage}");

            return Command::FAILURE;
        }
    }
}
