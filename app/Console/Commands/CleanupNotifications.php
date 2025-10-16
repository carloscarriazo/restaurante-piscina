<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class CleanupNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup {--days=7 : Number of days to keep notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old notifications from the database';

    /**
     * Create a new command instance.
     */
    public function __construct(private NotificationService $notificationService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');

        $this->info("Iniciando limpieza de notificaciones de más de {$days} días...");

        try {
            $deletedCount = $this->notificationService->cleanupOldNotifications();

            if ($deletedCount > 0) {
                $this->info("✅ Se eliminaron {$deletedCount} notificaciones antiguas.");
            } else {
                $this->info("ℹ️  No se encontraron notificaciones antiguas para eliminar.");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error durante la limpieza: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}