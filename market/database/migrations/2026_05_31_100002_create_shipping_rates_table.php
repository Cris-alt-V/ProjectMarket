<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('carrier');
            $table->decimal('base_cost', 10, 2)->default(0);
            $table->decimal('cost_per_km', 10, 2)->default(0);
            $table->decimal('cost_per_kg', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_rates');
    }
};
