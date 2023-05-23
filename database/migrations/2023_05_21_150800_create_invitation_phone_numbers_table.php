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
        Schema::create('invitation_phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('invitation_id')->constrained('invitations')->onUpdate('cascade')->onDelete('cascade');
            $table->string('phone_number');
            $table->tinyInteger('is_sent')->nullable()->default(0);
            $table->string('sent_by')->nullable();
            $table->dateTime('sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invitation_phone_numbers');
    }
};
