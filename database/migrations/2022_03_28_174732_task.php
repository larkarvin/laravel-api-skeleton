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

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('title');
            $table->text('description');
            $table->date('due_date')->nullable();
            $table->enum('priority',['Urgent','High','Normal','Low'])->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->archivedAt();
            $table->softDeletes();
            $table->index('user_id');
            $table->index('priority');
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
