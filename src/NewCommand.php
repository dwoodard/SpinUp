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
use function Laravel\Prompts\progress;

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

    // Features must follow the naming convention of FEATURE_[NAME_OF_FEATURE]
    private $features = [
        'FEATURE_LARAVEL_PWA',
        'FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES',
        'FEATURE_LARAVEL_CASHIER',
        'FEATURE_LARAVEL_PERMISSION',
        'FEATURE_HEADLESS_UI',
    ];


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


            //add General
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Forces install even if the directory already exists')
            ->addOption('dev', null, InputOption::VALUE_NONE, 'Installs the latest "development" release')
            ->addOption('laravel-quiet', null, InputOption::VALUE_OPTIONAL, 'Dont show any Laravel Install', true)

            //debug
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Debug')

            //add laradock
            ->addOption('laradock', null, InputOption::VALUE_NONE, 'Installs the Laradock scaffolding (in project)')

            // Breeze
            ->addOption('stack', null, InputOption::VALUE_OPTIONAL, 'The stack that should be installed', 'livewire')
            ->addOption('dark', null, InputOption::VALUE_NONE, 'Installs the dark theme for Breeze')
            ->addOption('ssr', null, InputOption::VALUE_NONE, 'Installs the SSR theme for Breeze')

            //composer packages to add
            ->addOption('all', null, InputOption::VALUE_NONE, 'Installs all the Composer packages')



            // componets
            ->addOption('features', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Toggle features to install')

            //;
        ;

        // add features as options by looping through the features array
        foreach ($this->features as $feature) {
            $this->addOption(
                $feature,
                null,
                InputOption::VALUE_NONE,
                'Installs the ' . $feature . ' feature'
            );
        }
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

        $this->configurePrompts($input, $output); //this configures the prompts to use the input and output

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
                . ($input->getOption('force') ? ' - Force Delete Project: <options=bold>Yes</>' . PHP_EOL : '')



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

        // if --features is passed, ask which features they want to install
        // otherwise, install all features
        if ($input->getOption('features')) {
            $input->setOption('features', multiselect(
                label: 'Which features would you like to install?',
                options: $this->features,
                default: $this->features
            ));
        } else {
            $input->setOption('features', $this->features);
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

        $this->debug('Excuting...', $input, $output);

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

        /*  */
        $this->handleIfExsistingProject($input, $output);
        $this->installLaravel($input, $output);
        $this->installBreeze($input, $output, $this->directory);
        /* anything below here should be optional and should be able to be turned off */
        $this->installLaradock($input, $output);
        /*  */

        $this->installStubs($input, $output);

        // this might in the stubs section?
        // $this->installDeployScript($input, $output);

        $this->installTemplates($input, $output);

        // $this->installFeatures($input, $output);


        /*
        |--------------------------------------------------------------------------
        | Seeders
        |--------------------------------------------------------------------------
        |
        | Commonly used seeders for the project
        */
        // $this->copySeeders($input, $output);

        $this->ShowHowProjectRuns($input, $output);



        return 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Handle If Exsisting Project
    |--------------------------------------------------------------------------
    |  Now that we have the name, we can check if the directory exists
    |  and if it does, we can delete it if the -f flag is passed.
    |  If not, we can ask if they want to delete it.
    */
    protected function handleIfExsistingProject(InputInterface $input, OutputInterface $output)
    {
        $this->debug('handleIfExsistingProject ...', $input, $output);

        //  -f, --force
        // if -f is passed, delete the project if it exists
        if (!$input->getOption('force')) {
            $this->verifyApplicationDoesntExist($this->directory, $input, $output);
        }
        // if not -f is passed, ask if they want to delete the project
        else {
            // -f is passed, delete the project if it exists
            $commands = [
                'rm -rf ' . $this->directory,
            ];
            $this->runCommands($commands, $input, $output);
        }
    }

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
    protected function installLaravel(InputInterface $input, OutputInterface $output)
    {
        $this->debug('Install Laravel...', $input, $output);

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

    /*
    |--------------------------------------------------------------------------
    | Stubs
    |--------------------------------------------------------------------------
    | Copy stub files from the stubs directory to the project directory.
    */
    private function installStubs(InputInterface $input, OutputInterface $output)
    {
        /*
            using a custom function that reads each file in the stubs directory
            looking for the feature flag, if true/false, it will include/exclude
            that part of the stub file.

            This Install Stubs function will be responsible for installing the stubs
            it will loop through each file in the stubs directory, including sub directories
            and will copy the file to the project directory, replacing any variables
            in the stub file with the correct values.

            It will check each file for any feature flags, and if the feature is toggled on,

            we'll need to filter out the features that are toggled on, and only install those
            features via ->filter() and ->map().

            Once we have the list of features, we can loop through each file in the stubs
            with this array of features we'll look through each file for something like
            FEATURE_[FEATURE_NAME], and if it is found, we'll include that part of the stub file.

            to do this we'll need to do the following:
            - get the list of features that are toggled on
            - loop through each file in the stubs directory and sub directories
             - using Filesystem and RecursiveDirectoryIterator
             - check if the file has any feature flags
                - if it does, check if the feature is toggled on
                    - if it is, include that part of the stub file
                    - if not, exclude that part of the stub file
            - copy the file to the project directory

            one last thing to remember is if the file in in the root directory
            it means it goes in the root directory of the project. we'll need a
            way to check if the file is in the root directory, and if so, copy it
            to the project directory.


            do it until all files in the stubs directory are copied to the project directory

            Example of pattern is
            FEATURE_LARAVE_PWA:START
                @laravelPWA
            FEATURE_LARAVE_PWA:END

            we want to include the @laravelPWA if FEATURE_LARAVE_PWA is toggled on
            if FEATURE_LARAVE_PWA is not toggled on, we want to exclude the @laravelPWA

            create a function called processFeatureFlags($contents, $features)
            - it will check if the file has any feature flags
            - if it does, check if the feature is toggled on
            - if it is, include that part of the stub file
            - then remove the feature flag from the file
            - if not, exclude that part of the stub file
            - then remove the feature flag from the file

            - then return the contents of the file

            lets sudo code this
            - get all the files in the stubs directory
            - loop through each file
            - check if the file has any feature flags
            - if it does, check if the feature is toggled on
            - if it is, include that part of the stub file
            - then remove the feature flag from the file
            - if not, exclude that part of the stub file
            - then remove the feature flag from the file
            - then copy the file to the project directory (if its in the root directory, copy it to the project directory)
            - then set the permissions to 0755
        */


        // if feature has the option, it means it is toggled on
        $features = collect($input->getOption('features'));

        // create Filesystem object
        $filesystem = new Filesystem();

        // get all the files in the stubs directory
        $files = collect($filesystem->allFiles(dirname(__DIR__) . '/stubs'))->map(
            fn ($file) => $file->getPathname()
        );



        function processFeatureFlags($contents, $features)
        {
            // Check if the file has any feature flags
            if (str_contains($contents, 'FEATURE_')) {
                // Iterate through each feature flag
                foreach ($features as $feature) {
                    // Check if the feature is toggled on
                    if (str_contains($contents, $feature)) {
                        // Include all instances of that part of the stub file
                        $contents = preg_replace(
                            "/$feature:START(.*?)$feature:END/s",
                            "",
                            $contents
                        );
                    }
                }
            }
            // Remove any remaining feature flag sections
            $contents = preg_replace(
                "/FEATURE_.*?:START(.*?)FEATURE_.*?:END/s",
                "",
                $contents
            );
            return $contents;
        }

        function saveContentToNewDestination($file, $contents, $directory)
        {


            $projectPath = $directory . str_replace('/stubs', '', str_replace(dirname(__DIR__), '', $file));

            // a quick check to see if the file is in the root directory
            // if it is, we'll need to copy it to the project directory
            // if file is in the root directory, copy it to the project directory
            if (str_contains($file, '/stubs/root/')) {
                $projectPath = $directory . '/' . basename($file);
            }





            // check if the directory exists for the file
            // if not, create it
            if (!file_exists(dirname($projectPath))) {
                mkdir(dirname($projectPath), 0755, true);
            }



            // Copy the file to the project directory
            if (copy($file, $projectPath)) {
                // Set the permissions to 0755
                chmod($projectPath, 0755);
            }

            return file_exists($projectPath);
        }




        // loop through each file
        $files->each(function ($file) use ($features, $filesystem, $output) {


            // get the contents of the file
            $contents = file_get_contents($file);


            // check if the file has any feature flags
            $contents = processFeatureFlags($contents, $features);

            // write out contents to new destination
            $saved = saveContentToNewDestination($file, $contents, $this->directory);

            // if the file was not saved, show an error
            $this->timeLineOutput(true, $output, "$file", $saved ?
                "✅ done" :
                "❌ failed");
        });

        // get the list of files in the stubs directory

        $this->debug('Install Stubs...', $input, $output);

        $this->timeLineOutput(false, $output, 'Installing Stubs...');

        $stubsDirectory = dirname(__DIR__) . '/stubs';
        $projectDirectory = $this->directory;



        $this->timeLineOutput(true, $output, 'Installing Stubs...',  "✅ done");
    }

















    /*
    --------------------------------------------------------------------------
    */








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
    // protected function installDeployScript(InputInterface $input, OutputInterface $output)
    // {
    //     $this->debug('Install Deploy Script...', $input, $output);

    //     $this->timeLineOutput(false, $output, 'Installing Deploy Script...');

    //     $stubRoot = dirname(__DIR__) . '/stubs' . '/root';

    //     $this->copyFile(
    //         $stubRoot . '/deploy.sh',
    //         $this->directory . '/deploy.sh'
    //     );
    //     // set permissions
    //     $commands = [
    //         "chmod 755 $this->directory/deploy.sh",
    //     ];
    //     $this->runCommands($commands, $input, $output);

    //     // replace last output line with a green checkmark
    //     $this->timeLineOutput(true, $output, 'Installing Deploy Script...',  "✅ done");
    // }

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

        $this->debug('installLaradock...', $input, $output);

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
    protected function installBreeze(InputInterface $input, OutputInterface $output, string $directory)
    {
        $this->debug('Install Breeze...', $input, $output);

        $this->timeLineOutput(false, $output, 'Installing Breeze...');

        $commands = array_filter([
            "composer require laravel/breeze --dev  >/dev/null 2>&1",
            trim(sprintf(
                $this->phpBinary() . ' artisan breeze:install vue --dark %s >/dev/null 2>&1',
                $input->getOption('ssr') ? '--ssr' : '',
            ))
        ]);

        $process = $this->runCommands($commands, $input, $output, workingPath: $directory);

        $process->isSuccessful() ?
            $this->timeLineOutput(true, $output, 'Installing Breeze...',  "✅ done") :
            $this->timeLineOutput(true, $output, 'Installing Breeze...',  "❌ failed");
    }

    private function ShowHowProjectRuns(InputInterface $input, OutputInterface $output)
    {

        $output->writeln(PHP_EOL . '<bg=green>       Run Project       </> ' .  PHP_EOL);

        $output->writeln(
            PHP_EOL .
                "cd $this->directory  && ./deploy.sh"
                . PHP_EOL
        );
    }

    /* Setup Functions END*/


    /*
    |--------------------------------------------------------------------------
    | Helper Functions
    |--------------------------------------------------------------------------
    | These functions are used to help with the setup functions above.


    |--------------------------------------------------------------------------
    | TimeLine Output
    |--------------------------------------------------------------------------
    | While running through the setup, we want show the user what is happening
    | and if it was successful or not. This function is used to output
    | the status of the setup, while also removing the previous
    |
    */
    private function timeLineOutput($eraseLastLine, $output, $message = "Installing...", $status = 'in progress')
    {

        if ($eraseLastLine) {
            // move cursor up and erase line
            $output->write("\033[1A"); // Move up
            $output->write("\033[K"); // Erase line
        }

        $output->writeln("<bg=green;fg=black> $message </> $status");
    }


    /*
    |--------------------------------------------------------------------------
    | INSTALL FEATURES
    |--------------------------------------------------------------------------
    | Show a list of features that can be installed, and
    | allow the user to select which ones they want to install, via multi-select.
    | We can then install the selected features.
    | this function will be responsible for installing the features with a toggled on
    | in the multi-select. we are going to assume all features are to be installed, unless
    | a flag of --features is passed, then we will only install
    | the components that are toggled on.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | INSTALL TEMPLATE
    |--------------------------------------------------------------------------

    */


    /*
    |--------------------------------------------------------------------------
    | Install Componets/Template Packages
    |--------------------------------------------------------------------------
    | Using Breeze as a base, we can install other packages that
    | are commonly used in Laravel projects. These include
    | Laravel PWA, Laravel Schemaless Attributes,

    | Tailwind CSS, Vue Components, Headless UI, Etc.
    |
    | Because we are using Breeze as a base, we'll base the template off of that.
    |
    | we'll move dashboard to a set of admin routes, and then we'll have a set of
    | - remove from routes/web.php
    | - copy stubs/routes/admin.php to routes/admin.php
    | - copy stubs/resources/views/admin to resources/views/admin
    */
    private function installTemplates(InputInterface $input, OutputInterface $output)
    {
        $this->debug('Install Template...', $input, $output);

        // remove the welcome.blade.php file
        $commands = [
            "rm -rf $this->directory/resources/views/welcome.blade.php",

        ];
        $this->runCommands($commands, $input, $output);

        $this->timeLineOutput(false, $output, 'Installing Template...');

        /*
            stubs/resources/views/app.blade.php
         */
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
    protected function installFeatures(InputInterface $input, OutputInterface $output)
    {
        $this->debug('Install Features...', $input, $output);

        $output->writeln('  <bg=blue;fg=black>' . collect($input->getOption('features'))  . PHP_EOL, OutputInterface::VERBOSITY_VERBOSE);

        // loop through the features and install them
        foreach ($input->getOption('features') as $feature) {
            $this->installFeature($feature, $input, $output);
        }
    }

    protected function installFeature($feature, InputInterface $input, OutputInterface $output)
    {
        $this->debug("Install $feature...", $input, $output);

        $this->timeLineOutput(false, $output, "Installing $feature...");

        switch ($feature) {
            case 'FEATURE_LARAVEL_PWA':
                $this->installLaravelPWA($input, $output);
                break;
            case 'FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES':
                $this->installLaravelSchemalessAttributes($input, $output);
                break;
            case 'FEATURE_LARAVEL_CASHIER':
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
        $this->timeLineOutput(true, $output, "Installing Laravel PWA...");



        //php artisan vendor:publish --provider="LaravelPWA\Providers\LaravelPWAServiceProvider"
        $commands = array_filter([
            'composer require silviolleite/laravelpwa --prefer-dist >/dev/null 2>&1',
            $this->phpBinary() . ' artisan vendor:publish --provider="LaravelPWA\Providers\LaravelPWAServiceProvider"',
        ]);
        // rather than using sed, we can just replace the file
        $this->replaceInFile(
            '<head>',
            '<head>' . PHP_EOL . '    @laravelPWA',
            $this->directory . '/resources/views/app.blade.php',
        );

        $this->runCommands($commands, $input, $output, workingPath: $this->directory);

        $this->timeLineOutput(true, $output, "Installing Laravel PWA...",  "✅ done");
    }

    private function installLaravelSchemalessAttributes(InputInterface $input, OutputInterface $output)
    {
        $this->timeLineOutput(true, $output, "Installing Laravel PWA...");
        $quite = ">/dev/null 2>&1";
        // composer require spatie/laravel-schemaless-attributes
        $commands = array_filter([
            // Add the package to the project
            "composer require spatie/laravel-schemaless-attributes --prefer-dist $quite",
            // Add migration to add schemaless_attributes column to users table
            $this->replaceFile(
                'database/migrations/2024_01_01_000001_add_schemaless_attributes_column_to_users_table.php',
                $this->directory . '/database/migrations/2024_01_01_000001_add_schemaless_attributes_column_to_users_table.php',
            ),

            // Add the HasSchemalessAttributes trait to the User model
            $this->replaceInFile(
                'use Laravel\Sanctum\HasApiTokens;',
                'use Laravel\Sanctum\HasApiTokens;' . PHP_EOL .
                    'use Spatie\\SchemalessAttributes\\Casts\\SchemalessAttributes;',
                $this->directory . '/app/Models/User.php',
            ),
            $this->replaceInFile(
                [
                    "protected \$casts = [\n        'email_verified_at' => 'datetime',\n        'password' => 'hashed',\n    ];",
                    "public function scopeWithExtraAttributes(): Builder\n    {\n        return \$this->extra_attributes->modelScope();\n    }",
                ],
                [
                    "protected \$casts = [\n        'email_verified_at' => 'datetime',\n        'password' => 'hashed',\n        'settings' => SchemalessAttributes::class\n    ];",
                    "public function scopeWithExtraAttributes(): Builder\n    {\n        return \$this->extra_attributes->modelScope();\n    }\n",
                ],
                $this->directory . '/app/Models/User.php'
            )
        ]);

        $this->runCommands($commands, $input, $output, workingPath: $this->directory);

        $this->timeLineOutput(true, $output, "Installing Laravel Schemaless Attributes...",  "✅ done");
    }

    private function installLaravelCashier(InputInterface $input, OutputInterface $output)
    {
        $this->timeLineOutput(true, $output, "Installing Laravel Cashier...",  "✅ done");
    }

    protected function configureComposerPackages(InputInterface $input, OutputInterface $output)
    {

        if ($input->getOption('laravelpwa')) {
            $this->requireComposerPackages(['silviolleite/laravelpwa'], $output, true);

            $commands = array_filter([
                $this->phpBinary() . ' artisan vendor:publish --provider="LaravelPWA\Providers\LaravelPWAServiceProvider"',
                exec('find . -name "app.blade.php" -exec sed -i \'\' \'s+<head>+<head>        @laravelPWA+g\' {} \;')
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

    protected function verifyApplicationDoesntExist($directory, InputInterface $input, OutputInterface $output)
    {
        if ((is_dir($directory) || is_file($directory)) && $directory != getcwd()) {
            throw new RuntimeException('Application already exists! ' . __FILE__ . ':' . __LINE__);
        }
    }

    protected function getVersion(InputInterface $input)
    {
        if ($input->getOption('dev')) {
            return 'dev-master';
        }

        return '';
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



    private function debug($task, $input, $output)
    {

        // if get option debug is passed, show debug info
        if ($input->getOption('debug')) {
            $output->writeln(
                "<bg=blue;fg=black>$task</> " .
                    '<fg=blue>' .
                    __FILE__ . ':' . __LINE__ .
                    '</>'
                    . PHP_EOL,
                OutputInterface::VERBOSITY_VERBOSE
            );
        }
    }
}
