<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\DailyDiscount;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Mostrar lista de pedidos
     */
    public function index()
    {
        $user = Auth::user();
        $userRoles = $user->roles->pluck('nombre')->toArray();

        // Filtrar pedidos según el rol
        $query = Order::with(['table', 'user', 'items.product']);

        if (in_array('Mesero', $userRoles)) {
            // Meseros solo ven sus propios pedidos
            $query->where('user_id', $user->id);
        } elseif (in_array('Cocinero', $userRoles)) {
            // Cocineros ven pedidos pendientes y en proceso
            $query->whereIn('status', ['pending', 'in_process']);
        }

        $orders = $query->latest()->paginate(15);

        return view('orders.index', compact('orders', 'userRoles'));
    }

    /**
     * Editar un pedido (solo meseros)
     */
    public function edit(Order $order)
    {
        $user = Auth::user();

        if (!$order->canBeEditedBy($user)) {
            return redirect()->back()->with('error', 'No puedes editar este pedido');
        }

        return view('orders.edit', compact('order'));
    }

    /**
     * Actualizar un pedido
     */
    public function update(Request $request, Order $order)
    {
        try {
            $user = Auth::user();
            $this->orderService->editOrder($order, $user, $request->all());

            return redirect()->route('orders.index')
                ->with('success', 'Pedido actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Combinar facturas
     */
    public function combineBilling(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:2',
            'order_ids.*' => 'exists:orders,id'
        ]);

        try {
            $user = Auth::user();
            $combinedOrder = $this->orderService->combineBilling($request->order_ids, $user);

            return redirect()->route('orders.show', $combinedOrder)
                ->with('success', 'Facturas combinadas exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Panel de cocina
     */
    public function kitchen()
    {
        $pendingOrders = Order::with(['table', 'user', 'items.product'])
            ->where('status', 'pending')
            ->where('kitchen_notified', true)
            ->latest('kitchen_notified_at')
            ->get();

        $inProcessOrders = Order::with(['table', 'user', 'items.product'])
            ->where('status', 'in_process')
            ->latest('updated_at')
            ->get();

        return view('kitchen.index', compact('pendingOrders', 'inProcessOrders'));
    }

    /**
     * Marcar pedido como en proceso (cocina)
     */
    public function markInProcess(Order $order)
    {
        $user = Auth::user();

        if (!$user->roles->contains('nombre', 'Cocinero')) {
            return response()->json(['error' => 'Sin permisos'], 403);
        }

        $order->update(['status' => 'in_process']);

        return response()->json(['success' => true]);
    }

    /**
     * Marcar pedido como servido
     */
    public function markServed(Order $order)
    {
        $user = Auth::user();

        if (!$user->roles->contains('nombre', 'Cocinero')) {
            return response()->json(['error' => 'Sin permisos'], 403);
        }

        $order->update(['status' => 'served']);

        return response()->json(['success' => true]);
    }

    /**
     * Mostrar descuentos disponibles
     */
    public function showDiscounts()
    {
        $todayDiscounts = DailyDiscount::getEligibleProducts();
        $upcomingDiscounts = DailyDiscount::where('discount_date', '>', now()->toDateString())
            ->where('is_active', true)
            ->with('product')
            ->take(5)
            ->get();

        return view('discounts.index', compact('todayDiscounts', 'upcomingDiscounts'));
    }

    /**
     * Aplicar descuento a un pedido
     */
    public function applyDiscount(Order $order)
    {
        try {
            $applied = $this->orderService->checkAndApplyDailyDiscounts($order);

            if ($applied) {
                return redirect()->back()
                    ->with('success', 'Descuento aplicado correctamente');
            } else {
                return redirect()->back()
                    ->with('info', 'Este pedido no califica para descuentos del día');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
