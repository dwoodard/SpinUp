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

use function Laravel\Prompts\confirm; // confirm('Would you like to install Laradock?');
use function Laravel\Prompts\multiselect; // multiselect('Which features would you like to install?', $this->features, $this->features);
use function Laravel\Prompts\text; // text('What is your name?', 'John Doe');
use function Laravel\Prompts\select; // select('What is your favorite color?', ['red', 'blue', 'green'], 'blue');

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
    private $projectDirectory;

    // Features must follow the naming convention of FEATURE_[NAME_OF_FEATURE]
    private $features = [
        'FEATURE_LARAVEL_PWA',
        'FEATURE_LARAVEL_PERMISSION',
        'FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES',
        'FEATURE_LARAVEL_TELESCOPE',
        'FEATURE_VENTURECRAFT_REVISIONABLE'
        // 'FEATURE_LARAVEL_CASHIER',
        // 'FEATURE_HEADLESS_UI',
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
        $this->name = $input->getArgument('name');
        $this->projectDirectory =  ($this->name === '.') ? getcwd() : getcwd() . '/' . $this->name;
        $this->composer = new Composer(new Filesystem(), $this->projectDirectory);

        /*  */
        $this->handleIfExsistingProject($input, $output);
        $this->installLaravel($input, $output);


        $this->installBreeze($input, $output, $this->projectDirectory);

        $this->installLaradock($input, $output);

        $this->installFeatures($input, $output);

        $this->installNpmPackages($input, $output);

        $this->installStubs($input, $output);

        $this->updatePackageFile($input, $output);

        $this->installLayout($input, $output);

        $this->installSetup($input, $output);

        $this->ShowHowProjectRuns($input, $output);

        return 0; //return 0 if everything is successful
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

        //  -f, --force
        // if -f is passed, delete the project if it exists
        if (!$input->getOption('force')) {
            $this->verifyApplicationDoesntExist($this->projectDirectory, $input, $output);
        }
        // if not -f is passed, ask if they want to delete the project
        else {
            // -f is passed, delete the project if it exists
            $commands = [
                'rm -rf ' . $this->projectDirectory,
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

        $quite = $input->getOption('laravel-quiet') ? '--quiet' : '';

        $this->timeLineOutput(true, $output, 'Installing Laravel...');

        $commands = [
            "composer create-project $quite laravel/laravel $this->projectDirectory  --remove-vcs --prefer-dist",
        ];

        $this->runCommands($commands, $input, $output);

        // replace last output line with a green checkmark
        $this->timeLineOutput(true, $output, 'Installing Laravel...', "✅ done");

        $this->commitGitProject($input, $output, 'Initial commit', true);
    }



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


        $this->timeLineOutput(false, $output, 'Installing Laradock...');
        $titleCaseName = ucwords($this->name);
        $laravelCommands = array_filter([
            "git clone https://github.com/Laradock/laradock.git laradock >/dev/null 2>&1",
            "echo '/data/' >> .gitignore",
            'rm -rf laradock/.git',
            // this will run form the root directory
            "sed -i '' 's/^DB_HOST=127.0.0.1/DB_HOST=mysql/g' .env",
            "sed -i '' 's/^DB_DATABASE=laravel/DB_DATABASE=default/g' .env",
            "sed -i '' 's/^DB_USERNAME=root/DB_USERNAME=root/g' .env",
            "sed -i '' 's/^DB_PASSWORD=/DB_PASSWORD=root/g' .env",
            "sed -i '' 's/^REDIS_HOST=.*/REDIS_HOST=redis/g' .env",

            "sed -i '' 's/^APP_NAME=Laravel/APP_NAME=\"$titleCaseName\"/g' .env",
        ]);

        $process = $this->runCommands(
            $laravelCommands,
            $input,
            $output,
            workingPath: $this->projectDirectory,
        );

        $process->isSuccessful() ?
            $this->timeLineOutput(true, $output, 'Installing Laradock...', "✅ done") :
            $this->timeLineOutput(true, $output, 'Installing Laradock...', "❌ failed");

        // now that it is cloned, we can run command in the directory

        $laradockCommands = array_filter([
            'cp .env.example .env',
            'sed -i "" "s+DATA_PATH_HOST=~/.laradock/data+DATA_PATH_HOST=../data+g" .env',
        ]);

        $process = $this->runCommands(
            $laradockCommands,
            $input,
            $output,
            workingPath: $this->projectDirectory . '/laradock',
        );

        $this->commitGitProject($input, $output, 'Install Laradock');
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
            $this->timeLineOutput(true, $output, 'Installing Breeze...', "✅ done") :
            $this->timeLineOutput(true, $output, 'Installing Breeze...', "❌ failed");

        $this->commitGitProject($input, $output, 'Install Breeze');
    }

    private function commitGitProject(InputInterface $input, OutputInterface $output, string $message = "", bool $init = false)
    {

        $commands = [];

        if ($init) {

            $commands[] = "git init";
            $message = $message ?: "Initial commit";
        }
        $quite = ">/dev/null 2>&1";
        $commands[] = "git add . $quite";
        $commands[] = "git commit -m '$message' $quite";

        $this->runCommands($commands, $input, $output, workingPath: $this->projectDirectory);

        // $this->timeLineOutput(true, $output, 'Initializing Git...',  "✅ done");
    }



    private function ShowHowProjectRuns(InputInterface $input, OutputInterface $output)
    {

        $output->writeln(PHP_EOL . '<bg=green>       Run Project       </> ' .  PHP_EOL);

        $output->writeln(
            PHP_EOL .
                "cd $this->projectDirectory && ./deploy.sh seed"
                . PHP_EOL
        );
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
      | Install Layout Templates
      |--------------------------------------------------------------------------
          Using Breeze as a base, we can install other packages that
          are commonly used in Laravel projects. These include
          Laravel PWA, Laravel Schemaless Attributes,

          Tailwind CSS, Vue Components, Headless UI, Etc.

          we'll move dashboard to a set of admin routes, and then we'll have a set of
          - remove from routes/web.php
          - copy stubs/routes/admin.php to routes/admin.php
          - copy stubs/resources/views/admin to resources/views/admin
      */
    private function installLayout(InputInterface $input, OutputInterface $output)
    {

        // remove the welcome.blade.php file
        $commands = [
            //remove the resources/views/welcome.blade.php file
            "rm -rf $this->projectDirectory/resources/views/welcome.blade.php",
        ];
        $this->runCommands($commands, $input, $output);

        $this->timeLineOutput(false, $output, 'Installing Template...');

        /* stubs/resources/views/app.blade.php */

        $this->commitGitProject($input, $output, 'Install Layout');
    }

    private function installNpmPackages(InputInterface $input, OutputInterface $output)
    {


        $commands = [
            'npm i @headlessui/vue',
            'npm i @heroicons/vue',

            'npm i @vueuse/core',
            'npm i @vueuse/components',
            'npm i @vueuse/motion',

            'npm i radix-vue',
            'npm i @radix-icons/vue',

            'npm i prettier --save-dev',
            'npm i eslint --save-dev',
            'npm i eslint-plugin-vue@latest --save-dev',
            'npm i prettier-eslint --save-dev',
            'npm i vue-eslint-parser --save-dev',

            'npm i @iconify/vue --save-dev',

            'npm i tailwindcss-animate --save-dev',
            'npm i radix-vue',
            'npm i clsx',
            'npm i tailwind-merge',

            // 'npm i vite-plugin-vue-devtools --save-dev',
         ];

        $this->runCommands($commands, $input, $output, workingPath: $this->projectDirectory);

        $this->commitGitProject($input, $output, 'Install Npm Packages');
    }

    /*
      |--------------------------------------------------------------------------
      | Stubs
      |--------------------------------------------------------------------------
      | Copy stub files from the stubs directory to the project directory.
      */
    private function installStubs(InputInterface $input, OutputInterface $output)
    {
        $this->timeLineOutput(false, $output, 'Installing Stubs...');

        // create Filesystem object
        $filesystem = new Filesystem();

        // get list of features (in in array, its toggled on)
        $features = collect($input->getOption('features'));
        // echo $features->join(PHP_EOL) . PHP_EOL;

        // get all the files in the stubs directory
        $filePaths = collect($filesystem->allFiles(dirname(__DIR__) . '/stubs', true))->map(
            fn ($file) => ($file->getPathname())
        );
        // echo $filePaths->join(PHP_EOL) . PHP_EOL;



        function removeMarkersFromStub($content, $features)
        {
            // for each line in the file
            $result = '';
            $hasFeature = false; // use to check if the feature is toggled on
            $inFeature = false; // use to check if we are in the block of a feature (between the markers START and END)
            $lines = explode(PHP_EOL, $content); // convert the content to an array of lines
            $deleteBlock = false; // use to check if we should delete the block of the feature

            foreach ($lines as $key => $line) {
                // we'll use a negative approach to skip line or add it to the result

                /*
                            this could happen where the feature is set but not in the array
                            # FEATURE_WHERE_TAG_DOES_NOT_EXIST:START
                            # My flags should be deleted regardless of the tag because they are not in the array
                            # FEATURE_WHERE_TAG_DOES_NOT_EXIST:END

                            when this is the case, we should delete the lines until the end of the block
                            call this: deleteBlock
                        */

                // is the line a feature or in the block of a feature
                if (preg_match('/(FEATURE_.*):START.*/', $line, $matches)) {
                    $hasFeature = $features->contains($matches[1]);
                    $inFeature = true; // use to check if we are in the block of a feature

                    if ($hasFeature) {
                        $deleteBlock = false;
                        $inFeature = true;
                        continue;
                    } else {
                        $deleteBlock = true;
                        $inFeature = true;
                        $hasFeature = false;
                        continue;
                    }
                }

                // check if the line is the end of the block,
                // if so, set deleteBlock to false and continue

                if (preg_match('/(FEATURE_.*):END.*/', $line, $matches)) {
                    $deleteBlock = false;
                    $inFeature = false;
                    $hasFeature = false;

                    continue;
                }

                if ($deleteBlock && $inFeature) {
                    continue;
                }

                // is this the last line, if so, don't add a new line
                $result .= $key === count($lines) - 1 ?
                    $line :
                    $line . PHP_EOL;
            }
            return $result;
        }

        function saveContentToNewDestination($stubFilePath, $fileContentModified, $projectPath)
        {
            // we need to take the $contents and save it to the new destination (root or sub directory)



            // check if the directory exists for the file
            // if not, create it
            if (!file_exists(dirname($projectPath))) {
                mkdir(dirname($projectPath), 0755, true);
            }

            // Copy the file to the project directory
            if (copy($stubFilePath, $projectPath)) {
                // Set the permissions to 0755
                chmod($projectPath, 0755);
            }

            // use fileContentModified to save the content to the new destination
            file_put_contents($projectPath, $fileContentModified);

            return file_exists($projectPath);
        }

        // loop through the files
        foreach ($filePaths as $stubFilePath) {

            if (!$input->getOption('debug') && str_contains($stubFilePath, '__example__')) {
                continue;
            }

            // get the contents of the file
            $contents = file_get_contents($stubFilePath);

            // echo $stubFilePath . PHP_EOL;
            $contents = removeMarkersFromStub($contents, $features);

            $destinationPath = str_contains($stubFilePath, '/stubs/root/') ?
                $this->projectDirectory . '/' . str_replace('/stubs/root/', '', str_replace(dirname(__DIR__), '', $stubFilePath)) :
                $this->projectDirectory . str_replace('/stubs', '', str_replace(dirname(__DIR__), '', $stubFilePath));

            $copied = saveContentToNewDestination($stubFilePath, $contents, $destinationPath);

            $stubFilePath = str_replace(dirname(__DIR__) . '/stubs/', '', $stubFilePath);
            $destinationPath = str_replace($this->projectDirectory . '/', '', $destinationPath);
            $fromTo = "Copied: $stubFilePath" .  " =>: $destinationPath";

            echo ($copied ? "✅" : "❌") . " $fromTo" . PHP_EOL;

            // $this->timeLineOutput(true, $output, 'Installing Stubs...',  "✅ done");
        }

        $this->commitGitProject($input, $output, 'Install Stubs');
    }

    private function updatePackageFile(InputInterface $input, OutputInterface $output)
    {

        $this->timeLineOutput(false, $output, 'Updating package.json...');

        // Read the JSON file
        $jsonFile = $this->projectDirectory . '/package.json';
        $jsonContent = file_get_contents($jsonFile);

        // Parse JSON into array
        $config = json_decode($jsonContent, true);

        // Find and modify the desired key
        if (isset($config['scripts']['dev']) && $config['scripts']['dev'] === 'vite') {
            $config['scripts']['dev'] = 'vite --mode development';
            $config['scripts']['prod'] = 'vite';
        }

        // Write the modified array back to the JSON file
        file_put_contents($jsonFile, json_encode($config, JSON_PRETTY_PRINT));

        $this->commitGitProject($input, $output, 'Update package.json');

        $this->timeLineOutput(true, $output, 'Updating package.json...', "✅ done");
    }

    private function installSetup(InputInterface $input, OutputInterface $output)
    {
        $commands = [
            'php artisan key:generate',
            'php artisan ziggy:generate',
            'npm install',
            'npm run build',
            './deploy.sh seed',
            'vite --mode development',
        ];
        $this->runCommands($commands, $input, $output, workingPath: $this->projectDirectory);

        $this->commitGitProject($input, $output, 'Install Setup');
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

        $output->writeln('  <bg=blue;fg=black>' . collect($input->getOption('features')) . '</> ' . PHP_EOL, OutputInterface::VERBOSITY_VERBOSE);

        // loop through the features and install them, if they are toggled on
        foreach ($input->getOption('features') as $feature) {
            $this->installFeature($feature, $input, $output)();
            $this->commitGitProject($input, $output, 'Install ' . $feature);
        }
    }

    protected function installFeature($feature, InputInterface $input, OutputInterface $output)
    {

        $installs = [
            "FEATURE_LARAVEL_PWA" => function () use ($input, $output) {
                $this->runCommands([
                    'composer require silviolleite/laravelpwa --prefer-dist',
                    'php artisan vendor:publish --provider="LaravelPWA\Providers\LaravelPWAServiceProvider"',
                ], $input, $output, workingPath: $this->projectDirectory);
            },

            "FEATURE_LARAVEL_PERMISSION" => function () use ($input, $output) {
                $this->runCommands([
                    "composer require spatie/laravel-permission",
                    // 'php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"',
                ], $input, $output, workingPath: $this->projectDirectory);
            },

            "FEATURE_LARAVEL_SCHEMALESS_ATTRIBUTES" => function () use ($input, $output) {
                $this->runCommands([
                    "composer require spatie/laravel-schemaless-attributes",
                ], $input, $output, workingPath: $this->projectDirectory);
            },

            "FEATURE_LARAVEL_CASHIER" => function () use ($input, $output) {
                $this->runCommands([
                    "composer require laravel/cashier",
                ], $input, $output, workingPath: $this->projectDirectory);
            },
            "FEATURE_LARAVEL_TELESCOPE" => function () use ($input, $output) {
                $this->runCommands([
                    "composer require laravel/telescope ",
                    //wait for the composer require to finish before running the next command
                    'while [ ! -d "vendor/laravel/telescope" ]; do sleep 1; done',
                    'php artisan telescope:install',
                ], $input, $output, workingPath: $this->projectDirectory);
            },
            "FEATURE_VENTURECRAFT_REVISIONABLE" => function () use ($input, $output) {
                $this->runCommands([
                    "composer require venturecraft/revisionable",
                    'php artisan package:discover',
                    "php artisan vendor:publish --provider='Venturecraft\Revisionable\RevisionableServiceProvider'",
                ], $input, $output, workingPath: $this->projectDirectory);
            },

        ];

        return $installs[$feature];
    }



    /*
      |--------------------------------------------------------------------------
      | Helper Functions
      |--------------------------------------------------------------------------
      | These functions are used to help with the setup functions above.
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
    private function timeLineOutput($eraseLastLine, $output, $message = "Installing...", $status = 'in progress')
    {

        if ($eraseLastLine) {
            // move cursor up and erase line
            $output->write("\033[1A"); // Move up
            $output->write("\033[K"); // Erase line
        }

        $output->writeln("<bg=green;fg=black> $message </> $status");
    }

    protected function verifyApplicationDoesntExist($projectDirectory, InputInterface $input, OutputInterface $output)
    {

        $message = "Application already exists!

        If you want to install the application in this directory, use the --force option. ";

        if ((is_dir($projectDirectory) || is_file($projectDirectory)) && $projectDirectory != getcwd()) {
            throw new RuntimeException($message . __FILE__ . ':' . __LINE__);
        }
    }

    protected function phpBinary()
    {
        $phpBinary = (new PhpExecutableFinder())->find(false);

        return $phpBinary !== false
            ? ProcessUtils::escapeArgument($phpBinary)
            : 'php';
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

    private function clear()
    {
        /* some voodoo magic to clear the terminal */
        echo chr(27) . chr(91) . 'H' . chr(27) . chr(91) . 'J';
    }
}
