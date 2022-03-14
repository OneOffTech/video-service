<?php

use App\Models\User;
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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            
            $table->efficientUuid('uuid')->index();

            $table->foreignIdFor(User::class);

            $table->string('title');
            
            $table->text('description')->nullable();
            
            $table->string('language', 4)->nullable();
            
            $table->json('tags')->nullable();

            $table->string('license')->nullable();

            $table->timestamps();
            
            $table->dateTime('published_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
};
