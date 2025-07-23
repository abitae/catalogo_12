<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .header { border-bottom: 2px solid #0074D9; margin-bottom: 20px; padding-bottom: 10px; }
        .empresa { font-size: 20px; font-weight: bold; color: #0074D9; }
        .ruc { font-size: 12px; color: #555; }
        .section-title { font-size: 14px; font-weight: bold; margin-top: 20px; margin-bottom: 8px; color: #0074D9; }
        .info-table, .products-table, .totals-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 2px 4px; }
        .products-table th, .products-table td { border: 1px solid #bbb; padding: 5px; }
        .products-table th { background: #f0f4fa; font-size: 12px; }
        .products-table td { font-size: 11px; }
        .totals-table td { padding: 4px 6px; font-size: 12px; }
        .totals-table .label { text-align: right; }
        .totals-table .value { text-align: right; font-weight: bold; }
        .condiciones, .observaciones { border: 1px solid #bbb; border-radius: 4px; padding: 8px; margin-bottom: 10px; font-size: 11px; }
        .footer { border-top: 1px solid #bbb; margin-top: 30px; padding-top: 10px; font-size: 10px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td>
                    @php
                        // Usar storage_path para obtener la ruta absoluta del logo almacenado en storage/app/public
                        $logoRelativePath = $cotizacion->line->logo ?? '';
                        $logoPath = storage_path('app/public/' . ltrim($logoRelativePath, '/'));
                        $logoExists = file_exists($logoPath) && !empty($logoRelativePath);
                    @endphp
                    @if($logoExists)
                        <img src="file://{{ $logoPath }}" alt="Logo" style="width: 100px; height: 100px;">
                    @else
                        <div style="width:100px; height:100px; background:#f0f0f0; display:flex; align-items:center; justify-content:center; color:#bbb; font-size:12px;">
                            Sin logo
                        </div>
                    @endif
                </td>
                <td>
                    <div class="empresa">{{ $cotizacion->line->name }}</div>
                    <div class="ruc">RUC: {{ $cotizacion->customer->ruc }}</div>
                    <div style="font-size:11px; color:#555;">{{ $cotizacion->customer->address }}<br>Tel: {{ $cotizacion->customer->phone }} | {{ $cotizacion->customer->email }}</div>
                </td>
                <td style="text-align:right;">
                    <div style="font-size:18px; color:#0074D9; font-weight:bold;">COTIZACIÓN</div>
                    <div style="font-size:13px;">N° <b>{{ $cotizacion->codigo_cotizacion }}</b></div>
                    <div style="font-size:11px;">Fecha: {{ \Carbon\Carbon::parse($cotizacion->fecha_cotizacion)->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Datos del Cliente</div>
    <table class="info-table">
        <tr>
            <td><b>Cliente:</b></td>
            <td>{{ $cotizacion->customer->rznSocial ?? $cotizacion->cliente_nombre }}</td>
            <td><b>Contacto:</b></td>
            <td>{{ $cotizacion->cliente_nombre }}</td>
        </tr>
        <tr>
            <td><b>Email:</b></td>
            <td>{{ $cotizacion->cliente_email }}</td>
            <td><b>Teléfono:</b></td>
            <td>{{ $cotizacion->cliente_telefono }}</td>
        </tr>
        <tr>
            <td><b>Vendedor:</b></td>
            <td>{{ $cotizacion->user->name ?? '' }}</td>
            <td><b>Validez:</b></td>
            <td>{{ $cotizacion->validez_dias }} días</td>
        </tr>
        <tr>
            <td><b>Vence:</b></td>
            <td>{{ $cotizacion->fecha_vencimiento ? \Carbon\Carbon::parse($cotizacion->fecha_vencimiento)->format('d/m/Y') : '' }}</td>
            <td><b>Estado:</b></td>
            <td>{{ ucfirst($cotizacion->estado) }}</td>
        </tr>
    </table>

    <div class="section-title">Productos Cotizados</div>
    <table class="products-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cotizacion->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->code ?? '' }}</td>
                    <td>{{ $detalle->producto->description ?? '' }}</td>
                    <td style="text-align:center;">{{ $detalle->cantidad }}</td>
                    <td style="text-align:right;">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td style="text-align:right;">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table" style="margin-top:10px;">
        <tr>
            <td class="label">Subtotal (sin IGV):</td>
            <td class="value">S/ {{ number_format($cotizacion->subtotal ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td class="label">IGV (18%):</td>
            <td class="value">S/ {{ number_format($cotizacion->getAttribute('igv') ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td class="label"><b>TOTAL:</b></td>
            <td class="value" style="font-size:15px; color:#0074D9;">S/ {{ number_format($cotizacion->total ?? 0, 2) }}</td>
        </tr>
    </table>

    <div class="section-title">Condiciones Comerciales</div>
    <div class="condiciones">
        <b>Condiciones de Pago:</b> {{ $cotizacion->condiciones_pago ?? 'No especificado' }}<br>
        <b>Condiciones de Entrega:</b> {{ $cotizacion->condiciones_entrega ?? 'No especificado' }}
    </div>

    <div class="section-title">Observaciones Generales</div>
    <div class="observaciones">
        {{ $cotizacion->observaciones ?? 'Sin observaciones adicionales' }}
    </div>

    <div class="footer">
        Documento generado automáticamente - EMPRESA S.A.C. | {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
