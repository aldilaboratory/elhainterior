<?php

// app/Console/Commands/RoSmoke.php
namespace App\Console\Commands;

use App\Services\RajaOngkirService;
use Illuminate\Console\Command;

class RoSmoke extends Command
{
    protected $signature = 'ro:smoke {q}';
    protected $description = 'Cari ID tujuan Komerce (search)';

    public function handle(RajaOngkirService $svc)
    {
        $q = $this->argument('q');
        $res = $svc->search($q);
        $this->info('Top 5:');
        $this->line(json_encode(array_slice($res,0,5), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        return self::SUCCESS;
    }
}
