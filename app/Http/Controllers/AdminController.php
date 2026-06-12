<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyReportMail;
use App\Models\Setting;
use App\Models\User;
use Carbon\CarbonPeriod;
use App\Models\TransactionItem;
class AdminController extends Controller
{
    public function dashboard(Request $request)
{
    // DATE FILTER
    $startDate = $request->start_date
        ?? Carbon::now()->startOfMonth()->toDateString();

    $endDate = $request->end_date
        ?? Carbon::now()->toDateString();

    // BASE QUERY
    $query = Transaction::whereBetween('created_at', [
        $startDate . ' 00:00:00',
        $endDate . ' 23:59:59'
    ]);

    // CHART DATA
    $labels = [];
    $data = [];

    $period = CarbonPeriod::create($startDate, $endDate);

    foreach ($period as $date) {

        $labels[] = $date->format('d M');

        $data[] = Transaction::whereDate('created_at', $date)
            ->sum('total');
    }

    // TOTAL SALES
    $totalSales = (clone $query)->sum('total');

    // TOTAL PROFIT
    $totalProfit = (clone $query)->sum('profit');

    // TOTAL TRANSACTIONS
    $totalTransactions = (clone $query)->count();

    // TODAY PROFIT
    $todayProfit = Transaction::whereDate(
        'created_at',
        Carbon::today()
    )->sum('profit');

    // YESTERDAY PROFIT
    $yesterdayProfit = Transaction::whereDate(
        'created_at',
        Carbon::yesterday()
    )->sum('profit');

    // PROFIT CHANGE %
    if ($yesterdayProfit > 0) {

        $percentChange =
            (($todayProfit - $yesterdayProfit)
            / $yesterdayProfit) * 100;

    } else {

        $percentChange = $todayProfit > 0 ? 100 : 0;
    }

    // RECENT TRANSACTIONS
    $recentTransactions = Transaction::whereBetween(
            'created_at',
            [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]
        )
        ->latest()
        ->take(10)
        ->get();

    // PRODUCTS
    $products = Product::all();

    // TOP SELLING PRODUCTS
    $topProducts = TransactionItem::select('product_id')
        ->selectRaw('SUM(quantity) as total_sold')
        ->whereHas('transaction', function ($q) use (
            $startDate,
            $endDate
        ) {
            $q->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        })
        ->groupBy('product_id')
        ->with('product')
        ->orderByDesc('total_sold')
        ->take(5)
        ->get();

    // TOP PROFIT PRODUCTS
    $topProfitProducts = TransactionItem::select('product_id')
        ->selectRaw(
            'SUM((price - buy_price) * quantity) as total_profit'
        )
        ->whereHas('transaction', function ($q) use (
            $startDate,
            $endDate
        ) {
            $q->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        })
        ->groupBy('product_id')
        ->with('product')
        ->orderByDesc('total_profit')
        ->take(5)
        ->get();

    // NEWEST ACCOUNTS
    $newestAccounts = User::latest()
        ->take(5)
        ->get();

    return view('admin.dashboard', compact(
        'labels',
        'data',
        'totalSales',
        'totalProfit',
        'totalTransactions',
        'todayProfit',
        'yesterdayProfit',
        'percentChange',
        'recentTransactions',
        'products',
        'topProducts',
        'topProfitProducts',
        'newestAccounts',
        'startDate',
        'endDate'
    ));
}
    public function toggleReport(Request $request)
{
    $setting = \App\Models\Setting::first();

    if (!$setting) {
        $setting = new \App\Models\Setting();
    }

    $setting->report_enabled = $request->has('report_enabled'); // ✅ checkbox
    $setting->report_email = $request->email;
    $setting->save();

    return back()->with('success', 'Report setting updated!');
}

public function sendReportNow()
{
    $setting = Setting::first();

    if (!$setting || !$setting->report_email) {
        return back()->with('error', 'No report email set!');
    }

    Mail::to($setting->report_email)
        ->send(new DailyReportMail());

    return back()->with('success', 'Report sent successfully!');
}

public function analytics(Request $request)
{
    // DEFAULT RANGE
    $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
    $endDate = $request->end_date ?? now()->toDateString();

    // GET TRANSACTIONS
    $transactions = Transaction::whereBetween('created_at', [
    $startDate . ' 00:00:00',
    $endDate . ' 23:59:59'
])->latest()->get();
    // GRAPH DATA
    $labels = [];
    $salesData = [];
    $profitData = [];

    $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

    foreach($period as $date){

    $labels[] = $date->format('d M');

    $salesData[] = Transaction::whereDate(
        'created_at',
        $date->toDateString()
    )->sum('total');

    $profitData[] = Transaction::whereDate(
        'created_at',
        $date->toDateString()
    )->sum('profit');
}

    // SUMMARY
    $totalSales = $transactions->sum('total');

    $totalProfit = $transactions->sum('profit');

    $totalTransactions = $transactions->count();

    return view('admin.analytics', compact(
        'labels',
        'totalSales',
        'totalProfit',
        'totalTransactions',
        'startDate',
        'endDate',
        'transactions',
        'salesData',
        'profitData'
    ));
}

public function accounts()
{
    $users = User::latest()->paginate(10);

    return view('admin.accounts', compact('users'));
}

public function storeAccount(Request $request)
{
    $request->validate([

        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
        'role' => 'required',
    ]);

    User::create([

        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'role' => $request->role,
        'password' => bcrypt($request->password)
    ]);

    return back()->with('success', 'Account created');
}
}