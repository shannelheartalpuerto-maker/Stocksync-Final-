<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tableName = 'products';

        // Guard: prevent adding scoped unique key when duplicates already exist in the same store.
        $duplicates = DB::table($tableName)
            ->select('admin_id', 'code', DB::raw('COUNT(*) as total'))
            ->groupBy('admin_id', 'code')
            ->having('total', '>', 1)
            ->count();

        if ($duplicates > 0) {
            throw new RuntimeException('Cannot apply barcode unique scope migration: duplicate product codes exist within the same store.');
        }

        // Drop any existing global unique index on code.
        $globalUniqueIndexes = DB::select(
            "
            SELECT s.index_name
            FROM information_schema.statistics s
            WHERE s.table_schema = DATABASE()
              AND s.table_name = ?
              AND s.non_unique = 0
            GROUP BY s.index_name
            HAVING COUNT(*) = 1
               AND MAX(s.column_name) = 'code'
            ",
            [$tableName]
        );

        foreach ($globalUniqueIndexes as $index) {
            $indexName = str_replace('`', '``', $index->index_name);
            DB::statement("ALTER TABLE `{$tableName}` DROP INDEX `{$indexName}`");
        }

        // Add scoped unique index (admin_id + code) if missing.
        $compositeExists = DB::selectOne(
            "
            SELECT 1
            FROM information_schema.statistics s
            WHERE s.table_schema = DATABASE()
              AND s.table_name = ?
              AND s.index_name = 'products_admin_id_code_unique'
            LIMIT 1
            ",
            [$tableName]
        );

        if (!$compositeExists) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unique(['admin_id', 'code'], 'products_admin_id_code_unique');
            });
        }
    }

    public function down()
    {
        $tableName = 'products';

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropUnique('products_admin_id_code_unique');
        });

        // Keep code searchable if rollback happens.
        $codeIndexExists = DB::selectOne(
            "
            SELECT 1
            FROM information_schema.statistics s
            WHERE s.table_schema = DATABASE()
              AND s.table_name = ?
              AND s.index_name = 'products_code_index'
            LIMIT 1
            ",
            [$tableName]
        );

        if (!$codeIndexExists) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->index('code', 'products_code_index');
            });
        }
    }
};
