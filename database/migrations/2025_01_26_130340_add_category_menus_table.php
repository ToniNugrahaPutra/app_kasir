<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->renameColumn('category', 'category_id')->default(0);
            $table->renameColumn('price', 'retail_price');
            $table->decimal('wholesale_price', 10, 0)->default(0)->after('price');
            $table->decimal('member_price', 10, 0)->default(0)->after('wholesale_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->renameColumn('category_id', 'category');
            $table->renameColumn('retail_price', 'price');
            $table->dropColumn('wholesale_price');
            $table->dropColumn('member_price');
        });
    }
};
