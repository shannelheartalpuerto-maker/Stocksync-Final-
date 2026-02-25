<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\DamagedStock;

class StockLogController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'week');
        $startDate = null;

        switch ($period) {
            case 'today':
                $startDate = now()->startOfDay();
                break;
            case 'week':
                $startDate = now()->startOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                break;
            case 'all':
                $startDate = null;
                break;
            default:
                $startDate = now()->startOfWeek();
                $period = 'week'; // Reset to valid default
                break;
        }

        // Base Queries
        $baseStockInQuery = StockIn::where('admin_id', $this->getAdminId())->with(['product', 'user']);
        $stockOutQuery = StockOut::where('admin_id', $this->getAdminId())->with(['product', 'user']);
        $damagedStockQuery = DamagedStock::where('admin_id', $this->getAdminId())->with(['product', 'user']);

        // Apply Date Filter
        if ($startDate) {
            $baseStockInQuery->where('created_at', '>=', $startDate);
            $stockOutQuery->where('created_at', '>=', $startDate);
            $damagedStockQuery->where('created_at', '>=', $startDate);
        }

        // Separate Returned from Regular Stock In
        $returnedStockQuery = (clone $baseStockInQuery)->where('notes', 'like', 'Return of Transaction%');
        $stockInQuery = (clone $baseStockInQuery)->where(function($q) {
            $q->whereNull('notes')
              ->orWhere('notes', 'not like', 'Return of Transaction%');
        });

        // Calculate Summaries
        $totalStockIn = (clone $stockInQuery)->sum('quantity');
        $totalReturned = (clone $returnedStockQuery)->sum('quantity');
        $totalStockOut = (clone $stockOutQuery)->sum('quantity');
        $totalDamaged = (clone $damagedStockQuery)->sum('quantity');

        // Get Paginated Results
        $stockIns = $stockInQuery->latest()->paginate(20, ['*'], 'in_page')->appends(['period' => $period, 'type' => 'stockin']);
        $returnedStocks = $returnedStockQuery->latest()->paginate(20, ['*'], 'returned_page')->appends(['period' => $period, 'type' => 'returned']);
        $stockOuts = $stockOutQuery->latest()->paginate(20, ['*'], 'out_page')->appends(['period' => $period, 'type' => 'stockout']);
        $damagedStocks = $damagedStockQuery->latest()->paginate(20, ['*'], 'damaged_page')->appends(['period' => $period, 'type' => 'damaged']);
            
        if ($request->ajax()) {
            $type = $request->input('type');
            // Pass all variables needed for pagination links in partials
            $viewData = compact('stockIns', 'returnedStocks', 'stockOuts', 'damagedStocks', 'period');
            
            if ($type === 'stockin') {
                return view('admin.partials.logs_stockin', $viewData)->render();
            } elseif ($type === 'returned') {
                return view('admin.partials.logs_returned', $viewData)->render();
            } elseif ($type === 'stockout') {
                return view('admin.partials.logs_stockout', $viewData)->render();
            } elseif ($type === 'damaged') {
                return view('admin.partials.logs_damaged', $viewData)->render();
            } else {
                // Return JSON payload for period filter updates
                $stockInHtml = view('admin.partials.logs_stockin', $viewData)->render();
                $returnedHtml = view('admin.partials.logs_returned', $viewData)->render();
                $stockOutHtml = view('admin.partials.logs_stockout', $viewData)->render();
                $damagedHtml = view('admin.partials.logs_damaged', $viewData)->render();
                return response()->json([
                    'period' => $period,
                    'summaries' => [
                        'stock_in' => (int) $totalStockIn,
                        'returned' => (int) $totalReturned,
                        'stock_out' => (int) $totalStockOut,
                        'damaged' => (int) $totalDamaged,
                    ],
                    'tabs' => [
                        'stockin_html' => $stockInHtml,
                        'returned_html' => $returnedHtml,
                        'stockout_html' => $stockOutHtml,
                        'damaged_html' => $damagedHtml,
                    ],
                ]);
            }
        }

        return view('admin.stock_logs.index', compact(
            'stockIns', 'returnedStocks', 'stockOuts', 'damagedStocks', 
            'totalStockIn', 'totalReturned', 'totalStockOut', 'totalDamaged', 'period'
        ));
    }
}
