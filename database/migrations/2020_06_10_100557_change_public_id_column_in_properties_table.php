<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePublicIdColumnInPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->longText('public_id')->change()->nullable();
            $table->longText('video_public_id')->change()->nullable();
            $table->longText('brochure_public_id')->change()->nullable();
            $table->longText('gallery_public_id')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('public_id');
            $table->dropColumn('video_public_id');
            $table->dropColumn('brochure_public_id');
            $table->dropColumn('gallery_public_id');
        });
    }
}
