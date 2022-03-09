<?php

use App\Models\Media;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('media', function (Blueprint $table) {
                $table->increments('id');
                $table->string('disk', 32);
                $table->string('path');
                $table->string('filename');
                $table->string('extension', 32);
                $table->string('mime_type', 128);
                $table->integer('size')->unsigned();
                $table->timestamps();

                $table->index('extension');
            }
        );

        Schema::create('mediables', function (Blueprint $table) {
                $table->foreignIdFor(Media::class, 'media_id');
                $table->integer('mediable_id')->unsigned();
                $table->string('mediable_type');

                $table->primary(['media_id', 'mediable_id', 'mediable_type']);
                $table->index(['mediable_id', 'mediable_type']);
            }
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('mediables');
    }
}
