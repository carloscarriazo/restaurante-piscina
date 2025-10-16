<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Table::query();

        // Filtrar por estado si se especifica
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filtrar por disponibilidad si se especifica
        if ($request->has('is_available') && $request->is_available !== '') {
            $query->where('is_available', $request->is_available);
        }

        // Buscar por nombre o número si se especifica
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('number', 'like', '%' . $request->search . '%');
            });
        }

        $tables = $query->orderBy('number')->paginate(12);

        return view('tables.index', compact('tables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tables.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'number' => 'required|string|max:10|unique:tables,number',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:20',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'is_available' => 'boolean'
        ]);

        $validated['is_available'] = $request->has('is_available');

        Table::create($validated);

        return redirect()->route('tables.index')
                        ->with('success', 'Mesa creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Table $table): View
    {
        $table->load(['orders' => function ($query) {
            $query->latest()->limit(5);
        }, 'reservations' => function ($query) {
            $query->latest()->limit(5);
        }]);

        return view('tables.show', compact('table'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Table $table): View
    {
        return view('tables.edit', compact('table'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Table $table): RedirectResponse
    {
        $validated = $request->validate([
            'number' => 'required|string|max:10|unique:tables,number,' . $table->id,
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:20',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:available,occupied,reserved,maintenance',
            'is_available' => 'boolean'
        ]);

        $validated['is_available'] = $request->has('is_available');

        $table->update($validated);

        return redirect()->route('tables.index')
                        ->with('success', 'Mesa actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table): RedirectResponse
    {
        // Verificar que la mesa no tenga órdenes activas
        $activeOrders = $table->orders()->whereIn('status', ['pending', 'in_process'])->count();

        if ($activeOrders > 0) {
            return redirect()->route('tables.index')
                            ->with('error', 'No se puede eliminar la mesa porque tiene órdenes activas.');
        }

        $table->delete();

        return redirect()->route('tables.index')
                        ->with('success', 'Mesa eliminada exitosamente.');
    }
}
