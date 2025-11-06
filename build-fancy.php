#!/bin/php
<?php
if (php_sapi_name() !== 'cli') {
    exit(0);
}

// --- Terminal styling helpers ---
class TerminalStyle
{

    public const RESET     = "\033[0m";
    public const BOLD      = "\033[1m";
    public const UNDERLINE = "\033[4m";
    public const GREEN     = "\033[32m";
    public const RED       = "\033[31m";
    public const YELLOW    = "\033[33m";
    public const CYAN      = "\033[36m";
    public const GRAY      = "\033[90m";

    /**
     * Apply color to text
     * @param string $text
     * @param string $color
     * @return string
     */
    public static function color(string|BuildCommand $text, string $color): string
    {
        if (is_a($text, BuildCommand::class)) {
            $text = $text->value;
        }
        return $color . $text . self::RESET;
    }

    /**
     * Apply bold style to text
     *
     * @param string $text
     * @return string
     */
    public static function bold(string|BuildCommand $text): string 
    {
        if (is_a($text, BuildCommand::class)) {
            $text = $text->value;
        }
        return self::BOLD . $text . self::RESET;
    }

    /**
     * Apply underline style to text
     *
     * @param string $text
     * @return string
     */
    public static function underline(string|BuildCommand $text): string
    {
        if (is_a($text, BuildCommand::class)) {
            $text = $text->value;
        }
        return self::UNDERLINE . $text . self::RESET;
    }

    /**
     * Generate a separator line
     *
     * @return string
     */
    public static function sep(): string
    {
        return self::color(str_repeat('─', 100), self::GRAY);
    }
}

// --- Enums for flags and build commands ---
enum Flag: string
{
    case NoComposer = '--no-composer';
    case Cleanup    = '--cleanup';
    case InstallNpm = '--install-npm';
    case Release    = '--release';
    case DryRun     = '--dry-run';
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
        public string $command,
        public string $description,
        public ?string $meta = null
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
    public function __construct(private string $dirName) {}
    public function preview(): void {
        $distignorePath = './.distignore';
        $removables = [];
        if (file_exists($distignorePath)) {
            $removables = array_filter(array_map('trim', file($distignorePath)));
        }
        print TerminalStyle::sep() . PHP_EOL;
        print TerminalStyle::bold(TerminalStyle::color("Planned files to remove:", TerminalStyle::YELLOW)) . PHP_EOL;
        foreach ($removables as $removable) {
            print TerminalStyle::color("  $removable", TerminalStyle::GRAY) . PHP_EOL;
        }
        print TerminalStyle::sep() . PHP_EOL;
    }
    public function clean(): void {
        $distignorePath = './.distignore';
        $removables = [];
        if (file_exists($distignorePath)) {
            $removables = array_filter(array_map('trim', file($distignorePath)));
        }
        print TerminalStyle::sep() . PHP_EOL;
        print TerminalStyle::bold(TerminalStyle::color("🧹 Cleanup started...", TerminalStyle::YELLOW)) . PHP_EOL;
        foreach ($removables as $removable) {
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

    private function maybeConvertEnum($value): string {
        // PHP 8.1+ enum detection
        if (is_object($value) && enum_exists(get_class($value)) && property_exists($value, 'value')) {
            return $value->value;
        }
        return (string)$value;
    }

    /**
     * Prepare build steps
     */
    public function prepareSteps(): void {
        // Composer
        if (file_exists('composer.json')) {
            if (!$this->args->has(Flag::NoComposer)) {
                $this->steps[] = new BuildStep(
                    is_object(BuildCommand::ComposerInstall) && enum_exists(get_class(BuildCommand::ComposerInstall)) ? BuildCommand::ComposerInstall->value : (string)BuildCommand::ComposerInstall,
                    'Composer install'
                );
            }
            $this->steps[] = new BuildStep(
                is_object(BuildCommand::ComposerDumpAutoload) && enum_exists(get_class(BuildCommand::ComposerDumpAutoload)) ? BuildCommand::ComposerDumpAutoload->value : (string)BuildCommand::ComposerDumpAutoload,
                'Composer dump-autoload'
            );
        }
        // NPM
        if (file_exists('package.json')) {
            $npmPackage = json_decode(file_get_contents('package.json'));
            if (file_exists('package-lock.json')) {
                if (!$this->args->has(Flag::InstallNpm)) {
                    $this->steps[] = new BuildStep(
                        is_object(BuildCommand::NpmCi) && enum_exists(get_class(BuildCommand::NpmCi)) ? BuildCommand::NpmCi->value : (string)BuildCommand::NpmCi,
                        'Install NPM packages (ci)'
                    );
                    $this->steps[] = new BuildStep(
                        is_object(BuildCommand::NpmRunBuild) && enum_exists(get_class(BuildCommand::NpmRunBuild)) ? BuildCommand::NpmRunBuild->value : (string)BuildCommand::NpmRunBuild,
                        'Build NPM packages'
                    );
                } else {
                    $this->steps[] = new BuildStep("npm install $npmPackage->name", 'NPM install package: ' . $npmPackage->name);
                    $this->steps[] = new BuildStep(
                        is_object(BuildCommand::RemoveDist) && enum_exists(get_class(BuildCommand::RemoveDist)) ? BuildCommand::RemoveDist->value : (string)BuildCommand::RemoveDist,
                        'Remove dist folder'
                    );
                    $this->steps[] = new BuildStep("mv node_modules/$npmPackage->name/dist ./", 'Move dist folder');
                }
            } else {
                if (!$this->args->has(Flag::InstallNpm)) {
                    $this->steps[] = new BuildStep(
                        is_object(BuildCommand::NpmInstall) && enum_exists(get_class(BuildCommand::NpmInstall)) ? BuildCommand::NpmInstall->value : (string)BuildCommand::NpmInstall,
                        'Install NPM packages'
                    );
                    $this->steps[] = new BuildStep(
                        is_object(BuildCommand::NpmRunBuild) && enum_exists(get_class(BuildCommand::NpmRunBuild)) ? BuildCommand::NpmRunBuild->value : (string)BuildCommand::NpmRunBuild,
                        'Build NPM packages'
                    );
                } else {
                    $this->steps[] = new BuildStep("npm install $npmPackage->name", 'NPM install package: ' . $npmPackage->name);
                    $this->steps[] = new BuildStep(
                        is_object(BuildCommand::RemoveDist) && enum_exists(get_class(BuildCommand::RemoveDist)) ? BuildCommand::RemoveDist->value : (string)BuildCommand::RemoveDist,
                        'Remove dist folder'
                    );
                    $this->steps[] = new BuildStep("mv node_modules/$npmPackage->name/dist ./", 'Move dist folder');
                }
            }
        }
        // Cleanup step
        if ($this->args->has(Flag::Cleanup)) {
            $distignorePath = './.distignore';
            $removables = [];
            if (file_exists($distignorePath)) {
                $removables = array_filter(array_map('trim', file($distignorePath)));
            }
            $desc = $removables ? "Remove files" : "Remove files (none listed)";
            $meta = $removables ? ("\n" . implode(", ", array_map(fn($f) => "$f", $removables))) : null;
            $this->steps[] = new BuildStep('cleanup', $desc, $meta);
        }
    }
    /**
     * Print planned build steps as a table, including meta info for each step.
     */
    public function printSteps(): void {
        print TerminalStyle::sep() . PHP_EOL;
        print TerminalStyle::bold(TerminalStyle::color("PLANNED BUILD STEPS:", TerminalStyle::UNDERLINE)) . PHP_EOL;
        print TerminalStyle::sep() . PHP_EOL;
        $numColWidth  = 4;
        $cmdColWidth  = 18;
        $descColWidth = 32;
        printf(
            "%s %s %s\n",
            TerminalStyle::bold(str_pad("#", $numColWidth)),
            TerminalStyle::bold(str_pad("Command", $cmdColWidth)),
            TerminalStyle::bold(str_pad("Description", $descColWidth))
        );
        print TerminalStyle::sep() . PHP_EOL;
        foreach ($this->steps as $i => $step) {
            printf(
                "%s %s %s\n",
                TerminalStyle::color(str_pad(($i + 1) . ".", $numColWidth), TerminalStyle::CYAN),
                TerminalStyle::bold(str_pad($step->command, $cmdColWidth)),
                $step->description
            );
            if ($step->meta) {
                echo str_pad("", $numColWidth);

                print TerminalStyle::color(" Files:", TerminalStyle::YELLOW) . PHP_EOL;
                print TerminalStyle::color($step->meta, TerminalStyle::GRAY) . PHP_EOL;
            }
        }
        print TerminalStyle::sep() . PHP_EOL;
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
    $args   = new ArgvParser($argv);
    $runner = new BuildRunner($args);
    $runner->prepareSteps();
    $runner->printSteps();
    $dirName = basename(dirname(__FILE__));
    if ($args->has(Flag::Cleanup) && !$args->has(Flag::DryRun)) {
        (new Cleaner($dirName))->clean();
    }
    if ($args->has(Flag::DryRun)) {
        print TerminalStyle::bold(TerminalStyle::color("Dry run: No commands will be executed.", TerminalStyle::YELLOW)) . PHP_EOL;
        exit(0);
    }
    $runner->run();
}

main($argv);
