<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('messages', 'product_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->nullable()->index()->after('receiver_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('messages', 'product_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropColumn('product_id');
            });
        }
    }
};
