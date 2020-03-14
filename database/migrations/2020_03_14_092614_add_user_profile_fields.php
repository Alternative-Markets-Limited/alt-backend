<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserProfileFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->default('https://res.cloudinary.com/altdotng/image/upload/v1584185412/alt_avatars/default-profile_an4tnd.png');
            $table->string('phone')->nullable();
            $table->date('birthday')->nullable();
            $table->string('bvn')->nullable();
            $table->string('occupation')->nullable();
            $table->text('address')->nullable();
            $table->string('public_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('phone');
            $table->dropColumn('birthday');
            $table->dropColumn('bvn');
            $table->dropColumn('occupation');
            $table->dropColumn('address');
            $table->dropColumn('public_id');
        });
    }
}
