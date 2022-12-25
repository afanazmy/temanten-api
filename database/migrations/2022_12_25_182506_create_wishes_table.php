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
        Schema::create('wishes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('invitation_id')->constrained('invitations')->onUpdate('cascade')->onDelete('cascade');
            $table->string('group_member_name')->nullable();
            $table->string('wish')->nullable();
            $table->string('rsvp')->nullable();
            $table->string('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wishes');
    }
};
