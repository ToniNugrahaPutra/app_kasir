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
            $table->integer('category_id')->default(0)->after('id');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->decimal('retail_price', 10, 0)->default(0)->after('category_id');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->decimal('wholesale_price', 10, 0)->default(0)->after('retail_price');
            $table->decimal('member_price', 10, 0)->default(0)->after('wholesale_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->integer('category')->default(0)->after('id');
            $table->decimal('price', 10, 0)->default(0)->after('category');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('category_id');
            $table->dropColumn('retail_price');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('wholesale_price');
            $table->dropColumn('member_price');
        });
    }
};
