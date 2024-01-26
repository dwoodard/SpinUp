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
  private $allPackages;

  public function __construct()
  {
    parent::__construct();

    $this->allPackages = collect([
      // Friendly name (Pascel) => composer package with args
      'LaravelBreeze' => ['laravel/breeze', 'prefer-dist'],
      'LaravelPWA' => ['silviolleite/laravelpwa', 'prefer-dist'],
      'LaravelSchemalessAttributes' => ['spatie/laravel-schemaless-attributes'],
      'LaravelPermission' => ['spatie/laravel-permission'],
      'LaravelMedialibrary' => ['spatie/laravel-medialibrary'],
    ]);
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

      //add laradock
      ->addOption('laradock', null, InputOption::VALUE_NONE, 'Installs the Laradock scaffolding (in project)')

      //composer packages to add
      ->addOption('all', null, InputOption::VALUE_NONE, 'Installs all the Composer packages');

    // // loop through allPackages and add an option for each one
    // $this->allPackages->each(function ($value, $key) {
    //   $this->addOption($key, null, InputOption::VALUE_NONE, 'Installs the ' . $key . ' package');
    // });
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
      PHP_EOL .
        '  <fg=green>
  ┏┓  •  ┳┳  
  ┗┓┏┓┓┏┓┃┃┏┓
  ┗┛┣┛┗┛┗┗┛┣┛   
    ┛      ┛</>
'
        // show name of the project if passed as an argument
        . ($input->getArgument('name') ? ' - Name: <options=bold>' . $input->getArgument('name') . '</>' . PHP_EOL : '')

        . ' - Project directory: <options=bold>' . getcwd() . '/' . $input->getArgument('name') . '</>' . PHP_EOL
        . ($input->getOption('laradock') ? ' - Laradock: <options=bold>Yes</>' . PHP_EOL : '')
        // all
        . ($input->getOption('all') ? ' - All Packages: <options=bold>Yes</>' . PHP_EOL : '')



    );

    // Set the default name if it is not passed as an argument... 
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

    // Install Laradock
    if (!$input->getOption('laradock')) {
      $input->setOption('laradock', confirm(
        label: 'Would you like to install Laradock?',
        default: false,
      ));
    }


    /* 
      if all is selected, then set all the options to true
      else, prompt for each option

    */


    if ($input->getOption('all')) {
      $this->allPackages->each(function ($value, $key) use ($input, $output) {
        $output->writeln($key);
      });
    } else {
      $this->allPackages->each(function ($value, $key) use ($input) {
        if (!$input->getOption($key)) {
          $input->setOption($key, confirm(
            label: 'Would you like to install ' . $key . '?',
            default: false,
          ));
        }
      });
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

    // $output in Yellow Excuting... filename with line number
    $output->writeln('  <bg=yellow;fg=black> Excuting... </>' . PHP_EOL);
    $output->writeln('  <fg=yellow;options=bold>File:</> ' . __FILE__  . ":" . __LINE__ . PHP_EOL);

    // $this->validateStackOption($input);
    $name = $input->getArgument('name');
    $directory = $name !== '.' ? getcwd() . '/' . $name : '.';
    $output->writeln($directory);
    $this->composer = new Composer(new Filesystem(), $directory);
    // $version = $this->getVersion($input);

    if (!$input->getOption('force')) {
      $this->verifyApplicationDoesntExist($directory);
    }

    if ($input->getOption('force') && $directory === '.') {
      throw new RuntimeException('Cannot use --force option when using current directory for installation!');
    }
    $composer = $this->findComposer();
    $commands = [
      // $composer . " create-project laravel/laravel \"$directory\" $version --remove-vcs --prefer-dist",
      $composer . " create-project laravel/laravel \"$directory\" $version --remove-vcs --prefer-dist --quiet",
    ];

    if ($directory != '.' && $input->getOption('force')) {
      if (PHP_OS_FAMILY == 'Windows') {
        array_unshift($commands, "(if exist \"$directory\" rd /s /q \"$directory\")");
      } else {
        array_unshift($commands, "rm -rf \"$directory\"");
      }
    }

    if (PHP_OS_FAMILY != 'Windows') {
      $commands[] = "chmod 755 \"$directory/artisan\"";
    }

    if (($process = $this->runCommands($commands, $input, $output))->isSuccessful()) {
      if ($name !== '.') {
        $this->replaceInFile(
          'APP_URL=http://localhost',
          'APP_URL=' . $this->generateAppUrl($name),
          $directory . '/.env'
        );

        [$database, $migrate] = $this->promptForDatabaseOptions($directory, $input);


        $this->configureDefaultDatabaseConnection($directory, $database, $name, $migrate);

        // prompt for composer packages
        // $this->configureComposerPackages($input, $output);


        if ($migrate) {
          $this->runCommands([
            $this->phpBinary() . ' artisan migrate',
          ], $input, $output, workingPath: $directory);
        }
      }

      // if ($input->getOption('git') || $input->getOption('github') !== false) {
      //   $this->createRepository($directory, $input, $output);
      // }

      if ($input->getOption('laradock')) {
        $this->installLaradock($directory, $input, $output);
      }

      // if ($input->getOption('github') !== false) {
      //   $this->pushToGitHub($name, $directory, $input, $output);
      //   $output->writeln('');
      // }

      $output->writeln("  <bg=blue;fg=white> INFO </> Application ready in <options=bold>[{$name}]</>. You can start your local development using:" . PHP_EOL);

      $output->writeln('<fg=gray>➜</> <options=bold>cd ' . $name . '</>');
      $output->writeln('<fg=gray>➜</> <options=bold>php artisan serve</>');
      $output->writeln('');

      $output->writeln('');
    }

    return $process->getExitCode();
  }

  /**
   * Return the local machine's default Git branch if set or default to `main`.
   *
   * @return string
   */
  protected function defaultBranch()
  {
    $process = new Process(['git', 'config', '--global', 'init.defaultBranch']);

    $process->run();

    $output = trim($process->getOutput());

    return $process->isSuccessful() && $output ? $output : 'main';
  }

  /**
   * Configure the default database connection.
   *
   * @param  string  $directory
   * @param  string  $database
   * @param  string  $name
   * @param  bool  $migrate
   * @return void
   */
  protected function configureDefaultDatabaseConnection(string $directory, string $database, string $name, bool $migrate)
  {
    // MariaDB configuration only exists as of Laravel 11...
    if ($database === 'mariadb' && !$this->usingLaravel11OrNewer($directory)) {
      $database = 'mysql';
    }

    $this->pregReplaceInFile(
      '/DB_CONNECTION=.*/',
      'DB_CONNECTION=' . $database,
      $directory . '/.env'
    );

    $this->pregReplaceInFile(
      '/DB_CONNECTION=.*/',
      'DB_CONNECTION=' . $database,
      $directory . '/.env.example'
    );

    if ($database === 'sqlite') {
      $environment = file_get_contents($directory . '/.env');

      // If database options aren't commented, comment them for SQLite...
      if (!str_contains($environment, '# DB_HOST=127.0.0.1')) {
        $this->commentDatabaseConfigurationForSqlite($directory);

        return;
      }

      return;
    }

    // Any commented database configuration options should be uncommented when not on SQLite...
    $this->uncommentDatabaseConfiguration($directory);

    $defaultPorts = [
      'pgsql' => '5432',
      'sqlsrv' => '1433',
    ];

    if (isset($defaultPorts[$database])) {
      $this->replaceInFile(
        'DB_PORT=3306',
        'DB_PORT=' . $defaultPorts[$database],
        $directory . '/.env'
      );

      $this->replaceInFile(
        'DB_PORT=3306',
        'DB_PORT=' . $defaultPorts[$database],
        $directory . '/.env.example'
      );
    }

    $this->replaceInFile(
      'DB_DATABASE=laravel',
      'DB_DATABASE=' . str_replace('-', '_', strtolower($name)),
      $directory . '/.env'
    );

    $this->replaceInFile(
      'DB_DATABASE=laravel',
      'DB_DATABASE=' . str_replace('-', '_', strtolower($name)),
      $directory . '/.env.example'
    );
  }

  /**
   * Determine if the application is using Laravel 11 or newer.
   *
   * @param  string  $directory
   * @return bool
   */
  public function usingLaravel11OrNewer(string $directory): bool
  {
    $version = json_decode(file_get_contents($directory . '/composer.json'), true)['require']['laravel/framework'];
    $version = str_replace('^', '', $version);
    $version = explode('.', $version)[0];

    return $version >= 11;
  }

  /**
   * Comment the irrelevant database configuration entries for SQLite applications.
   *
   * @param  string  $directory
   * @return void
   */
  protected function commentDatabaseConfigurationForSqlite(string $directory): void
  {
    $defaults = [
      'DB_HOST=127.0.0.1',
      'DB_PORT=3306',
      'DB_DATABASE=laravel',
      'DB_USERNAME=root',
      'DB_PASSWORD=',
    ];

    $this->replaceInFile(
      $defaults,
      collect($defaults)->map(fn ($default) => "# {$default}")->all(),
      $directory . '/.env'
    );

    $this->replaceInFile(
      $defaults,
      collect($defaults)->map(fn ($default) => "# {$default}")->all(),
      $directory . '/.env.example'
    );
  }

  /**
   * Uncomment the relevant database configuration entries for non SQLite applications.
   *
   * @param  string  $directory
   * @return void
   */
  protected function uncommentDatabaseConfiguration(string $directory)
  {
    $defaults = [
      '# DB_HOST=127.0.0.1',
      '# DB_PORT=3306',
      '# DB_DATABASE=laravel',
      '# DB_USERNAME=root',
      '# DB_PASSWORD=',
    ];

    $this->replaceInFile(
      $defaults,
      collect($defaults)->map(fn ($default) => substr($default, 2))->all(),
      $directory . '/.env'
    );

    $this->replaceInFile(
      $defaults,
      collect($defaults)->map(fn ($default) => substr($default, 2))->all(),
      $directory . '/.env.example'
    );
  }

  /**
   * Install Laravel Breeze into the application.
   *
   * @param  string  $directory
   * @param  \Symfony\Component\Console\Input\InputInterface  $input
   * @param  \Symfony\Component\Console\Output\OutputInterface  $output
   * @return void
   */
  protected function installBreeze(string $directory, InputInterface $input, OutputInterface $output)
  {
    $commands = array_filter([
      $this->findComposer() . ' require laravel/breeze',
      trim(sprintf(
        $this->phpBinary() . ' artisan breeze:install %s %s %s %s %s',
        $input->getOption('stack'),
        $input->getOption('typescript') ? '--typescript' : '',
        $input->getOption('pest') ? '--pest' : '',
        $input->getOption('dark') ? '--dark' : '',
        $input->getOption('ssr') ? '--ssr' : '',
      )),
    ]);

    $this->runCommands($commands, $input, $output, workingPath: $directory);

    $this->commitChanges('Install Breeze', $directory, $input, $output);
  }



  /**
   * Determine the default database connection.
   *
   * @param  string  $directory
   * @param  \Symfony\Component\Console\Input\InputInterface  $input
   * @return string
   */
  protected function promptForDatabaseOptions(string $directory, InputInterface $input)
  {
    // Laravel 11.x appliations use SQLite as default...
    $defaultDatabase = $this->usingLaravel11OrNewer($directory) ? 'sqlite' : 'mysql';

    if ($input->isInteractive()) {
      $database = select(
        label: 'Which database will your application use?',
        options: [
          'mysql' => 'MySQL',
          'mariadb' => 'MariaDB',
          'pgsql' => 'PostgreSQL',
          'sqlite' => 'SQLite',
          'sqlsrv' => 'SQL Server',
        ],
        default: $defaultDatabase
      );

      if ($this->usingLaravel11OrNewer($directory) && $database !== $defaultDatabase) {
        $migrate = confirm(label: 'Default database updated. Would you like to run the default database migrations?', default: true);
      }
    }

    return [$database ?? $defaultDatabase, $migrate ?? false];
  }









  /**
   * Install Pest into the application.
   *
   * @param  \Symfony\Component\Console\Input\InputInterface  $input
   * @param  \Symfony\Component\Console\Output\OutputInterface  $output
   * @return void
   */
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

  protected function installLaradock(string $directory, InputInterface $input, OutputInterface $output)
  {
    // install laradock


    $commands = array_filter([
      'git clone https://github.com/Laradock/laradock.git laradock',
      'mkdir data',
      'cd laradock',
      'pwd',
      'cp .env.example .env',
      'sed -i \'\' \'s+DATA_PATH_HOST=~/.laradock/data+DATA_PATH_HOST=../data+g\' .env',
      'sed -i \'\' \'s:DB_HOST=127.0.0.1:DB_HOST=mysql:g\' .env',
      'sed -i \'\' \'s:REDIS_HOST=127.0.0.1:REDIS_HOST=redis:g\' .env',
      'sed -i \'\' \'s:DB_PASSWORD=.*:DB_PASSWORD=root:g\' .env',
      'echo \'QUEUE_HOST=beanstalkd\' >> .env',
      'cd ..'
    ]);
  }


  /**
   * Select Composer packages to install.
   * 
   */
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



  /**
   * Create a Git repository and commit the base Laravel skeleton.
   *
   * @param  string  $directory
   * @param  \Symfony\Component\Console\Input\InputInterface  $input
   * @param  \Symfony\Component\Console\Output\OutputInterface  $output
   * @return void
   */
  protected function createRepository(string $directory, InputInterface $input, OutputInterface $output)
  {
    $branch = $input->getOption('branch') ?: $this->defaultBranch();

    $commands = [
      'git init -q',
      'git add .',
      'git commit -q -m "Set up a fresh Laravel app"',
      "git branch -M {$branch}",
    ];

    $this->runCommands($commands, $input, $output, workingPath: $directory);
  }

  /**
   * Commit any changes in the current working directory.
   *
   * @param  string  $message
   * @param  string  $directory
   * @param  \Symfony\Component\Console\Input\InputInterface  $input
   * @param  \Symfony\Component\Console\Output\OutputInterface  $output
   * @return void
   */
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

  /**
   * Create a GitHub repository and push the git log to it.
   *
   * @param  string  $name
   * @param  string  $directory
   * @param  \Symfony\Component\Console\Input\InputInterface  $input
   * @param  \Symfony\Component\Console\Output\OutputInterface  $output
   * @return void
   */
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

  /**
   * Verify that the application does not already exist.
   *
   * @param  string  $directory
   * @return void
   */
  protected function verifyApplicationDoesntExist($directory)
  {
    if ((is_dir($directory) || is_file($directory)) && $directory != getcwd()) {
      throw new RuntimeException('Application already exists!');
    }
  }

  /**
   * Generate a valid APP_URL for the given application name.
   *
   * @param  string  $name
   * @return string
   */
  protected function generateAppUrl($name)
  {
    $hostname = mb_strtolower($name) . '.test';

    return $this->canResolveHostname($hostname) ? 'http://' . $hostname : 'http://localhost';
  }

  /**
   * Determine whether the given hostname is resolvable.
   *
   * @param  string  $hostname
   * @return bool
   */
  protected function canResolveHostname($hostname)
  {
    return gethostbyname($hostname . '.') !== $hostname . '.';
  }

  /**
   * Get the version that should be downloaded.
   *
   * @param  \Symfony\Component\Console\Input\InputInterface  $input
   * @return string
   */
  protected function getVersion(InputInterface $input)
  {
    if ($input->getOption('dev')) {
      return 'dev-master';
    }

    return '';
  }

  /**
   * Get the composer command for the environment.
   *
   * @return string
   */
  protected function findComposer()
  {
    return implode(' ', $this->composer->findComposer());
  }

  /**
   * Get the path to the appropriate PHP binary.
   *
   * @return string
   */
  protected function phpBinary()
  {
    $phpBinary = (new PhpExecutableFinder)->find(false);

    return $phpBinary !== false
      ? ProcessUtils::escapeArgument($phpBinary)
      : 'php';
  }

  /**
   * Install the given Composer Packages into the application.
   *
   * @return bool
   */
  protected function requireComposerPackages(array $packages, OutputInterface $output, bool $asDev = false)
  {
    return $this->composer->requirePackages($packages, $asDev, $output);
  }

  /**
   * Remove the given Composer Packages from the application.
   *
   * @return bool
   */
  protected function removeComposerPackages(array $packages, OutputInterface $output, bool $asDev = false)
  {
    return $this->composer->removePackages($packages, $asDev, $output);
  }

  /**
   * Run the given commands.
   *
   * @param  array  $commands
   * @param  \Symfony\Component\Console\Input\InputInterface  $input
   * @param  \Symfony\Component\Console\Output\OutputInterface  $output
   * @param  string|null  $workingPath
   * @param  array  $env
   * @return \Symfony\Component\Process\Process
   */
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

  /**
   * Replace the given file.
   *
   * @param  string  $replace
   * @param  string  $file
   * @return void
   */
  protected function replaceFile(string $replace, string $file)
  {
    $stubs = dirname(__DIR__) . '/stubs';

    file_put_contents(
      $file,
      file_get_contents("$stubs/$replace"),
    );
  }

  /**
   * Replace the given string in the given file.
   *
   * @param  string|array  $search
   * @param  string|array  $replace
   * @param  string  $file
   * @return void
   */
  protected function replaceInFile(string|array $search, string|array $replace, string $file)
  {
    file_put_contents(
      $file,
      str_replace($search, $replace, file_get_contents($file))
    );
  }

  /**
   * Replace the given string in the given file using regular expressions.
   *
   * @param  string|array  $search
   * @param  string|array  $replace
   * @param  string  $file
   * @return void
   */
  protected function pregReplaceInFile(string $pattern, string $replace, string $file)
  {
    file_put_contents(
      $file,
      preg_replace($pattern, $replace, file_get_contents($file))
    );
  }
}
