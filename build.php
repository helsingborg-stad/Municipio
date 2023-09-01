#!/bin/php
<?php
// Only allow run from cli.
if (php_sapi_name() !== 'cli') {
    exit(0);
}

/* Parameters: 
 --no-composer      Does not install vendors. Just create the autoloader.
 --cleanup          Remove removeables. 
 --allow-gulp       Allow gulp to be used. 
*/

// Any command needed to run and build plugin assets when newly cheched out of repo.
$buildCommands = [];

//Add composer build, if flag --no-composer is undefined.
//Dump autloader. 
//Only if composer.json exists.
if(file_exists('composer.json')) {
    if(is_array($argv) && !in_array('--no-composer', $argv)) {
        $buildCommands[] = 'composer install --prefer-dist --no-progress --no-dev'; 
    }
    $buildCommands[] = 'composer dump-autoload';
}

//Run npm if package.json is found
if(file_exists('package.json') && file_exists('package-lock.json')) {
    $buildCommands[] = 'npm ci --no-progress --no-audit';
} elseif(file_exists('package.json') && !file_exists('package-lock.json')) {
    $buildCommands[] = 'npm install --no-progress --no-audit';
}

//Run build if package-lock.json is found
if(file_exists('package-lock.json') && !file_exists('gulp.js')) {
    $buildCommands[] = 'npx --yes browserslist@latest --update-db';
    $buildCommands[] = 'npm run build';
} elseif(file_exists('package-lock.json') && file_exists('gulp.js') && is_array($argv) && in_array('--allow-gulp', $argv)) {
    $buildCommands[] = 'gulp';
}

// Files and directories not suitable for prod to be removed.
$removables = [
    '.git',
    '.gitignore',
    '.github',
    '.gitattributes',
    'build.php',
    '.npmrc',
    'composer.json',
    'composer.lock',
    'env-example',
    'webpack.config.js',
    'package-lock.json',
    'package.json',
    'phpunit.xml.dist',
    'README.md',
    'gulpfile.js',
    './node_modules/',
    './source/sass/',
    './source/js/',
    'LICENSE',
    'babel.config.js',
    'yarn.lock'
];

$dirName = basename(dirname(__FILE__));

// Run all build commands.
$output = '';
$exitCode = 0;
foreach ($buildCommands as $buildCommand) {
    print "---- Running build command '$buildCommand' for $dirName. ----\n";
    $timeStart = microtime(true);
    $exitCode = executeCommand($buildCommand);
    $buildTime = round(microtime(true) - $timeStart);
    print "---- Done build command '$buildCommand' for $dirName.  Build time: $buildTime seconds. ----\n";
    if ($exitCode > 0) {
        exit($exitCode);
    }
}

// Remove files and directories if '--cleanup' argument is supplied to save local developers from disasters.
if(is_array($argv) && in_array('--cleanup', $argv)) {
    foreach ($removables as $removable) {
        if (file_exists($removable)) {
            print "Removing $removable from $dirName\n";
            shell_exec("rm -rf $removable");
        }
    }
}

/**
 * Better shell script execution with live output to STDOUT and status code return.
 * @param  string $command Command to execute in shell.
 * @return int             Exit code.
 */
function executeCommand($command)
{
    $fullCommand = '';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $fullCommand = "cmd /v:on /c \"$command 2>&1 & echo Exit status : !ErrorLevel!\"";
    } else {
        $fullCommand = "$command 2>&1 ; echo Exit status : $?";
    }

    $proc = popen($fullCommand, 'r');

    $liveOutput     = '';
    $completeOutput = '';

    while (!feof($proc)) {
        $liveOutput     = fread($proc, 4096);
        $completeOutput = $completeOutput . $liveOutput;
        print $liveOutput;
        @ flush();
    }

    pclose($proc);

    // Get exit status.
    preg_match('/[0-9]+$/', $completeOutput, $matches);

    // Return exit status.
    return intval($matches[0]);
}