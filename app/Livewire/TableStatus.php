<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Table;
use App\Services\TableService;
use Illuminate\Support\Facades\Auth;

class TableStatus extends Component
{
    public $tables = [];
    public $updateInterval = 30; // segundos

    protected $listeners = [
        'table.updated' => 'loadTables',
        'order.created' => 'loadTables',
        'order.completed' => 'loadTables'
    ];

    public function mount()
    {
        $this->loadTables();
    }

    public function loadTables()
    {
        $tableService = app(TableService::class);
        $this->tables = $tableService->getAllTablesWithStatus();
    }

    public function toggleTableStatus($tableId)
    {
        try {
            $table = Table::findOrFail($tableId);
            $tableService = app(TableService::class);

            // Cambiar estado de mesa
            if ($table->status === 'available') {
                $result = $tableService->occupyTable($tableId, Auth::id());
            } else {
                $result = $tableService->freeTable($tableId);
            }

            if ($result['success']) {
                $this->loadTables();

                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'Mesa actualizada',
                    'message' => $result['message']
                ]);
            } else {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => $result['message']
                ]);
            }

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al actualizar mesa: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.table-status');
    }
}