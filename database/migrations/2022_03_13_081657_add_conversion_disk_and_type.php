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
        Schema::table('blobs', function (Blueprint $table) {

            $table->string('conversions_disk')->after('disk');
            
            $table->integer('role')->after('mime_type')->index();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blobs', function (Blueprint $table) {
            //
        });
    }
};
