<?php

use App\Models\Video;
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
        Schema::create('blobs', function (Blueprint $table) {
            $table->id();
            
            $table->efficientUuid('uuid')->index();

            $table->foreignIdFor(Video::class)->nullable();

            $table->string('disk');

            $table->string('conversions_disk');

            $table->string('name');

            $table->string('file_name');

            $table->string('mime_type');

            $table->integer('role')->index();

            $table->unsignedBigInteger('size');
            
            $table->unsignedInteger('width')->nullable();

            $table->unsignedInteger('height')->nullable();

            $table->timestamps();
            
            $table->json('conversions')->nullable();

            $table->json('properties')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blobs');
    }
};
