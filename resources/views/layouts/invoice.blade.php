<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 30px;
            border-radius: 5px;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .logo-container {
            flex: 1;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .invoice-info {
            flex: 1;
            text-align: right;
        }
        .invoice-title {
            color: #4a4a4a;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .invoice-number {
            font-size: 16px;
            color: #777;
            margin-bottom: 15px;
        }
        .invoice-date {
            font-size: 14px;
            color: #777;
        }
        .client-vendor-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .client-info, .vendor-info {
            flex: 1;
        }
        h3 {
            color: #4a4a4a;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-top: 0;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
            margin-right: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f8f8f8;
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #eee;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .items-table .text-right {
            text-align: right;
        }
        .totals {
            margin-left: auto;
            width: 300px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .total-label {
            font-weight: bold;
        }
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #4a4a4a;
            padding-top: 10px;
            margin-top: 5px;
            border-top: 2px solid #eee;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
        }
        .status-pendiente { background-color: #ffc107; }
        .status-procesando { background-color: #17a2b8; }
        .status-enviado { background-color: #007bff; }
        .status-entregado { background-color: #28a745; }
        .status-cancelado { background-color: #dc3545; }
        .invoice-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .btn-container {
            margin-top: 30px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 0 5px;
        }
        .btn-outline {
            background-color: transparent;
            border: 1px solid #007bff;
            color: #007bff;
        }
        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                padding: 0;
            }
            .btn-container {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
            </div>
            <div class="invoice-info">
                <div class="invoice-title">FACTURA</div>
                <div class="invoice-number">#{{ $order->order_number }}</div>
                <div class="invoice-date">Fecha: {{ $order->created_at->format('d/m/Y') }}</div>
                <div class="invoice-date">
                    Estado: 
                    <span class="status-badge status-{{ $order->status }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="client-vendor-info">
            <div class="client-info">
                <h3>Cliente</h3>
                <div class="info-row">
                    <span class="label">Nombre:</span> {{ $order->client->name }}
                </div>
                <div class="info-row">
                    <span class="label">Email:</span> {{ $order->client->email }}
                </div>
                @if ($order->address)
                <div class="info-row">
                    <span class="label">Dirección:</span> {{ $order->address }}
                </div>
                @endif
                @if ($order->client->phone)
                <div class="info-row">
                    <span class="label">Teléfono:</span> {{ $order->client->phone }}
                </div>
                @endif
            </div>
            <div class="vendor-info">
                <h3>Vendedor</h3>
                <div class="info-row">
                    <span class="label">Nombre:</span> {{ $order->vendor->name }}
                </div>
                <div class="info-row">
                    <span class="label">Tienda:</span> {{ $order->vendor->stand->name ?? 'N/A' }}
                </div>
                <div class="info-row">
                    <span class="label">Email:</span> {{ $order->vendor->email }}
                </div>
                @if ($order->vendor->phone)
                <div class="info-row">
                    <span class="label">Teléfono:</span> {{ $order->vendor->phone }}
                </div>
                @endif
            </div>
        </div>

        <h3>Detalle de productos</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Descripción</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Precio</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->product->description }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }}€</td>
                    <td class="text-right">{{ number_format($item->quantity * $item->price, 2) }}€</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <div class="total-label">Subtotal:</div>
                <div>{{ number_format($order->subtotal, 2) }}€</div>
            </div>
            @if ($order->discount > 0)
            <div class="totals-row">
                <div class="total-label">Descuento:</div>
                <div>-{{ number_format($order->discount, 2) }}€</div>
            </div>
            @endif
            <div class="totals-row">
                <div class="total-label">IVA ({{ $order->tax_rate ?? 21 }}%):</div>
                <div>{{ number_format($order->tax_amount, 2) }}€</div>
            </div>
            @if ($order->shipping_cost > 0)
            <div class="totals-row">
                <div class="total-label">Envío:</div>
                <div>{{ number_format($order->shipping_cost, 2) }}€</div>
            </div>
            @endif
            <div class="totals-row grand-total">
                <div class="total-label">TOTAL:</div>
                <div>{{ number_format($order->total, 2) }}€</div>
            </div>
        </div>

        @if ($order->notes)
        <div style="margin-top: 20px;">
            <h3>Notas</h3>
            <p>{{ $order->notes }}</p>
        </div>
        @endif

        <div class="invoice-footer">
            <p>Gracias por su compra. Para cualquier consulta relacionada con esta factura, por favor contacte a {{ $order->vendor->name }} en {{ $order->vendor->email }}.</p>
            <p>Esta factura ha sido generada automáticamente y es válida sin firma ni sello.</p>
        </div>

        <div class="btn-container">
            <button onclick="window.print()" class="btn">Imprimir factura</button>
            <a href="{{ route('orders.show', $order->order_number) }}" class="btn btn-outline">Volver a detalles</a>
        </div>
    </div>
</body>
</html>