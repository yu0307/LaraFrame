<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lf_mail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sender');
            $table->integer('recipient');
            $table->string('subject', 220)->default('Untitiled');
            $table->text('contents')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('lf_mail');
    }
}
