import { readFileSync, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';

const root = process.cwd();
const packageJsonPath = resolve(root, 'package.json');
const styleCssPath = resolve(root, 'style.css');

const packageJson = JSON.parse(readFileSync(packageJsonPath, 'utf8'));
const version = packageJson.version;

if (!version) {
  throw new Error('No version found in package.json.');
}

const styleCss = readFileSync(styleCssPath, 'utf8');
const versionLinePattern = /^Version:\s*.*$/m;

if (!versionLinePattern.test(styleCss)) {
  throw new Error('Could not find a "Version:" line in style.css header.');
}

const updatedStyleCss = styleCss.replace(versionLinePattern, `Version: ${version}`);
writeFileSync(styleCssPath, updatedStyleCss, 'utf8');

console.log(`Synchronized style.css version to ${version}`);
