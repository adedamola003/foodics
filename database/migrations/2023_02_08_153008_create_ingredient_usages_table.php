<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingredient_usages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_item_id')->unsigned()->nullable();
            $table->integer('ingredient_id')->unsigned();
            $table->double('quantity', 10, 2);
            $table->double('balance', 10, 2);
            $table->enum('unit', ['g'])->comment('unit is in the lowest possible unit, e.g. grams');
            $table->enum('usage_type', ['0', '1'])->index()->comment('0: Debit, 1: Credit');
            $table->timestamps();

            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            $table->foreign('ingredient_id')->references('id')->on('ingredients')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingredient_usages');
    }
};
