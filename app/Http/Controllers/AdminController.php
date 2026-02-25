<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $adminId = $this->getAdminId();
        $period = request('period', 'all_time');
        
        $startDate = match($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => null, // All time
        };

        // Stat Cards
        $totalProducts = Product::where('admin_id', $adminId)->count();
        $totalStockCount = Product::where('admin_id', $adminId)->sum('quantity');
        $totalStockValue = Product::where('admin_id', $adminId)->sum(DB::raw('price * quantity'));
            
        // Filtered Stats
        $transactionQuery = Transaction::where('admin_id', $adminId);
        if ($startDate) {
            $transactionQuery->where('created_at', '>=', $startDate);
        }
        $totalRevenue = $transactionQuery->sum('total_amount');
        
        // Extended Stats
        $transactionQuery = Transaction::where('admin_id', $adminId); // New query instance
        if ($startDate) {
            $transactionQuery->where('created_at', '>=', $startDate);
        }
        $totalTransactions = $transactionQuery->count();
        $averageOrderValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
        
        // Keep this month specific stat for context, or maybe remove if redundant with filter? 
        // Let's keep it but maybe rename in view if needed.
        $thisMonthRevenue = Transaction::where('admin_id', $adminId)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        // Recent Activity
        $transactionStatus = request('transaction_status', 'all');
        $recentTransactionsQuery = Transaction::where('admin_id', $adminId)->with('user');

        if ($transactionStatus != 'all') {
            $recentTransactionsQuery->where('status', $transactionStatus);
        }

        $recentTransactions = $recentTransactionsQuery->latest()
                                         ->take(5)
                                         ->get();

        // Recent Stock Logs
        $recentLogs = StockLog::where('admin_id', $adminId)
            ->with(['product', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Chart Data: Revenue Last 7 Days (Or Filtered?)
        // If filter is active, maybe show relevant chart? For now, keep last 7 days as a trend indicator.
        $revenueData = Transaction::where('admin_id', $adminId)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $dates = [];
        $revenues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;
            $dayRevenue = $revenueData->firstWhere('date', $date);
            $revenues[] = $dayRevenue ? $dayRevenue->total : 0;
        }

        // Chart Data: Top 5 Selling Products (Pie Chart or Bar) - Filtered
        $topProductsQuery = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id') // Join transactions to filter by date
            ->where('products.admin_id', $adminId);

        if ($startDate) {
            $topProductsQuery->where('transactions.created_at', '>=', $startDate);
        }

        $topProducts = $topProductsQuery->select('products.name', DB::raw('SUM(transaction_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
        
        $productNames = $topProducts->pluck('name');
        $productSales = $topProducts->pluck('total_sold');

        // Forecast Data
        $totalForecastQty = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.admin_id', $adminId)
            ->where('transactions.created_at', '>=', now()->subDays(30))
            ->sum('transaction_items.quantity');

        // Forecast & Suggestions - Enhanced
        // Logic:
        // 1. Determine Analysis Window based on Period
        // 2. Calculate Sales Velocity (Avg Daily Sales)
        // 3. Segment into Fast, Stable, Slow based on relative performance
        
        $forecastStartDate = match($period) {
            'today' => now()->startOfDay(),
            'week' => now()->subDays(7),
            'month' => now()->subDays(30),
            default => null, // All time
        };

        $daysInPeriod = match($period) {
            'today' => 1,
            'week' => 7,
            'month' => 30,
            default => 0, // Special handling for all time
        };

        $allProductStats = Product::where('admin_id', $adminId)->with('category')->get()->map(function ($product) use ($forecastStartDate, $daysInPeriod) {
            $query = DB::table('transaction_items')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transaction_items.product_id', $product->id);
            
            if ($forecastStartDate) {
                $query->where('transactions.created_at', '>=', $forecastStartDate);
            }

            $soldInPeriod = $query->sum('transaction_items.quantity');
            
            // Calculate Denominator
            if ($daysInPeriod > 0) {
                $divisor = $daysInPeriod;
            } else {
                // For All Time: Days since product created or 1 day min
                $daysExist = $product->created_at->diffInDays(now());
                $divisor = $daysExist > 0 ? $daysExist : 1;
            }

            $avgDailySales = $soldInPeriod / $divisor;
            $daysCoverage = $avgDailySales > 0 ? $product->quantity / $avgDailySales : ($product->quantity > 0 ? 999 : 0);
            
            return [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'category_name' => $product->category->name ?? 'Uncategorized',
                'category_id' => $product->category_id,
                'stock' => $product->quantity,
                'current_low_threshold' => $product->low_stock_threshold,
                'current_good_threshold' => $product->good_stock_threshold,
                'current_overstock_threshold' => $product->overstock_threshold,
                'sold_period' => $soldInPeriod,
                'avg_daily_sales' => $avgDailySales,
                'days_coverage' => $daysCoverage,
            ];
        });

        // Classification Logic - INDIVIDUAL BASED (Turnover Ratio)
        // We classify based on Projected Monthly Turnover:
        // How much of the CURRENT stock would be sold in 30 days at current velocity?
        // Ratio = (AvgDailySales * 30) / (CurrentStock + 1)
        
        $productForecasts = $allProductStats->map(function ($item) {
            
            // 1. Get Long-Term Baseline (Last 30 Days) for Stable Status
            $soldLast30Days = DB::table('transaction_items')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transaction_items.product_id', $item['id'])
                ->where('transactions.created_at', '>=', now()->subDays(30))
                ->sum('transaction_items.quantity');
            
            $avgDailySalesLongTerm = $soldLast30Days / 30;
            $daysCoverageLongTerm = $avgDailySalesLongTerm > 0 ? $item['stock'] / $avgDailySalesLongTerm : ($item['stock'] > 0 ? 999 : 0);

            // 2. Determine Velocity based on SELECTED Period (User's Filter)
            $velocityStatus = 'Stable';
            $velocityClass = 'text-primary';
            $icon = 'fa-minus';

            $projectedMonthlySales = $item['avg_daily_sales'] * 30; // Based on filtered velocity
            
            if ($item['stock'] <= 0) {
                 $turnoverRatio = ($item['avg_daily_sales'] > 0) ? 999 : 0;
            } else {
                $turnoverRatio = $projectedMonthlySales / $item['stock'];
            }

            if ($turnoverRatio >= 0.5) {
                $velocityStatus = 'Fast Moving';
                $velocityClass = 'text-success';
                $icon = 'fa-angles-up';
            } elseif ($turnoverRatio < 0.1) {
                $velocityStatus = 'Slow Moving';
                $velocityClass = 'text-secondary';
                $icon = 'fa-angles-down';
            }

            // New Feature: Stock Action (Stock In / Stock Out)
            // Logic: Target Stock = AvgDaily * 30. Compare with Current Stock.
            
            $targetStock = ceil($item['avg_daily_sales'] * 30);
            $diff = $targetStock - $item['stock'];
            
            $stockAction = 'Maintain';
            $actionQty = 0;
            $actionClass = 'text-success'; // Default Green (Healthy)
            $actionIcon = 'fa-check';

            if ($diff > 0) {
                // Need more stock
                $stockAction = 'Stock In';
                $actionQty = $diff;
                $actionClass = 'text-danger'; // Red (Urgent Buy)
                $actionIcon = 'fa-arrow-trend-up';
            } 
            
            // Removed "Stock Out" logic as per user request. 
            // We only suggest stocking IN. If overstocked, we simply "Maintain".

            // New Feature: Suggested Thresholds (Optimization)
            // Logic: 
            // Low Stock = 7 Days of Sales (Weekly Buffer)
            // Good Stock = 30 Days of Sales (Monthly Target)
            // Over Stock = 60 Days of Sales (2 Months Max)
            // We use Long Term (30d) Average for stability
            
            if ($avgDailySalesLongTerm <= 0) {
                $suggestedLow = 0;
                $suggestedGood = 0;
                $suggestedOver = 0;
            } else {
                // Ensure logical progression: Low < Good < Over
                $baseLow = ceil($avgDailySalesLongTerm * 7);
                $suggestedLow = max(5, $baseLow); 

                $baseGood = ceil($avgDailySalesLongTerm * 30);
                $suggestedGood = max($suggestedLow + 5, $baseGood); // Ensure Good is visibly higher than Low

                $baseOver = ceil($avgDailySalesLongTerm * 60);
                $suggestedOver = max($suggestedGood + 10, max(50, $baseOver)); // Ensure Over is visibly higher than Good
            }

            return array_merge($item, [
                'velocity_status' => $velocityStatus,
                'velocity_class' => $velocityClass,
                'velocity_icon' => $icon,
                'stock_action' => $stockAction,
                'action_qty' => $actionQty,
                'action_class' => $actionClass,
                'action_icon' => $actionIcon,
                'target_stock' => $targetStock,
                'suggested_low_threshold' => $suggestedLow,
                'suggested_good_stock' => $suggestedGood,
                'suggested_overstock_threshold' => $suggestedOver,
                'turnover_ratio' => $turnoverRatio
            ]);
        });

        // Card Stats
        $fastMovingCount = $productForecasts->where('velocity_status', 'Fast Moving')->count();
        $stableMovingCount = $productForecasts->where('velocity_status', 'Stable')->count();
        $slowMovingCount = $productForecasts->where('velocity_status', 'Slow Moving')->count();
        
        // Count items that need restocking (Stock In)
        $restockNeededCount = $productForecasts->where('stock_action', 'Stock In')->count();

        // Top Moving / Most Sold (Filtered)
        $topMovingProduct = $topProducts->first(); 
        $mostSoldProduct = $topMovingProduct;

        // Category Distribution (Keep global for now)
        $categoryStats = \App\Models\Category::where('admin_id', $adminId)
            ->withCount('products')
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts', 'totalStockCount', 'totalStockValue', 'totalRevenue',
            'recentTransactions', 'dates', 'revenues', 'productNames', 'productSales',
            'totalTransactions', 'averageOrderValue', 'thisMonthRevenue', 'recentLogs',
            'totalForecastQty', 'productForecasts',
            'topMovingProduct', 'mostSoldProduct', 'categoryStats', 'period',
            'fastMovingCount', 'stableMovingCount', 'slowMovingCount', 'restockNeededCount'
        ));
    }

    public function salesData(Request $request)
    {
        $adminId = $this->getAdminId();
        $period = $request->get('period', 'all_time');
        $transactionStatus = $request->get('transaction_status', 'all');

        $startDate = match($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => null,
        };

        // Revenue and stats
        $transactionQuery = Transaction::where('admin_id', $adminId);
        if ($startDate) {
            $transactionQuery->where('created_at', '>=', $startDate);
        }
        $totalRevenue = $transactionQuery->sum('total_amount');

        $transactionQuery = Transaction::where('admin_id', $adminId);
        if ($startDate) {
            $transactionQuery->where('created_at', '>=', $startDate);
        }
        $totalTransactions = $transactionQuery->count();
        $averageOrderValue = $totalTransactions > 0 ? round($totalRevenue / $totalTransactions, 2) : 0;

        // Most sold product (filtered)
        $topProductsQuery = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('products.admin_id', $adminId);
        if ($startDate) {
            $topProductsQuery->where('transactions.created_at', '>=', $startDate);
        }
        $topProducts = $topProductsQuery
            ->select('products.name', DB::raw('SUM(transaction_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(1)
            ->get();
        $mostSoldProduct = $topProducts->first();

        // Chart: last 7 days revenue (always based on last 7 days to keep trend)
        $revenueData = Transaction::where('admin_id', $adminId)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        $dates = [];
        $revenues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;
            $dayRevenue = $revenueData->firstWhere('date', $date);
            $revenues[] = $dayRevenue ? (float) $dayRevenue->total : 0;
        }

        // Recent transactions (HTML)
        $recentTransactionsQuery = Transaction::where('admin_id', $adminId)->with('user');
        if ($transactionStatus != 'all') {
            $recentTransactionsQuery->where('status', $transactionStatus);
        }
        $recentTransactions = $recentTransactionsQuery->latest()->take(5)->get();
        $recentHtml = view('admin.partials.recent_transactions_list', compact('recentTransactions'))->render();

        return response()->json([
            'period' => $period,
            'totals' => [
                'revenue' => (float) $totalRevenue,
                'transactions' => $totalTransactions,
                'avg_order' => (float) $averageOrderValue,
                'best_seller' => [
                    'name' => $mostSoldProduct->name ?? 'N/A',
                    'sold' => $mostSoldProduct->total_sold ?? 0,
                ],
            ],
            'chart' => [
                'dates' => $dates,
                'revenues' => $revenues,
            ],
            'recent_html' => $recentHtml,
        ]);
    }

    public function forecastData(Request $request)
    {
        $adminId = $this->getAdminId();
        $period = $request->get('period', 'all_time');

        $forecastStartDate = match($period) {
            'today' => now()->startOfDay(),
            'week' => now()->subDays(7),
            'month' => now()->subDays(30),
            default => null,
        };
        $daysInPeriod = match($period) {
            'today' => 1,
            'week' => 7,
            'month' => 30,
            default => 0,
        };

        $allProductStats = Product::where('admin_id', $adminId)->with('category')->get()->map(function ($product) use ($forecastStartDate, $daysInPeriod) {
            $query = DB::table('transaction_items')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transaction_items.product_id', $product->id);
            
            if ($forecastStartDate) {
                $query->where('transactions.created_at', '>=', $forecastStartDate);
            }

            $soldInPeriod = $query->sum('transaction_items.quantity');
            if ($daysInPeriod > 0) {
                $divisor = $daysInPeriod;
            } else {
                $daysExist = $product->created_at->diffInDays(now());
                $divisor = $daysExist > 0 ? $daysExist : 1;
            }
            $avgDailySales = $soldInPeriod / $divisor;
            $daysCoverage = $avgDailySales > 0 ? $product->quantity / $avgDailySales : ($product->quantity > 0 ? 999 : 0);
            
            return [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'category_name' => $product->category->name ?? 'Uncategorized',
                'stock' => $product->quantity,
                'avg_daily_sales' => $avgDailySales,
                'days_coverage' => $daysCoverage,
            ];
        });

        $productForecasts = $allProductStats->map(function ($item) {
            $soldLast30Days = DB::table('transaction_items')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transaction_items.product_id', $item['id'])
                ->where('transactions.created_at', '>=', now()->subDays(30))
                ->sum('transaction_items.quantity');
            
            $avgDailySalesLongTerm = $soldLast30Days / 30;

            $velocityStatus = 'Stable';
            $velocityClass = 'text-primary';
            $icon = 'fa-minus';
            $projectedMonthlySales = $item['avg_daily_sales'] * 30;
            $turnoverRatio = $item['stock'] <= 0
                ? ($item['avg_daily_sales'] > 0 ? 999 : 0)
                : ($projectedMonthlySales / $item['stock']);
            if ($turnoverRatio >= 0.5) {
                $velocityStatus = 'Fast Moving';
                $velocityClass = 'text-success';
                $icon = 'fa-angles-up';
            } elseif ($turnoverRatio < 0.1) {
                $velocityStatus = 'Slow Moving';
                $velocityClass = 'text-secondary';
                $icon = 'fa-angles-down';
            }

            $targetStock = ceil($item['avg_daily_sales'] * 30);
            $diff = $targetStock - $item['stock'];
            $stockAction = 'Maintain';
            $actionQty = 0;
            $actionClass = 'text-success';
            $actionIcon = 'fa-check';
            if ($diff > 0) {
                $stockAction = 'Stock In';
                $actionQty = $diff;
                $actionClass = 'text-danger';
                $actionIcon = 'fa-arrow-trend-up';
            }

            if ($avgDailySalesLongTerm <= 0) {
                $suggestedLow = 0;
                $suggestedGood = 0;
                $suggestedOver = 0;
            } else {
                $baseLow = ceil($avgDailySalesLongTerm * 7);
                $suggestedLow = max(5, $baseLow); 
                $baseGood = ceil($avgDailySalesLongTerm * 30);
                $suggestedGood = max($suggestedLow + 5, $baseGood);
                $baseOver = ceil($avgDailySalesLongTerm * 60);
                $suggestedOver = max($suggestedGood + 10, max(50, $baseOver));
            }

            return array_merge($item, [
                'velocity_status' => $velocityStatus,
                'velocity_class' => $velocityClass,
                'velocity_icon' => $icon,
                'stock_action' => $stockAction,
                'action_qty' => $actionQty,
                'action_class' => $actionClass,
                'action_icon' => $actionIcon,
                'suggested_low_threshold' => $suggestedLow,
                'suggested_good_stock' => $suggestedGood,
                'suggested_overstock_threshold' => $suggestedOver,
            ]);
        });

        $fastMovingCount = $productForecasts->where('velocity_status', 'Fast Moving')->count();
        $stableMovingCount = $productForecasts->where('velocity_status', 'Stable')->count();
        $slowMovingCount = $productForecasts->where('velocity_status', 'Slow Moving')->count();
        $restockNeededCount = $productForecasts->where('stock_action', 'Stock In')->count();

        $rowsHtml = view('admin.partials.forecast_rows', ['productForecasts' => $productForecasts])->render();

        return response()->json([
            'period' => $period,
            'counts' => [
                'fast' => $fastMovingCount,
                'stable' => $stableMovingCount,
                'slow' => $slowMovingCount,
                'restock' => $restockNeededCount,
            ],
            'rows_html' => $rowsHtml,
        ]);
    }

    public function manageStaff()
    {
        $query = \App\Models\User::where('role', 'staff')
            ->where('id', '!=', auth()->id());

        if (auth()->user()->admin_id === null) {
            // Main Admin: See all linked to this organization
            $query->where('admin_id', $this->getAdminId());
        } else {
            // Co-Admin: See only created by me
            $query->where('created_by', auth()->id());
        }

        $staff = $query->get();
        if (request()->ajax()) {
            $rows = view('admin.staff.partials.rows', compact('staff'))->render();
            $modals = view('admin.staff.partials.modals', compact('staff'))->render();
            return response()->json([
                'rows_html' => $rows,
                'modals_html' => $modals,
            ]);
        }
        return view('admin.staff.index', compact('staff'));
    }

    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'staff',
            'admin_id' => $this->getAdminId(),
            'created_by' => auth()->id(),
            'status' => 'active',
        ]);
        if ($request->ajax()) {
            return $this->manageStaff();
        }
        return redirect()->back()->with('success', 'Staff account created successfully.');
    }

    public function updateStaff(Request $request, $id)
    {
        $query = \App\Models\User::where('admin_id', $this->getAdminId());
        
        if (auth()->user()->admin_id !== null) {
            $query->where('created_by', auth()->id());
        }
        
        $staff = $query->findOrFail($id);

        $request->validate([
            'status' => 'required|in:active,suspended',
        ]);

        $staff->update([
            'status' => $request->status,
        ]);
        if ($request->ajax()) {
            return $this->manageStaff();
        }
        return redirect()->back()->with('success', 'Staff status updated successfully.');
    }

    public function destroyStaff($id)
    {
        $query = \App\Models\User::where('admin_id', $this->getAdminId());
        
        if (auth()->user()->admin_id !== null) {
            $query->where('created_by', auth()->id());
        }
        
        $staff = $query->findOrFail($id);
        $staff->delete();
        if (request()->ajax()) {
            return $this->manageStaff();
        }
        return redirect()->back()->with('success', 'Staff account deleted successfully.');
    }

    // --- Admin Management ---
    public function manageAdmins()
    {
        $query = \App\Models\User::where('role', 'admin')
            ->where('id', '!=', auth()->id());

        if (auth()->user()->admin_id === null) {
            // Main Admin: See all linked to this organization
            $query->where('admin_id', $this->getAdminId());
        } else {
            // Co-Admin: See only created by me
            $query->where('created_by', auth()->id());
        }

        $admins = $query->get();
        if (request()->ajax()) {
            $rows = view('admin.admins.partials.rows', compact('admins'))->render();
            $modals = view('admin.admins.partials.modals', compact('admins'))->render();
            return response()->json([
                'rows_html' => $rows,
                'modals_html' => $modals,
            ]);
        }
        return view('admin.admins.index', compact('admins'));
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'admin',
            'admin_id' => $this->getAdminId(),
            'created_by' => auth()->id(),
            'status' => 'active',
        ]);
        if ($request->ajax()) {
            return $this->manageAdmins();
        }
        return redirect()->back()->with('success', 'Admin account created successfully.');
    }

    public function updateAdmin(Request $request, $id)
    {
        $query = \App\Models\User::where('role', 'admin')
            ->where('admin_id', $this->getAdminId());
            
        if (auth()->user()->admin_id !== null) {
            $query->where('created_by', auth()->id());
        }

        $admin = $query->findOrFail($id);

        $request->validate([
            'status' => 'required|in:active,suspended',
        ]);

        $admin->update([
            'status' => $request->status,
        ]);
        if ($request->ajax()) {
            return $this->manageAdmins();
        }
        return redirect()->back()->with('success', 'Admin status updated successfully.');
    }

    public function destroyAdmin($id)
    {
        $query = \App\Models\User::where('role', 'admin')
            ->where('admin_id', $this->getAdminId());
            
        if (auth()->user()->admin_id !== null) {
            $query->where('created_by', auth()->id());
        }
        
        $admin = $query->findOrFail($id);
            
        $admin->delete();
        if (request()->ajax()) {
            return $this->manageAdmins();
        }
        return redirect()->back()->with('success', 'Admin account deleted successfully.');
    }
}
