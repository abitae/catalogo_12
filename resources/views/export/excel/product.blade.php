<table>
    <thead>
        <tr>
            <th style="background-color: lightblue;">MARCA</th>
            <th style="background-color: lightblue;">CATEGORIA</th>
            <th style="background-color: lightblue;">LINEA</th>
            <th style="background-color: lightblue;">CODIGO</th>
            <th style="background-color: lightblue;">CODIGO FABRICA</th>
            <th style="background-color: lightblue;">CODIGO FICHA</th>
            <th style="background-color: lightblue;">PRECIO COMPRA</th>
            <th style="background-color: lightblue;">PRECIO VENTA</th>
            <th style="background-color: lightblue;">STOCK</th>
            <th style="background-color: lightblue;">DESCRIPCION</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{ $product->brand->name }}</td>
                <td width='20'>{{ $product->category->name }}</td>
                <td width='20'>{{ $product->line->name }}</td>
                <td width='20'>{{ $product->code }}</td>
                <td width='20'>{{ $product->code_fabrica }}</td>
                <td width='20'>{{ $product->code_peru }}</td>
                <td width='20'>{{ $product->price_compra }}</td>
                <td width='20'>{{ $product->price_venta }}</td>
                <td width='20'>{{ $product->stock }}</td>
                <td width='100'>{{ $product->description }}</td>
            </tr>
        @endforeach
    </tbody>
</table>