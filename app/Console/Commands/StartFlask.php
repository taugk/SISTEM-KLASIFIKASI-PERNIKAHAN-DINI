<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'serve', description: 'Menjalankan Laravel dan Flask bersamaan')]
class StartFlask extends ServeCommand
{
    public function handle()
    {
        $this->info("Menjalankan Flask API...");

        $apiDir = 'C:\Users\user\Desktop\web\backend_model';
        $command = 'cmd /c start cmd /k "cd /d ' . $apiDir . ' && set FLASK_APP=main.py && set FLASK_ENV=development && python -m flask run --host=127.0.0.1 --port=5000"';


        $process = Process::fromShellCommandline($command);

        try {
            $process->run();
            $this->info("Flask API telah dijalankan di http://127.0.0.1:5000");
        } catch (\Exception $e) {
            $this->error("Gagal menjalankan Flask API: " . $e->getMessage());
        }

        return parent::handle();
    }
}
