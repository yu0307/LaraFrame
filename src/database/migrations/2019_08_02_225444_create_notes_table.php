<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lf_notes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject',220)->default('Untitiled');
            $table->text('notes')->nullable();
            $table->string('notable_id', 36)->nullable();
            $table->string('notable_type', 50)->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lf_notes');
    }
}
