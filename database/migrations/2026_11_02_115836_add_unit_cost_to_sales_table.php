<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUnitCostToSalesTable extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('unit_cost', 10, 2)->default(0)->after('quantity');
        });

        // fill in old sales using the current purchase cost
        DB::statement('
            UPDATE sales s
            JOIN products p   ON p.id  = s.product_id
            JOIN purchases pu ON pu.id = p.purchase_id
            SET s.unit_cost = pu.cost_price
        ');
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('unit_cost');
        });
    }
}