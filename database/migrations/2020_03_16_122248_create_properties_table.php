<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('image')->default('https://res.cloudinary.com/altdotng/image/upload/v1584435658/properties/default-property_b95lqm.jpg');
            $table->longText('about');
            $table->string('brochure')->nullable();
            $table->string('location');
            $table->bigInteger('investment_population');
            $table->json('net_rental_yield');
            $table->bigInteger('min_yield');
            $table->bigInteger('max_yield');
            $table->json('holding_period');
            $table->bigInteger('min_fraction_price');
            $table->bigInteger('max_fraction_price');
            $table->integer('category_id');
            $table->json('gallery')->nullable();
            $table->json('facility')->nullable();
            $table->string('video')->nullable();
            $table->string('public_id')->nullable();
            $table->string('video_public_id')->nullable();
            $table->string('brochure_public_id')->nullable();
            $table->string('gallery_public_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
