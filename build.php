#!/bin/php
<?php
// Only allow run from cli.
if (php_sapi_name() !== 'cli') {
    exit(0);
}

// Any command needed to run and build plugin assets when newly cheched out of repo.
$buildCommands = [
    'npm install --no-progress',
    'npm run build',
    'composer install --prefer-dist --no-progress --no-suggest'
];

// Files and directories not suitable for prod to be removed.
$removables = [
    '.git',
    '.gitignore',
    '.github',
    'build.php',
    '.npmrc',
    'composer.json',
    'env-example',
    'gulpfile.js',
    'gulpfile.old.js',
    'webpack.config.js',
    'node_modules',
    'package-lock.json',
    'package.json'
];

// Run all build commands.
$output = '';
$exitCode = 0;
foreach ($buildCommands as $buildCommand) {
    print "Running build command $buildCommand.\n";

    $exitCode = executeCommand($buildCommand);
    if ($exitCode > 0) {
        exit($exitCode);
    }
}

// Remove files and directories.
foreach ($removables as $removable) {
    if (file_exists($removable)) {
        print "Removing $removable\n";
        shell_exec("rm -rf $removable");
    }
}

/**
 * Better shell script execution with live output to STDOUT and status code return.
 * @param  string $command Command to execute in shell.
 * @return int             Exit code.
 */
function executeCommand($command)
{
    $proc = popen("$command 2>&1 ; echo Exit status : $?", 'r');

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
