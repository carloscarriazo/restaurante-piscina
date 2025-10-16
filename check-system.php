<?php

/**
 * Script de verificación y corrección del sistema
 * Blue Lagoon Restaurant Management System
 */

echo "🔍 Verificando sistema Blue Lagoon Restaurant...\n\n";

// 1. Verificar archivos críticos
echo "📁 Verificando archivos críticos:\n";
$criticalFiles = [
    'app/Services/BaseService.php',
    'app/Services/OrderService.php', 
    'app/Services/NotificationService.php',
    'app/Livewire/RealTimeNotifications.php',
    'app/Events/KitchenOrderReady.php',
    'resources/js/notifications.js',
    'resources/css/notifications.css',
    'routes/channels.php'
];

foreach ($criticalFiles as $file) {
    if (file_exists($file)) {
        echo "✅ {$file}\n";
    } else {
        echo "❌ FALTANTE: {$file}\n";
    }
}

// 2. Verificar dependencias de Composer
echo "\n📦 Verificando composer.json:\n";
if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'), true);
    
    $requiredPackages = [
        'laravel/framework',
        'livewire/livewire',
        'laravel/jetstream',
        'laravel/sanctum'
    ];
    
    foreach ($requiredPackages as $package) {
        if (isset($composer['require'][$package])) {
            echo "✅ {$package}\n";
        } else {
            echo "❌ FALTANTE: {$package}\n";
        }
    }
} else {
    echo "❌ composer.json no encontrado\n";
}

// 3. Verificar archivo .env
echo "\n⚙️ Verificando configuración:\n";
if (file_exists('.env')) {
    $env = file_get_contents('.env');
    
    $requiredEnvVars = [
        'APP_NAME',
        'DB_CONNECTION',
        'DB_DATABASE',
        'BROADCAST_DRIVER'
    ];
    
    foreach ($requiredEnvVars as $var) {
        if (strpos($env, $var . '=') !== false) {
            echo "✅ {$var}\n";
        } else {
            echo "⚠️ REVISAR: {$var}\n";
        }
    }
} else {
    echo "❌ .env no encontrado\n";
}

// 4. Verificar migraciones pendientes
echo "\n🗄️ Verificando migraciones:\n";
if (is_dir('database/migrations')) {
    $migrations = glob('database/migrations/*.php');
    echo "📊 Total migraciones encontradas: " . count($migrations) . "\n";
    
    // Verificar migraciones críticas del sistema
    $criticalMigrations = [
        'create_notifications_table',
        'add_missing_fields_to_tables_table',
        'add_missing_fields_to_orders_table',
        'create_invoices_table'
    ];
    
    foreach ($criticalMigrations as $migration) {
        $found = false;
        foreach ($migrations as $file) {
            if (strpos(basename($file), $migration) !== false) {
                echo "✅ {$migration}\n";
                $found = true;
                break;
            }
        }
        if (!$found) {
            echo "⚠️ NO ENCONTRADA: {$migration}\n";
        }
    }
} else {
    echo "❌ Directorio de migraciones no encontrado\n";
}

// 5. Verificar providers
echo "\n🔧 Verificando providers:\n";
if (file_exists('bootstrap/providers.php')) {
    $providers = file_get_contents('bootstrap/providers.php');
    
    $requiredProviders = [
        'BroadcastServiceProvider',
        'EventServiceProvider'
    ];
    
    foreach ($requiredProviders as $provider) {
        if (strpos($providers, $provider) !== false) {
            echo "✅ {$provider}\n";
        } else {
            echo "❌ FALTANTE: {$provider}\n";
        }
    }
} else {
    echo "❌ bootstrap/providers.php no encontrado\n";
}

echo "\n🎯 Resumen de verificación completado.\n";
echo "💡 Para continuar, ejecuta: php artisan serve\n";
echo "🌐 Luego visita: http://localhost:8000\n\n";

// Generar reporte de comandos sugeridos
echo "📋 Comandos sugeridos para completar la configuración:\n";
echo "   php artisan migrate\n";
echo "   php artisan config:cache\n";
echo "   php artisan route:cache\n";
echo "   npm install && npm run build\n";
echo "   php artisan serve\n\n";

echo "🚀 Sistema listo para testing!\n";