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

        Schema::create('request_donors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blood_request_id')->constrained();
            $table->foreignId('blood_donor_id')->constrained();
            $table->string('response_status', 20)->default('pending');
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->text('notes')->nullable();
            $table->unique(['blood_request_id', 'blood_donor_id']);
            $table->tinyInteger('status')->default(1)->comment('1=active,0=inactive');
            $table->timestamp('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->tinyInteger('deleted')->default(0)->comment('1=deleted,0=active');
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });

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

        Schema::create('blood_request_status_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blood_request_id')->constrained();
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->text('reason')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=active,0=inactive');
            $table->timestamp('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->tinyInteger('deleted')->default(0)->comment('1=deleted,0=active');
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });

        Schema::create('donor_availability_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blood_donor_id')->constrained();
            $table->boolean('was_available');
            $table->boolean('is_available');
            $table->text('reason')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=active,0=inactive');
            $table->timestamp('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->tinyInteger('deleted')->default(0)->comment('1=deleted,0=active');
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });

        Schema::create('donor_contact_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blood_donor_id')->constrained();
            $table->foreignId('blood_request_id')->nullable()->constrained();
            $table->string('requester_name');
            $table->string('requester_phone', 20);
            $table->text('message');
            $table->string('contact_status', 20)->default('pending');
            $table->timestamp('handled_at')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=active,0=inactive');
            $table->timestamp('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->tinyInteger('deleted')->default(0)->comment('1=deleted,0=active');
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });

        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 30);
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->index(['subject_type', 'subject_id']);
            $table->tinyInteger('status')->default(1)->comment('1=active,0=inactive');
            $table->timestamp('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->tinyInteger('deleted')->default(0)->comment('1=deleted,0=active');
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });

        Schema::create('system_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('donor_contact_requests');
        Schema::dropIfExists('donor_availability_logs');
        Schema::dropIfExists('blood_request_status_logs');
        Schema::dropIfExists('blood_donations');
        Schema::dropIfExists('request_donors');
        Schema::dropIfExists('blood_requests');
    }
};
