<h2>Daily Sales Report</h2>

<p>Total Sales: Rp {{ number_format($totalSales) }}</p>
<p>Total Profit: Rp {{ number_format($totalProfit) }}</p>

<hr>

<h4>Transactions:</h4>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Total</th>
        <th>Profit</th>
        <th>Date</th>
    </tr>

    @foreach($transactions as $t)
    <tr>
        <td>{{ $t->id }}</td>
        <td>{{ $t->total }}</td>
        <td>{{ $t->profit }}</td>
        <td>{{ $t->created_at }}</td>
    </tr>
    @endforeach

</table>