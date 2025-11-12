<table border="1">
    <thead style="font-weight: bold; background-color: #f2f2f2;">
        <tr>
            <th>No</th>
            <th>Transaction Code</th>
            <th>Supplier</th>
            <th>Contact</th>
            <th>Total Items</th>
            <th>Total Price</th>
            <th>Officer</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $index => $t)
            {{-- Main Transaction Row --}}
            <tr style="background-color: #eaf1fb;">
                <td>{{ $index + 1 }}</td>
                <td>{{ $t->transaction_code }}</td>
                <td>{{ $t->supplier }}</td>
                <td>{{ $t->contact }}</td>
                <td>{{ $t->total_items }}</td>
                <td>Rp {{ number_format($t->total_price, 0, ',', '.') }}</td>
                <td>{{ $t->officer }}</td>
                <td>{{ $t->created_at->format('d M Y H:i') }}</td>
            </tr>

            @if($withItems && isset($t->items) && count($t->items) > 0)
                <tr>
                    <th></th>
                    <th>Variant</th>
                    <th>Batch</th>
                    <th colspan="2">Quantity</th>
                    <th>Price</th>
                    <th colspan="2">Subtotal</th>
                </tr>
                @foreach ($t->items as $i => $item)
                    <tr>
                        <td></td>
                        <td>{{ $item->variant_name_snapshot }}</td>
                        <td>{{ $item->batch_number ?? '-' }}</td>
                        <td colspan="2">{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td colspan="2">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endif

            <tr><td colspan="8"></td></tr>
        @endforeach
    </tbody>
</table>
