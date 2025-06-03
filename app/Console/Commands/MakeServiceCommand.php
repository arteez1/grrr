<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';
    public function handle(): void
    {
        $name = $this->argument('name') . 'Service';
        $path = app_path('Services/' . $name . '.php');

        if (file_exists($path)) {
            $this->error('Service already exists!');
            return;
        }

        $stub = file_get_contents(__DIR__.'/stubs/service.stub');
        $stub = str_replace('{{className}}', $name, $stub);

        file_put_contents($path, $stub);

        $this->info('Service created successfully: ' . $name);
    }
}
