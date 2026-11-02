<?php

namespace App\Providers;

use App\Mail\Transport\GraphTokenProvider;
use App\Mail\Transport\GraphTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class GraphMailServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Mail::extend('graph', fn (array $config) => new GraphTransport(
            new GraphTokenProvider($config),
            $config['mailbox'] ?? '',
        ));
    }
}
