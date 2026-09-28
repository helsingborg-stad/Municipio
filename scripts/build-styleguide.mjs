import { existsSync, readFileSync } from 'node:fs';
import { join } from 'node:path';
import { spawnSync } from 'node:child_process';

const directory = 'vendor/helsingborg-stad/styleguide';
const packageFile = join(directory, 'package.json');
const lockFile = join(directory, 'package-lock.json');
const manifestFile = join(directory, 'assets/dist/manifest.json');

if (existsSync(packageFile)) {
    if (!existsSync(lockFile)) {
        throw new Error(`Cannot build Styleguide source without ${lockFile}`);
    }

    const npmCli = process.env.npm_execpath;
    if (!npmCli) {
        throw new Error('Build Styleguide through npm run so the npm CLI path is available');
    }

    for (const args of [
        ['ci', '--prefix', directory, '--no-audit', '--no-fund'],
        ['run', 'build', '--prefix', directory],
    ]) {
        const result = spawnSync(process.execPath, [npmCli, ...args], { stdio: 'inherit' });
        if (result.error) throw result.error;
        if (result.status !== 0) process.exit(result.status ?? 1);
    }
} else {
    if (!existsSync(manifestFile)) {
        throw new Error(`Styleguide release is missing ${manifestFile}`);
    }

    const manifest = JSON.parse(readFileSync(manifestFile, 'utf8'));
    if (!manifest['css/utilities/preloader.css']) {
        throw new Error(`Styleguide release has an incomplete ${manifestFile}`);
    }

    console.log(`Using prebuilt Styleguide assets from ${manifestFile}`);
}
