1.  **Update `StockLogController.php`**:
    *   Add logic to handle a `period` request parameter (`today`, `week`, `month`, `all`).
    *   Determine the `$startDate` based on the period.
    *   Filter the existing pagination queries (`$stockIns`, `$stockOuts`, `$damagedStocks`) using this start date.
    *   Calculate summary statistics for the selected period:
        *   `totalStockInQty`: Sum of `quantity` from `StockIn`.
        *   `totalStockOutQty`: Sum of `quantity` from `StockOut`.
        *   `totalDamagedQty`: Sum of `quantity` from `DamagedStock`.
        *   `topAction`: A simple insight, e.g., "Most frequent action: Sale".
    *   Pass these variables and the `period` to the view.

2.  **Update `resources/views/admin/stock_logs/index.blade.php`**:
    *   Add a **Filter Section** at the top with buttons: "Today", "This Week", "This Month", "All Time".
    *   Add a **Summary Dashboard** section (row of 3-4 cards) displaying the calculated stats (`totalStockInQty`, etc.) with the new gradient design.
    *   Ensure the "Stock In", "Stock Out", and "Damaged" lists reflect the filtered data.
    *   Update pagination links to append the `period` parameter so filters persist across pages.