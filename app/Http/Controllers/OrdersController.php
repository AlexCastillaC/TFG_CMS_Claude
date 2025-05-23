<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrdersController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // Filtrar por estado, método de pago, o estado de pago
        $status = $request->input('status');
        $payment_method = $request->input('payment_method');
        $payment_status = $request->input('payment_status');
        
        $orders = Order::query();
        
        // Si es cliente, mostrar solo sus pedidos
        if ($user->role == 'cliente') {
            $orders->where('client_id', $user->id);
        }
        
        // Si es vendedor, mostrar solo los pedidos asignados a él
        if ($user->role == 'vendedor') {
            $orders->where('vendor_id', $user->id)->orWhere('client_id', $user->id);
        }

        if ($user->role == 'proveedor') {
            $orders->where('vendor_id', $user->id);
        }
        
        // Aplicar filtros si existen
        if ($status) {
            $orders->where('status', $status);
        }
        
        if ($payment_method) {
            $orders->where('payment_method', $payment_method);
        }
        
        if ($payment_status) {
            $orders->where('payment_status', $payment_status);
        }
        
        // Ordenar del más reciente al más antiguo
        $orders = $orders->orderBy('created_at', 'desc')->paginate(10);
        
        return view('orders.index', [
            'clientOrders' => Order::where('client_id', auth()->id())->latest()->paginate(10),
            'vendorOrders' => Order::where('vendor_id', auth()->id())->latest()->paginate(10)
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(string $orderNumber): View
    {
        $user = Auth::user();
        $order = Order::with(['items.product'])->where('order_number', $orderNumber)->firstOrFail();
        
        // Verificar que el usuario tenga permiso para ver esta orden
        if ($user->role == 'cliente' && $order->client_id != $user->id) {
            abort(403, 'No tienes permiso para ver este pedido.');
        }
        
        if ($user->role == 'vendedor' && $order->vendor_id != $user->id) {
            abort(403, 'No tienes permiso para ver este pedido.');
        }
        
        return view('orders.show', compact('order'));
    }
    
    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $user = Auth::user();
        
        // Verificar que sea un vendedor y que tenga permiso para actualizar esta orden
        if (!$user->role == 'vendedor' || $order->vendor_id != $user->id) {
            abort(403, 'No tienes permiso para actualizar este pedido.');
        }
        
        $request->validate([
            'status' => 'required|in:pendiente,procesando,enviado,entregado,cancelado',
        ]);
        
        $order->status = $request->status;
        $order->save();
        
        return redirect()->back()->with('success', 'Estado del pedido actualizado correctamente.');
    }
    
    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $user = Auth::user();
        
        // Verificar que sea un vendedor y que tenga permiso para actualizar esta orden
        if (!$user->role == 'vendedor' || $order->vendor_id != $user->id) {
            abort(403, 'No tienes permiso para actualizar este pedido.');
        }
        
        $request->validate([
            'payment_status' => 'required|in:pendiente,pagado,reembolsado',
        ]);
        
        $order->payment_status = $request->payment_status;
        $order->save();
        
        return redirect()->back()->with('success', 'Estado de pago actualizado correctamente.');
    }
    
    /**
     * Cancel order.
     */
    public function cancel(Order $order): RedirectResponse
    {
        $user = Auth::user();
        
        // Verificar que sea el cliente dueño del pedido
        if (!$user->role == 'cliente' || $order->client_id != $user->id) {
            abort(403, 'No tienes permiso para cancelar este pedido.');
        }
        
        // Solo permitir cancelar pedidos que estén en estado pendiente o procesando
        if (!in_array($order->status, ['pendiente', 'procesando'])) {
            return redirect()->back()->with('error', 'No es posible cancelar pedidos que ya han sido enviados o entregados.');
        }
        
        $order->status = 'cancelado';
        $order->save();
        
        // Restaurar el stock de los productos
        foreach ($order->items as $item) {
            $product = $item->product;
            $product->stock += $item->quantity;
            $product->save();
        }
        
        return redirect()->back()->with('success', 'Pedido cancelado correctamente.');
    }
    
    /**
     * Generate invoice.
     */
    public function invoice(string $orderNumber): View
    {
        $user = Auth::user();
        $order = Order::with(['items.product'])->where('order_number', $orderNumber)->firstOrFail();
        
        // Verificar que el usuario tenga permiso para ver esta factura
        if ($user->role == 'cliente' && $order->client_id != $user->id) {
            abort(403, 'No tienes permiso para ver esta factura.');
        }
        
        if ($user->role == 'vendedor' && $order->vendor_id != $user->id) {
            abort(403, 'No tienes permiso para ver esta factura.');
        }
        
        return view('orders.invoice', compact('order'));
    }
    
    /**
     * Admin: Assign vendor to order.
     */
    public function assignVendor(Request $request, Order $order): RedirectResponse
    {
        $user = Auth::user();
        
        // Verificar que sea un administrador
        if (!$user->hasRole('admin')) {
            abort(403, 'No tienes permiso para asignar vendedores a pedidos.');
        }
        
        $request->validate([
            'vendor_id' => 'required|exists:users,id',
        ]);
        
        // Verificar que el usuario seleccionado sea un vendedor
        $vendor = User::findOrFail($request->vendor_id);
        if (!$vendor->hasRole('vendedor')) {
            return redirect()->back()->with('error', 'El usuario seleccionado no es un vendedor.');
        }
        
        $order->vendor_id = $request->vendor_id;
        $order->save();
        
        return redirect()->back()->with('success', 'Vendedor asignado correctamente.');
    }
    
    /**
     * Admin: View all orders.
     */
    public function adminIndex(Request $request): View
    {
        $user = Auth::user();
        
        // Verificar que sea un administrador
        if (!$user->hasRole('admin')) {
            abort(403, 'No tienes permiso para ver todos los pedidos.');
        }
        
        // Filtrar por estado, método de pago, estado de pago, vendedor o cliente
        $status = $request->input('status');
        $payment_method = $request->input('payment_method');
        $payment_status = $request->input('payment_status');
        $vendor_id = $request->input('vendor_id');
        $client_id = $request->input('client_id');
        
        $orders = Order::query();
        
        // Aplicar filtros si existen
        if ($status) {
            $orders->where('status', $status);
        }
        
        if ($payment_method) {
            $orders->where('payment_method', $payment_method);
        }
        
        if ($payment_status) {
            $orders->where('payment_status', $payment_status);
        }
        
        if ($vendor_id) {
            $orders->where('vendor_id', $vendor_id);
        }
        
        if ($client_id) {
            $orders->where('client_id', $client_id);
        }
        
        // Incluir relaciones para mostrar los nombres de vendedor y cliente
        $orders = $orders->with(['client', 'vendor'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);
        
        // Obtener lista de vendedores y clientes para los filtros
        $vendors = User::role('vendedor')->get();
        $clients = User::role('cliente')->get();
        
        return view('orders.admin.index', compact(
            'orders', 
            'status', 
            'payment_method', 
            'payment_status', 
            'vendor_id', 
            'client_id', 
            'vendors', 
            'clients'
        ));
    }
}