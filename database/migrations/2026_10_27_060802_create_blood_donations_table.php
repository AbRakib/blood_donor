<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_donations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blood_donor_id')->constrained();
            $table->foreignId('blood_request_id')->nullable()->constrained();
            $table->date('donated_at');
            $table->string('donation_status', 20)->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->unsignedInteger('verified_by')->nullable();
            $table->index(['donation_status', 'donated_at']);
            $table->unsignedSmallInteger('bags')->default(1);
            $table->string('hospital_name')->nullable();
            $table->text('notes')->nullable();
            $table->unique(['blood_request_id', 'blood_donor_id']);
            $table->index(['blood_donor_id', 'donated_at']);
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
        Schema::dropIfExists('blood_donations');
    }
};
