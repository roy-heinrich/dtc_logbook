<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Facility;
use App\Models\RegUser;
use App\Models\Role;
use App\Models\Service;
use Illuminate\Console\Command;

class PurgeSoftDeletedRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:purge-soft-deletes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete soft-deleted records older than 30 days.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cutoff = now()->subDays(30);

        $counts = [
            'admins' => Admin::onlyTrashed()->where('deleted_at', '<=', $cutoff)->forceDelete(),
            'roles' => Role::onlyTrashed()->where('deleted_at', '<=', $cutoff)->forceDelete(),
            'regusers' => RegUser::onlyTrashed()->where('deleted_at', '<=', $cutoff)->forceDelete(),
            'facilities' => Facility::onlyTrashed()->where('deleted_at', '<=', $cutoff)->forceDelete(),
            'services' => Service::onlyTrashed()->where('deleted_at', '<=', $cutoff)->forceDelete(),
        ];

        $this->info('Soft-deleted records purged.');
        foreach ($counts as $table => $count) {
            $this->line("{$table}: {$count}");
        }

        return self::SUCCESS;
    }
}
