<?php

namespace App\Console\Commands;

use App\Models\BloodRequest;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ExpireBloodRequests extends Command
{
    protected $signature = 'blood-requests:expire';

    protected $description = 'Close open blood requests whose configured expiry time has passed';

    public function handle(): int
    {
        BloodRequest::query()->active()->whereIn('request_status', ['pending', 'verified', 'in_progress'])->where('expires_at', '<=', now())->orderBy('id')->chunkById(100, function (Collection $records): void {
            foreach ($records as $record) {
                DB::transaction(function () use ($record): void {
                    $locked = BloodRequest::query()->active()->lockForUpdate()->find($record->id);
                    if ($locked && in_array($locked->request_status, ['pending', 'verified', 'in_progress'], true) && $locked->expires_at?->lessThanOrEqualTo(now())) {
                        $locked->changeStatus('expired');
                    }
                });
            }
        });
        $this->info('Expired requests processed.');

        return self::SUCCESS;
    }
}
