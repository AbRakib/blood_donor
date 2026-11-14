<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->unique();
            $table->string('email')->nullable()->unique();
            $table->string('photo')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();

            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();

            $table->string('nid_number')->nullable();
            $table->string('nid_front_image')->nullable();
            $table->string('nid_back_image')->nullable();

            $table->string('license_number')->nullable();
            $table->string('license_type')->nullable();
            $table->date('license_issue_date')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->string('license_image')->nullable();

            $table->tinyInteger('status')->default(1)->comment('1=active,0=inactive');
            $table->timestamp('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->tinyInteger('deleted')->default(0)->comment('1=deleted,0=active');
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
