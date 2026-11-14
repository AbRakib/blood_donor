<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blood_group_id')->constrained();
            $table->foreignId('district_id')->nullable()->constrained();
            $table->foreignId('upazila_id')->nullable()->constrained();
            $table->foreignId('union_id')->nullable()->constrained();
            $table->string('patient_name');
            $table->string('contact_name');
            $table->string('contact_phone', 20);
            $table->string('hospital_name');
            $table->text('hospital_address')->nullable();
            $table->unsignedSmallInteger('bags_needed')->default(1);
            $table->dateTime('needed_at');
            $table->string('urgency', 20)->default('normal');
            $table->string('request_status', 20)->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->unsignedInteger('verified_by')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->text('notes')->nullable();
            $table->index(['request_status', 'needed_at']);
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

    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
