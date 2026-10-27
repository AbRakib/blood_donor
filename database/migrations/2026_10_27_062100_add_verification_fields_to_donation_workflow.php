<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_donors', function (Blueprint $table): void {
            $table->unsignedInteger('total_donations')->default(0);
        });
        Schema::table('blood_donations', function (Blueprint $table): void {
            $table->string('donation_status', 20)->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->unsignedInteger('verified_by')->nullable();
            $table->index(['donation_status', 'donated_at']);
        });
        Schema::table('blood_requests', function (Blueprint $table): void {
            $table->timestamp('verified_at')->nullable();
            $table->unsignedInteger('verified_by')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
        });
        DB::table('blood_requests')->where('request_status', 'matching')->update(['request_status' => 'in_progress']);
    }

    public function down(): void
    {
        DB::table('blood_requests')->where('request_status', 'in_progress')->update(['request_status' => 'matching']);
        DB::table('blood_requests')->where('request_status', 'verified')->update(['request_status' => 'pending']);
        DB::table('blood_requests')->where('request_status', 'expired')->update(['request_status' => 'cancelled']);
        Schema::table('blood_requests', function (Blueprint $table): void {
            $table->dropIndex(['expires_at']);
            $table->dropColumn(['verified_at', 'verified_by', 'expires_at']);
        });
        Schema::table('blood_donations', function (Blueprint $table): void {
            $table->dropIndex(['donation_status', 'donated_at']);
            $table->dropColumn(['donation_status', 'verified_at', 'verified_by']);
        });
        Schema::table('blood_donors', function (Blueprint $table): void {
            $table->dropColumn('total_donations');
        });
    }
};
