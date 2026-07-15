<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'make:service')]
class MakeService extends Command
{
    protected $description = 'Create a new service class in the Services directory';

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the service class');
    }

    public function handle(): int
    {
        $name = $this->argument('name');

        $servicePath = app_path('Services');

        if (! File::exists($servicePath)) {
            File::makeDirectory($servicePath, 0755, true);
            $this->info('Created directory: app/Services');
        }

        $className = ucfirst($name);
        $filePath = "{$servicePath}/{$className}.php";

        if (File::exists($filePath)) {
            $this->error("Service {$className} already exists!");

            return self::FAILURE;
        }

        $stub = <<<PHP
<?php

namespace App\Services;

class {$className}
{
    public function __construct()
    {
        // Initialization logic
    }
}
PHP;

        File::put($filePath, $stub);
        $this->info("Service class {$className} created at Services/{$className}.php");

        return self::SUCCESS;
    }
}
