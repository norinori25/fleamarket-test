<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 出品者
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price');
            $table->string('brand_name')->nullable();
            $table->string('image_url')->nullable();
            $table->string('condition')->nullable();
            $table->string('status')->default('on_sale'); // on_sale / sold
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
}
