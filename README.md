[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF)](https://php.net/)
![Run tests](https://github.com/123inkt/phpunit-file-coverage-inspection/workflows/Run%20checks/badge.svg)

# PHPUnit coverage inspection
A tool to allow code coverage rules be defined per file. Set a minimum coverage threshold for every file and configure
custom minimum coverage for existing files if the current test coverage is not up to standards yet. 
Inspection failure will be output in checkstyle format, allowing it to be imported in ci/cd tools.

## Use case
Standard coverage calculation is calculated over the whole codebase. If for example the threshold is 80% and one file drops
below 80% you never notice this because the overall coverage went from 87.6% to 87.4%.
This package makes sure that that doesn't happen anymore and coverage is calculated on a per-file basis. 

## Supported formats
* Input: clover coverage.xml
* Output: checkstyle or gitlab

## Installation
Include the library as dependency in your own project via: 
```
composer require "digitalrevolution/phpunit-file-coverage-inspection" --dev
```

## Configuration

File: `phpfci.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpfci xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="vendor/digitalrevolution/phpunit-file-coverage-inspection/resources/phpfci.xsd"
        min-coverage="100"
        allow-uncovered-methods="false"
>
    <custom-coverage>
        <!-- directory based coverage rule -->
        <directory path="src/Lib/" min="90"/>
        <!-- subdirectories will superceed a parent directory rule -->
        <directory path="src/Lib/Config/" min="100"/>
        <!-- file rule will always superceed a directory rule -->
        <file path="src/Lib/Config/File.php" min="80"/>
    </custom-coverage>
    
    <!-- when 'allow-uncovered-methods' is set to false, override this behaviour for specific files: -->
    <ignore-uncovered-methods>
        <file path="src/Command/ExampleCommand.php"/>
    </ignore-uncovered-methods>
</phpfci>
```

| Option                  | Required | Default  | Description                                   |
|-------------------------|----------|----------|-----------------------------------------------|
| min-coverage            | yes      | -        | The minimum global coverage                   |
| allow-uncovered-methods | no       | false    | All methods should have atleast some coverage | 


or generate a config file based on existing coverage results

```shell script
php vendor/bin/phpfci baseline --baseDir /home/ci/workspace coverage.xml ./phpfci.xml
```

The base directory will be subtracted from the filepaths in coverage.xml

## Usage

Checkstyle format:
```shell script
php vendor/bin/phpfci inspect coverage.xml --reportCheckstyle=reports/checkstyle.xml
```

Gitlab format:
```shell script
php vendor/bin/phpfci inspect coverage.xml --reportGitlab=reports/gitlab.errors.json
```

Gitlab format to file and text output to stdout:
```shell script
php vendor/bin/phpfci inspect coverage.xml --reportGitlab=reports/gitlab.errors.json --reportText
```

Text format to stdout:
```shell script
php vendor/bin/phpfci inspect coverage.xml
```
```shell script
php vendor/bin/phpfci inspect coverage.xml --reportText
```

## Command line arguments

| Option                        | Values                                   | Description                                                             |
|-------------------------------|------------------------------------------|-------------------------------------------------------------------------| 
| `argument 1`                  | `inspect`, `baseline`                    | the command to execute.                                                 |
| `argument 2`                  | `coverage.xml`                           | the phpunit clover coverage input file.                                 |
| `--reportGitlab=[<file>]`     | filepath or if absent stdout             | the file (or stdout) to write the gitlab format to.                     |
| `--reportCheckstyle=[<file>]` | filepath or if absent stdout             | the file (or stdout) to write the checkstyle format to.                 |
| `--reportText=[<file>]`       | filepath or if absent stdout             | the file (or stdout) to write the checkstyle format to.                 |
| `--config=<path-to-file>`     | `phpfci.xml`                             | the path to the config file.                                            |
| `--baseDir=<path>`            | defaults to directory of the output file | The root directory of the project, will be used to make paths relative. |
| `--exit-code-on-failure`      | -                                        | Set exit code to `1` when there are failures.                           |

Note: if no `--reportGitlab`, `--reportCheckstyle` or `--reportText` is set, it will default to `--reportText=php://stdout`

## Migrating from 1 to 2
The third required argument and `--report` has been removed, and should be replaced by:
`--reportGitlab=<file>`, `--reportCheckstyle=<file>` or `--reportText=<file>` 

## About us

At 123inkt (Part of Digital Revolution B.V.), every day more than 50 development professionals are working on improving our internal ERP 
and our several shops. Do you want to join us? [We are looking for developers](https://www.werkenbij123inkt.nl/zoek-op-afdeling/it).
