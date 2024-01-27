<?php

namespace Laravel\Installer\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\ProcessUtils;


use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class NewCommand extends Command
{
    use Concerns\ConfiguresPrompts;

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    private $name;
    private $directory;


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new Laravel application')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addOption('pest', null, InputOption::VALUE_NONE, 'Installs the Pest testing framework')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Forces install even if the directory already exists')
            ->addOption('dev', null, InputOption::VALUE_NONE, 'Installs the latest "development" release')
            ->addOption('laravel-quiet', null, InputOption::VALUE_OPTIONAL, 'Dont show any Laravel Install', true)


            //add laradock
            ->addOption('laradock', null, InputOption::VALUE_NONE, 'Installs the Laradock scaffolding (in project)')

            // Breeze
            ->addOption('stack', null, InputOption::VALUE_OPTIONAL, 'The stack that should be installed', 'livewire')
            ->addOption('dark', null, InputOption::VALUE_NONE, 'Installs the dark theme for Breeze')
            ->addOption('ssr', null, InputOption::VALUE_NONE, 'Installs the SSR theme for Breeze')

            //composer packages to add
            ->addOption('all', null, InputOption::VALUE_NONE, 'Installs all the Composer packages')

            // composer packages
            ->addOption('laravelpwa', null, InputOption::VALUE_NONE, 'Installs the Laravel PWA scaffolding (in project)')
            ->addOption('laravel-schemaless-attributes', null, InputOption::VALUE_NONE, 'Installs the Laravel Schemaless Attributes scaffolding (in project)')
            ->addOption('laravel-permission', null, InputOption::VALUE_NONE, 'Installs the Laravel Permission scaffolding (in project)')
            ->addOption('laravel-medialibrary', null, InputOption::VALUE_NONE, 'Installs the Laravel Medialibrary scaffolding (in project)')

            // componets
            ->addOption('features', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Toggle components to install', [])


            //;
        ;
    }






    /**
     * Interact with the user before validating the input.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);

        $this->configurePrompts($input, $output);

        $output->write(
            //SpinUp logo
            PHP_EOL . '  <fg=green>
  ┏┓  •  ┳┳
  ┗┓┏┓┓┏┓┃┃┏┓
  ┗┛┣┛┗┛┗┗┛┣┛
    ┛      ┛
    </>
'
                // show name of the project if passed as an argument
                . ($input->getArgument('name') ? ' - Name: <options=bold>' . $input->getArgument('name') . '</>' . PHP_EOL : '')
                . ($input->getArgument('name') ? ' - Project directory: <options=bold>' . getcwd() . '/' . $input->getArgument('name') . '</>' . PHP_EOL : '')
                . ($input->getOption('laradock') ? ' - Laradock: <options=bold>Yes</>' . PHP_EOL : '')
                . ($input->getOption('pest') ? ' - Pest: <options=bold>Yes</>' . PHP_EOL : '')
                . ($input->getOption('force') ? ' - Force Delete Project: <options=bold>Yes</>' . PHP_EOL : '')
                . ($input->getOption('features') ? ' - Toggle Features: <options=bold>' . implode(', ', $input->getOption('features')) . '</>' . PHP_EOL : '')

                . PHP_EOL
                . PHP_EOL
        );

        // if not set, ask for the name of the project
        if (!$input->getArgument('name')) {
            $input->setArgument('name', text(
                label: 'What is the name of your project?',
                placeholder: 'E.g. example-app',
                required: 'The project name is required.',
                validate: fn ($value) => preg_match('/[^\pL\pN\-_.]/', $value) !== 0
                    ? 'The name may only contain letters, numbers, dashes, underscores, and periods.'
                    : null,
            ));
        }



        // if features is passed assume we are asking what features to install, otherwise all features are installed
        if ($input->getOption('features')) {
            $features = collect([
                'laravelpwa' => [
                    "install" => true,
                    "label" => 'Laravel PWA',
                    "description" => 'Laravel PWA',
                ],
                'laravel-schemaless-attributes' => [
                    "install" => false,
                    "label" => 'Laravel Schemaless Attributes',
                    "description" => 'Laravel Schemaless Attributes',

                ],
                'laravel-cashier' => [
                    "install" => false,
                    "label" => 'Laravel Cashier',
                    "description" => 'Laravel Cashier (Stripe)',
                ]
            ]);



            $input->setOption('features', multiselect(
                label: 'Toggle Features',
                options: $features->map(fn ($feature) => $feature['label'])->toArray(),
                default: $features->filter(fn ($feature) => $feature['install'])->keys(),
            ));
        }
    }


    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('  <bg=blue;fg=black> Excuting... </> '  . '<fg=blue>' . __FILE__ . ':' . __LINE__ . '</>' . PHP_EOL, OutputInterface::VERBOSITY_VERBOSE);


        /*
        |--------------------------------------------------------------------------
        | SETUP
        |--------------------------------------------------------------------------
        |
        | Variables and setup for the rest of the script, including
        | setting up the project name, directory, and composer
        | instance.
        |
        */
        $this->name = $input->getArgument('name');
        $this->directory =  ($this->name === '.') ? getcwd() : getcwd() . '/' . $this->name;
        $this->composer = new Composer(new Filesystem(), $this->directory);




        /*
        |--------------------------------------------------------------------------
        | Handle If Exsisting Project
        |--------------------------------------------------------------------------
        |  Now that we have the name, we can check if the directory exists
        |  and if it does, we can delete it if the -f flag is passed.
        |  If not, we can ask if they want to delete it.
        */
        $this->handleIfExsistingProject($input, $output);

        /*
        |--------------------------------------------------------------------------
        | Install Laravel
        |--------------------------------------------------------------------------
        |
        | This section installs Laravel, and sets up the project, including
        | setting up the database, and installing the composer packages
        | that were selected.
        |
        */

        $this->installLaravel($input, $output);


        /* anything below here should be optional and should be able to be turned off */


        /*
        |--------------------------------------------------------------------------
        | Install Laradock
        |--------------------------------------------------------------------------
        | [Optional] ask if they want to install Laradock, and if so, install it.
        |
        | This section installs Laradock, a collection of Docker images
        | used to run Laravel projects. It also sets up the .envs for
        | both Laravel and Laradock.
        |
        | [Docs]   https://laradock.io/
        |
        */
        $this->installLaradock($input, $output);



        /*
        |--------------------------------------------------------------------------
        | Install Deploy Script
        |--------------------------------------------------------------------------
        | This script automates the setup and management of a Laravel web
        | application within a Docker environment. It checks and
        | installs dependencies, manages database migrations,
        | and starts or builds Docker containers for local development.
        |
        | stubs/root/deploy.sh
        */
        $this->installDeployScript($input, $output);


        /*
        |--------------------------------------------------------------------------
        | Install Breeze
        |--------------------------------------------------------------------------
        | Laravel Breeze is a minimal, simple implementation of all of
        | Laravel's authentication features, including login,
        | registration, password reset, email verification, and
        | password confirmation.
        |
        | [Docs]   https://laravel.com/docs/starter-kits#laravel-breeze
        */
        $this->installBreeze($input, $output, $this->directory);




        /*
        |--------------------------------------------------------------------------
        | Install Componets/Template Packages
        |--------------------------------------------------------------------------
        | Using Breeze as a base, we can install other packages that
        | are commonly used in Laravel projects. These include
        | Laravel PWA, Laravel Schemaless Attributes,
        */
        $this->installFeatures($input, $output);



        return 0;
    }






    protected function handleIfExsistingProject(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('  <bg=blue;fg=black> handleIfExsistingProject... </> '  . '<fg=blue>' . __FILE__ . ':' . __LINE__ . '</>' . PHP_EOL, OutputInterface::VERBOSITY_VERBOSE);

        //  -f, --force
        // if -f is passed, delete the project if it exists

        // if not -f is passed, ask if they want to delete the project
        if (!$input->getOption('force')) {
            $this->verifyApplicationDoesntExist($this->directory);
        } else {
            // -f is passed, delete the project if it exists
            $commands = [
                'rm -rf ' . $this->directory,
            ];
            $this->runCommands($commands, $input, $output);
        }
    }

    protected function installLaravel(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('  <bg=blue;fg=black> installLaravel... </> '  . '<fg=blue>' . __FILE__ . ':' . __LINE__ . '</>' . PHP_EOL, OutputInterface::VERBOSITY_VERBOSE);

        $version = $this->getVersion($input);
        $quite = $input->getOption('laravel-quiet') ? '--quiet' : '';

        $this->timeLineOutput(true, $output, 'Installing Laravel...');

        $commands = [
            "composer create-project $quite laravel/laravel $this->directory $version --remove-vcs --prefer-dist",
        ];

        $this->runCommands($commands, $input, $output);

        // replace last output line with a green checkmark
        $this->timeLineOutput(true, $output, 'Installing Laravel...',  "✅ done");
    }

    protected function installDeployScript(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('  <bg=blue;fg=black> installDeployScript... </> '  . '<fg=blue>' . __FILE__ . ':' . __LINE__ . '</>' . PHP_EOL, OutputInterface::VERBOSITY_VERBOSE);

        $this->timeLineOutput(false, $output, 'Installing Deploy Script...');

        $stubRoot = dirname(__DIR__) . '/stubs' . '/root';

        $this->copyFile(
            $stubRoot . '/deploy.sh',
            $this->directory . '/deploy.sh'
        );
        // set permissions
        $commands = [
            "chmod 755 $this->directory/deploy.sh",
        ];
        $this->runCommands($commands, $input, $output);

        // replace last output line with a green checkmark
        $this->timeLineOutput(true, $output, 'Installing Deploy Script...',  "✅ done");
    }

    /*
    |--------------------------------------------------------------------------
    | Setup Functions
    |--------------------------------------------------------------------------
    | Below are functions that are used to setup the project. They are
    | called from the main execute function, these should be modular
    | to allow for easy editing and adding of new features.
    |
    */

    protected function installLaradock(InputInterface $input, OutputInterface $output)
    {
        $input->setOption('laradock', $input->getOption('laradock') || confirm(
            label: 'Would you like to install Laradock?',
            default: true,
        ));

        // if set to no or not interactive, return
        if (!$input->getOption('laradock')) {
            return;
        }
        $output->writeln('  <bg=blue;fg=black> installLaradock... </> '  . '<fg=blue>' . __FILE__ . ':' . __LINE__ . '</>' . PHP_EOL, OutputInterface::VERBOSITY_VERBOSE);
        $this->timeLineOutput(false, $output, 'Installing Laradock...');

        $laravelCommands = array_filter([
            "git clone https://github.com/Laradock/laradock.git laradock >/dev/null 2>&1",
            // this will run form the root directory
            "sed -i '' 's/^DB_HOST=127.0.0.1/DB_HOST=mysql/g' .env",
            "sed -i '' 's/^DB_DATABASE=laravel/DB_DATABASE=default/g' .env",
            "sed -i '' 's/^DB_USERNAME=root/DB_USERNAME=root/g' .env",
            "sed -i '' 's/^DB_PASSWORD=/DB_PASSWORD=root/g' .env",
            "sed -i '' 's/^REDIS_HOST=.*/REDIS_HOST=redis/g' .env",
        ]);

        $process = $this->runCommands(
            $laravelCommands,
            $input,
            $output,
            workingPath: $this->directory,
        );


        $process->isSuccessful() ?
            $this->timeLineOutput(true, $output, 'Installing Laradock...',  "✅ done") :
            $this->timeLineOutput(true, $output, 'Installing Laradock...',  "❌ failed");


        // now that it is cloned, we can run command in the directory

        $laradockCommands = array_filter([
            'cp .env.example .env',
            'sed -i "" "s+DATA_PATH_HOST=~/.laradock/data+DATA_PATH_HOST=../data+g" .env',
        ]);


        $process = $this->runCommands(
            $laradockCommands,
            $input,
            $output,
            workingPath: $this->directory . '/laradock',
        );
    }

    /* Setup Functions END*/


    /*
    |--------------------------------------------------------------------------
    | Helper Functions
    |--------------------------------------------------------------------------
    | These functions are used to help with the setup functions above.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | TimeLine Output
    |--------------------------------------------------------------------------
    | While running through the setup, we want show the user what is happening
    | and if it was successful or not. This function is used to output
    | the status of the setup, while also removing the previous
    |
    */
    function timeLineOutput($eraseLastLine, $output, $message = "Installing...", $status = 'in progress')
    {

        if ($eraseLastLine) {
            // move cursor up and erase line
            $output->write("\033[1A"); // Move up
            $output->write("\033[K"); // Erase line
        }

        $output->writeln("<bg=green;fg=black> $message </> $status");
    }

    protected function installBreeze(InputInterface $input, OutputInterface $output, string $directory)
    {
        $output->writeln('  <bg=blue;fg=black> installBreeze... </> '  . '<fg=blue>' . __FILE__ . ':' . __LINE__ . '</>' . PHP_EOL, OutputInterface::VERBOSITY_VERBOSE);

        $this->timeLineOutput(false, $output, 'Installing Breeze...');

        $commands = array_filter([
            "composer require laravel/breeze --dev  >/dev/null 2>&1",
            trim(sprintf(
                $this->phpBinary() . ' artisan breeze:install vue --dark %s %s >/dev/null 2>&1',
                $input->getOption('pest') ? '--pest' : '',
                $input->getOption('ssr') ? '--ssr' : '',
            ))
        ]);

        $process = $this->runCommands($commands, $input, $output, workingPath: $directory);

        $process->isSuccessful() ?
            $this->timeLineOutput(true, $output, 'Installing Breeze...',  "✅ done") :
            $this->timeLineOutput(true, $output, 'Installing Breeze...',  "❌ failed");
    }



    /*
    |--------------------------------------------------------------------------
    | INSTALL FEATURES
    |--------------------------------------------------------------------------
    | We want to show a list of features that can be installed, and
    | allow the user to select which ones they want to install, via multi-select.
    | We can then install the selected features.
    | this function will be responsible for installing the features with a toggled on
    | in the multi-select. we are going to assume all features are to be installed, unless
    | a flag of --features is passed, then we will only install
    | the components that are toggled on.
    |
    */


    protected function installFeatures(InputInterface $input, OutputInterface $output)
    {
        //
        $output->writeln('  <bg=blue;fg=black> installFeatures... </> '  . '<fg=blue>' . __FILE__ . ':' . __LINE__ . '</>' . PHP_EOL, OutputInterface::VERBOSITY_VERBOSE);
        $output->writeln('  <bg=blue;fg=black>' . collect($input->getOption('features'))  . PHP_EOL, OutputInterface::VERBOSITY_VERBOSE);


        // loop through the features and install them
        foreach ($input->getOption('features') as $feature) {
            $this->installFeature($feature, $input, $output);
        }
    }

    protected function installFeature($feature, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('  <bg=blue;fg=black> installFeature... </> '  . '<fg=blue>' . __FILE__ . ':' . __LINE__ . '</>' . PHP_EOL, OutputInterface::VERBOSITY_VERBOSE);
        $this->timeLineOutput(false, $output, "Installing $feature...");

        switch ($feature) {
            case 'laravelpwa':
                $this->installLaravelPWA($input, $output);
                break;
            case 'laravel-schemaless-attributes':
                $this->installLaravelSchemalessAttributes($input, $output);
                break;
            case 'laravel-cashier':
                $this->installLaravelCashier($input, $output);
                break;
            default:
                //  feature does not exist
                $output->writeln('  <bg=red;fg=black> ERROR </> '  . '<fg=red>' . $feature . ' does not exist' . '</>' . PHP_EOL, OutputInterface::VERBOSITY_VERBOSE);
                break;
        }
    }

    private function installLaravelPWA(InputInterface $input, OutputInterface $output)
    {
        // install laravel pwa

        //commands
        $commands = array_filter([
            $this->phpBinary() . ' artisan vendor:publish --provider="LaravelPWA\Providers\LaravelPWAServiceProvider"',
            /*
          I need to find the default html file and add @laravelPWA to it
          zsh: no such file or directory: head
        */
            exec('find . -name "app.blade.php" -exec sed -i \'\' \'s+<head>+<head>@laravelPWA+g\' {} \;')
        ]);

        $this->timeLineOutput(true, $output, "Installing Laravel PWA...",  "✅ done");
    }
    private function installLaravelSchemalessAttributes(InputInterface $input, OutputInterface $output)
    {
        $this->timeLineOutput(true, $output, "Installing Laravel Schemaless Attributes...",  "✅ done");
    }
    private function installLaravelCashier(InputInterface $input, OutputInterface $output)
    {
        $this->timeLineOutput(true, $output, "Installing Laravel Cashier...",  "✅ done");
    }





    protected function installPest(string $directory, InputInterface $input, OutputInterface $output)
    {
        if (
            $this->removeComposerPackages(['phpunit/phpunit'], $output, true)
            && $this->requireComposerPackages(['pestphp/pest:^2.0', 'pestphp/pest-plugin-laravel:^2.0'], $output, true)
        ) {
            $commands = array_filter([
                $this->phpBinary() . ' ./vendor/bin/pest --init',
            ]);

            $this->runCommands($commands, $input, $output, workingPath: $directory, env: [
                'PEST_NO_SUPPORT' => 'true',
            ]);

            $this->replaceFile(
                'pest/Feature.php',
                $directory . '/tests/Feature/ExampleTest.php',
            );

            $this->replaceFile(
                'pest/Unit.php',
                $directory . '/tests/Unit/ExampleTest.php',
            );

            $this->commitChanges('Install Pest', $directory, $input, $output);
        }
    }



    protected function configureComposerPackages(InputInterface $input, OutputInterface $output)
    {

        if ($input->getOption('laravelpwa')) {
            $this->requireComposerPackages(['silviolleite/laravelpwa'], $output, true);

            $commands = array_filter([
                $this->phpBinary() . ' artisan vendor:publish --provider="LaravelPWA\Providers\LaravelPWAServiceProvider"',
                /*
          I need to find the default html file and add @laravelPWA to it
          zsh: no such file or directory: head
        */
                exec('find . -name "app.blade.php" -exec sed -i \'\' \'s+<head>+<head>@laravelPWA+g\' {} \;')
            ]);

            $this->runCommands($commands, $input, $output);
        }

        if ($input->getOption('laravel-schemaless-attributes')) {
            $this->requireComposerPackages(['spatie/laravel-schemaless-attributes'], $output, true);
        }

        if ($input->getOption('laravel-permission')) {
            $this->requireComposerPackages(['spatie/laravel-permission'], $output, true);
        }

        if ($input->getOption('laravel-medialibrary')) {
            $this->requireComposerPackages(['spatie/laravel-medialibrary'], $output, true);
        }
    }


    protected function commitChanges(string $message, string $directory, InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('git') && $input->getOption('github') === false) {
            return;
        }

        $commands = [
            'git add .',
            "git commit -q -m \"$message\"",
        ];

        $this->runCommands($commands, $input, $output, workingPath: $directory);
    }

    protected function pushToGitHub(string $name, string $directory, InputInterface $input, OutputInterface $output)
    {
        $process = new Process(['gh', 'auth', 'status']);
        $process->run();

        if (!$process->isSuccessful()) {
            $output->writeln('  <bg=yellow;fg=black> WARN </> Make sure the "gh" CLI tool is installed and that you\'re authenticated to GitHub. Skipping...' . PHP_EOL);

            return;
        }

        $name = $input->getOption('organization') ? $input->getOption('organization') . "/$name" : $name;
        $flags = $input->getOption('github') ?: '--private';

        $commands = [
            "gh repo create {$name} --source=. --push {$flags}",
        ];

        $this->runCommands($commands, $input, $output, workingPath: $directory, env: ['GIT_TERMINAL_PROMPT' => 0]);
    }

    protected function verifyApplicationDoesntExist($directory)
    {
        if ((is_dir($directory) || is_file($directory)) && $directory != getcwd()) {
            throw new RuntimeException('Application already exists!');
        }
    }

    protected function generateAppUrl($name)
    {
        $hostname = mb_strtolower($name) . '.test';

        return $this->canResolveHostname($hostname) ? 'http://' . $hostname : 'http://localhost';
    }

    protected function canResolveHostname($hostname)
    {
        return gethostbyname($hostname . '.') !== $hostname . '.';
    }

    protected function getVersion(InputInterface $input)
    {
        if ($input->getOption('dev')) {
            return 'dev-master';
        }

        return '';
    }


    protected function findComposer()
    {
        return implode(' ', $this->composer->findComposer());
    }

    protected function phpBinary()
    {
        $phpBinary = (new PhpExecutableFinder)->find(false);

        return $phpBinary !== false
            ? ProcessUtils::escapeArgument($phpBinary)
            : 'php';
    }

    protected function requireComposerPackages(array $packages, OutputInterface $output, bool $asDev = false)
    {
        return $this->composer->requirePackages($packages, $asDev, $output);
    }

    protected function removeComposerPackages(array $packages, OutputInterface $output, bool $asDev = false)
    {
        return $this->composer->removePackages($packages, $asDev, $output);
    }

    protected function runCommands($commands, InputInterface $input, OutputInterface $output, string $workingPath = null, array $env = [])
    {
        if (!$output->isDecorated()) {
            $commands = array_map(function ($value) {
                if (str_starts_with($value, 'chmod')) {
                    return $value;
                }

                if (str_starts_with($value, 'git')) {
                    return $value;
                }

                return $value . ' --no-ansi';
            }, $commands);
        }

        if ($input->getOption('quiet')) {
            $commands = array_map(function ($value) {
                if (str_starts_with($value, 'chmod')) {
                    return $value;
                }

                if (str_starts_with($value, 'git')) {
                    return $value;
                }

                return $value . ' --quiet';
            }, $commands);
        }

        $process = Process::fromShellCommandline(implode(' && ', $commands), $workingPath, $env, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $output->writeln('  <bg=yellow;fg=black> WARN </> ' . $e->getMessage() . PHP_EOL);
            }
        }

        $process->run(function ($type, $line) use ($output) {
            $output->write('    ' . $line);
        });

        return $process;
    }

    protected function replaceFile(string $replace, string $file)
    {
        $stubs = dirname(__DIR__) . '/stubs';

        file_put_contents(
            $file,
            file_get_contents("$stubs/$replace"),
        );
    }

    protected function replaceInFile(string|array $search, string|array $replace, string $file)
    {
        file_put_contents(
            $file,
            str_replace($search, $replace, file_get_contents($file))
        );
    }

    protected function pregReplaceInFile(string $pattern, string $replace, string $file)
    {
        file_put_contents(
            $file,
            preg_replace($pattern, $replace, file_get_contents($file))
        );
    }


    protected function copyDirectory(string $source, string $destination)
    {
        $filesystem = new Filesystem();
        $filesystem->copyDirectory($source, $destination);
    }

    protected function copyFile(string $source, string $destination)
    {
        $filesystem = new Filesystem();
        $filesystem->copy($source, $destination);
    }



    // for reference
    // protected function EXECUTE_MASTER(InputInterface $input, OutputInterface $output): int
    // {
    //     $this->validateStackOption($input);

    //     $name = $input->getArgument('name');

    //     $directory = $name !== '.' ? getcwd() . '/' . $name : '.';

    //     $this->composer = new Composer(new Filesystem(), $directory);

    //     $version = $this->getVersion($input);

    //     if (!$input->getOption('force')) {
    //         $this->verifyApplicationDoesntExist($directory);
    //     }

    //     if ($input->getOption('force') && $directory === '.') {
    //         throw new RuntimeException('Cannot use --force option when using current directory for installation!');
    //     }

    //     $composer = $this->findComposer();

    //     $commands = [
    //         $composer . " create-project laravel/laravel \"$directory\" $version --remove-vcs --prefer-dist",
    //     ];

    //     if ($directory != '.' && $input->getOption('force')) {
    //         if (PHP_OS_FAMILY == 'Windows') {
    //             array_unshift($commands, "(if exist \"$directory\" rd /s /q \"$directory\")");
    //         } else {
    //             array_unshift($commands, "rm -rf \"$directory\"");
    //         }
    //     }

    //     if (PHP_OS_FAMILY != 'Windows') {
    //         $commands[] = "chmod 755 \"$directory/artisan\"";
    //     }

    //     if (($process = $this->runCommands($commands, $input, $output))->isSuccessful()) {
    //         if ($name !== '.') {
    //             $this->replaceInFile(
    //                 'APP_URL=http://localhost',
    //                 'APP_URL=' . $this->generateAppUrl($name),
    //                 $directory . '/.env'
    //             );

    //             [$database, $migrate] = $this->promptForDatabaseOptions($directory, $input);

    //             $this->configureDefaultDatabaseConnection($directory, $database, $name, $migrate);

    //             if ($migrate) {
    //                 $this->runCommands([
    //                     $this->phpBinary() . ' artisan migrate',
    //                 ], $input, $output, workingPath: $directory);
    //             }
    //         }

    //         if ($input->getOption('git') || $input->getOption('github') !== false) {
    //             $this->createRepository($directory, $input, $output);
    //         }

    //         if ($input->getOption('breeze')) {
    //             $this->installBreeze($directory, $input, $output);
    //         } elseif ($input->getOption('jet')) {
    //             $this->installJetstream($directory, $input, $output);
    //         } elseif ($input->getOption('pest')) {
    //             $this->installPest($directory, $input, $output);
    //         }

    //         if ($input->getOption('github') !== false) {
    //             $this->pushToGitHub($name, $directory, $input, $output);
    //             $output->writeln('');
    //         }

    //         $output->writeln("  <bg=blue;fg=white> INFO </> Application ready in <options=bold>[{$name}]</>. You can start your local development using:" . PHP_EOL);

    //         $output->writeln('<fg=gray>➜</> <options=bold>cd ' . $name . '</>');
    //         $output->writeln('<fg=gray>➜</> <options=bold>php artisan serve</>');
    //         $output->writeln('');

    //         $output->writeln('  New to Laravel? Check out our <href=https://bootcamp.laravel.com>bootcamp</> and <href=https://laravel.com/docs/installation#next-steps>documentation</>. <options=bold>Build something amazing!</>');
    //         $output->writeln('');
    //     }

    //     return $process->getExitCode();
    // }
}
