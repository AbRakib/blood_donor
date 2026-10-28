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
        Schema::create('districts', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('code', 20)->nullable()->unique();
            $table->tinyInteger('status')->default(1)->comment('1=active,0=inactive');
            $table->timestamp('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->tinyInteger('deleted')->default(0)->comment('1=deleted,0=active');
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });

        Schema::create('upazilas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('district_id')->constrained();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->unique(['district_id', 'name']);
            $table->tinyInteger('status')->default(1)->comment('1=active,0=inactive');
            $table->timestamp('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->tinyInteger('deleted')->default(0)->comment('1=deleted,0=active');
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });

        Schema::create('unions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('upazila_id')->constrained();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->unique(['upazila_id', 'name']);
            $table->tinyInteger('status')->default(1)->comment('1=active,0=inactive');
            $table->timestamp('created_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->tinyInteger('deleted')->default(0)->comment('1=deleted,0=active');
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });

        Schema::create('blood_donors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('blood_group_id')->constrained('blood_groups');
            $table->string('phone', 20);
            $table->string('area')->nullable();
            $table->text('address')->nullable();
            $table->date('last_donation_date')->nullable();
            $table->boolean('is_available')->default(true);
            $table->unsignedInteger('total_donations')->default(0);
            $table->foreignId('district_id')->nullable()->constrained();
            $table->foreignId('upazila_id')->nullable()->constrained();
            $table->foreignId('union_id')->nullable()->constrained();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->boolean('consent_to_contact')->default(false);
            $table->index(['blood_group_id', 'status', 'deleted', 'is_available'], 'donor_search_index');

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
        Schema::dropIfExists('blood_donors');
        Schema::dropIfExists('unions');
        Schema::dropIfExists('upazilas');
        Schema::dropIfExists('districts');
    }
};
