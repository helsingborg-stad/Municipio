#!/bin/php
<?php
// Only allow run from cli.
if (php_sapi_name() !== 'cli') {
    exit(0);
}

// Any command needed to run and build plugin assets when newly cheched out of repo.
$buildCommands = [
    'npm install',
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
    'node_modules'
];

// Run all build commands.
$output = '';
$exitCode = 0;
foreach ($buildCommands as $buildCommand) {
    print "Running build command $buildCommand.\n";
    exec($buildCommand, $output, $exitCode);
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
