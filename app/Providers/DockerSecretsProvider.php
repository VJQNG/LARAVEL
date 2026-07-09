<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DockerSecretsProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach ($_ENV as $key => $value) {
            if (str_ends_with($key, '_FILE') && file_exists($value)) {
                $realKey = str_replace('_FILE', '', $key);
                $secret  = trim(file_get_contents($value));
                
                // Parche de enrutamiento explícito de configuraciones
                if ($realKey === 'DB_PASSWORD') {
                    config(['database.connections.mysql.password' => $secret]);
                } elseif ($realKey === 'APP_KEY') {
                    config(['app.key' => $secret]);
                } else {
                    config([strtolower(str_replace('_', '.', $realKey)) => $secret]);
                }
                
                putenv("$realKey=$secret");
                $_ENV[$realKey] = $secret;
                $_SERVER[$realKey] = $secret;
            }
        }
    }
}
