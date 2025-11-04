#!/bin/php
<?php
if (php_sapi_name() !== 'cli') {
    exit(0);
}

// --- Terminal styling helpers ---
class TerminalStyle {

    private const RESET     = "\033[0m";
    private const BOLD      = "\033[1m";
    private const UNDERLINE = "\033[4m";
    private const GREEN     = "\033[32m";
    private const RED       = "\033[31m";
    private const YELLOW    = "\033[33m";
    private const CYAN      = "\033[36m";
    private const GRAY      = "\033[90m";

    /**
     * Apply color to text
     * @param string $text
     * @param string $color
     * @return string
     */
    public static function color(string $text, string $color): string
    {
        return $color . $text . self::RESET;
    }

    /**
     * Apply bold style to text
     *
     * @param string $text
     * @return string
     */
    public static function bold(string $text): string 
    {
        return self::BOLD . $text . self::RESET;
    }

    /**
     * Apply underline style to text
     *
     * @param string $text
     * @return string
     */
    public static function underline(string $text): string
    {
        return self::UNDERLINE . $text . self::RESET;
    }

    /**
     * Generate a separator line
     *
     * @return string
     */
    public static function sep(): string
    {
        return self::color(str_repeat('─', 60), self::GRAY);
    }
}

// --- Enums for flags and build commands ---
enum Flag: string
{
    case NoComposer = '--no-composer';
    case Cleanup    = '--cleanup';
    case InstallNpm = '--install-npm';
    case Release    = '--release';
}

enum BuildCommand: string
{
    case ComposerInstall      = 'composer install --prefer-dist --no-progress --no-dev';
    case ComposerDumpAutoload = 'composer dump-autoload';
    case NpmCi                = 'npm ci --no-progress --no-audit';
    case NpmInstall           = 'npm install --no-progress --no-audit';
    case NpmRunBuild          = 'npm run build';
    case RemoveDist           = 'rm -rf ./dist';
}

// --- Argument parser ---
class ArgvParser {
    public array $flags = [];
    public function __construct(private array $argv) {
        foreach ($argv as $arg) {
            foreach (Flag::cases() as $flag) {
                if ($arg === $flag->value) {
                    $this->flags[$flag->name] = $flag;
                }
            }
        }
    }
    public function has(Flag $flag): bool {
        return isset($this->flags[$flag->name]);
    }
}

// --- Build step ---
class BuildStep {
    public function __construct(
        public BuildCommand|string $command,
        public ?string $description = null
    ) {}
    public function run(string $dirName): int {
        print TerminalStyle::sep() . PHP_EOL;
        print TerminalStyle::bold(TerminalStyle::color("➤ Running: '{$this->command}'", TerminalStyle::CYAN)) . " for $dirName\n";
        $timeStart = microtime(true);
        $exitCode = ShellExecutor::run($this->command);
        $buildTime = round(microtime(true) - $timeStart);
        if ($exitCode === 0) {
            print TerminalStyle::color("✔ Success", TerminalStyle::GREEN) . " ";
        } else {
            print TerminalStyle::color("✖ Failed", TerminalStyle::RED) . " ";
        }
        print TerminalStyle::color("({$buildTime}s)", TerminalStyle::YELLOW) . PHP_EOL;
        print TerminalStyle::sep() . PHP_EOL . PHP_EOL;
        return $exitCode;
    }
}

// --- Shell executor ---
class ShellExecutor {
    public static function run(string $command): int {
        $fullCommand = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            ? "cmd /v:on /c \"$command 2>&1 & echo Exit status : !ErrorLevel!\""
            : "$command 2>&1 ; echo Exit status : $?";
        $proc = popen($fullCommand, 'r');
        $completeOutput = '';
        while (!feof($proc)) {
            $liveOutput = fread($proc, 4096);
            $completeOutput .= $liveOutput;
            print $liveOutput;
            @flush();
        }
        pclose($proc);
        preg_match('/[0-9]+$/', $completeOutput, $matches);
        return intval($matches[0]);
    }
}

// --- Cleaner ---
class Cleaner {
    public function __construct(private array $removables, private string $dirName) {}
    public function clean(): void {
        print TerminalStyle::sep() . PHP_EOL;
        print TerminalStyle::bold(TerminalStyle::color("🧹 Cleanup started...", TerminalStyle::YELLOW)) . PHP_EOL;
        foreach ($this->removables as $removable) {
            if (file_exists($removable)) {
                print TerminalStyle::color("Removing $removable from {$this->dirName}", TerminalStyle::GRAY) . PHP_EOL;
                shell_exec("rm -rf $removable");
            }
        }
        print TerminalStyle::bold(TerminalStyle::color("🧹 Cleanup finished.", TerminalStyle::GREEN)) . PHP_EOL;
        print TerminalStyle::sep() . PHP_EOL;
    }
}

// --- Build runner ---
class BuildRunner {
    private array $steps = [];
    public function __construct(private ArgvParser $args) {}
    public function prepareSteps(): void {
        // Composer
        if (file_exists('composer.json')) {
            if (!$this->args->has(Flag::NoComposer)) {
                $this->steps[] = new BuildStep(BuildCommand::ComposerInstall, 'Composer install');
            }
            $this->steps[] = new BuildStep(BuildCommand::ComposerDumpAutoload, 'Composer dump-autoload');
        }
        // NPM
        if (file_exists('package.json')) {
            $npmPackage = json_decode(file_get_contents('package.json'));
            if (file_exists('package-lock.json')) {
                if (!$this->args->has(Flag::InstallNpm)) {
                    $this->steps[] = new BuildStep(BuildCommand::NpmCi, 'NPM ci');
                    $this->steps[] = new BuildStep(BuildCommand::NpmRunBuild, 'NPM build');
                } else {
                    $this->steps[] = new BuildStep("npm install $npmPackage->name", 'NPM install package');
                    $this->steps[] = new BuildStep(BuildCommand::RemoveDist, 'Remove dist');
                    $this->steps[] = new BuildStep("mv node_modules/$npmPackage->name/dist ./", 'Move dist');
                }
            } else {
                if (!$this->args->has(Flag::InstallNpm)) {
                    $this->steps[] = new BuildStep(BuildCommand::NpmInstall, 'NPM install');
                    $this->steps[] = new BuildStep(BuildCommand::NpmRunBuild, 'NPM build');
                } else {
                    $this->steps[] = new BuildStep("npm install $npmPackage->name", 'NPM install package');
                    $this->steps[] = new BuildStep(BuildCommand::RemoveDist, 'Remove dist');
                    $this->steps[] = new BuildStep("mv node_modules/$npmPackage->name/dist ./", 'Move dist');
                }
            }
        }
    }
    public function run(): void {
        $dirName = basename(dirname(__FILE__));
        foreach ($this->steps as $step) {
            $exitCode = $step->run($dirName);
            if ($exitCode > 0) {
                exit($exitCode);
            }
        }
    }
}

// --- Main Entrypoint ---
function main(array $argv) {
    $args = new ArgvParser($argv);
    $runner = new BuildRunner($args);
    $runner->prepareSteps();
    $runner->run();

    // Removables
    $removables = [
        '.gitignore','.github','.gitattributes','build.php','build.js','.npmrc',
        'composer.lock','env-example','webpack.config.js','package-lock.json','package.json',
        'phpunit.xml.dist','README.md','./node_modules/','./source/sass/','./source/js/',
        'LICENSE','babel.config.js','yarn.lock','.devcontainer',
    ];
    if (!$args->has(Flag::Release)) {
        $removables[] = '.git';
    }
    $dirName = basename(dirname(__FILE__));
    if ($args->has(Flag::Cleanup)) {
        (new Cleaner($removables, $dirName))->clean();
    }
}

main($argv);
