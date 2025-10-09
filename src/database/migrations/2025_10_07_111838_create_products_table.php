<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 出品者
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price');
            $table->string('brand_name')->nullable();
            $table->string('image_url')->nullable();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->string('condition')->nullable();
            $table->string('status')->default('on_sale'); // on_sale / sold
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
