<?php

// app/Console/Commands/SetOrigin.php
namespace App\Console\Commands;

use App\Models\ShippingOrigin;
use Illuminate\Console\Command;

class SetOrigin extends Command
{
    protected $signature = 'ro:set-origin {origin_id} {--label=Gudang Utama}';
    protected $description = 'Set shipping origin aktif';

    public function handle()
    {
        $id = (int) $this->argument('origin_id');
        ShippingOrigin::query()->update(['is_active'=>false]);
        ShippingOrigin::updateOrCreate(['id'=>1], [
            'origin_id' => $id,
            'label'     => (string) $this->option('label'),
            'is_active' => true,
        ]);
        $this->info("Origin set ke ID {$id}.");
        return self::SUCCESS;
    }
}
