[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2-8892BF)](https://php.net/)
![Run tests](https://github.com/123inkt/phpunit-file-coverage-inspection/workflows/Run%20checks/badge.svg)

# PHPUnit coverage inspection
A tool to allow code coverage rules be defined per file. Set a minimum coverage threshold for every file and configure
custom minimum coverage for existing files if the current test coverage is not up to standards yet. 
Inspection failure will be output in checkstyle format, allowing it to be imported in ci/cd tools.

## Use case
Standard coverage calculation is calculated over the whole codebase. If for example the threshold is 80% and one file drops
below 80% you never notice this because the overall coverage went from 87.6% to 87.4%.
This package makes sure that that doesn't happen anymore and coverage is calculated on a per-file basis. 

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
>
    <custom-coverage>
        <file path="src/FileA.php" min="80"/>
        <file path="src/FileB.php" min="60"/>
    </custom-coverage>
</phpfci>
```

or generate a config file based on existing coverage results

```shell script
php bin/phpfci baseline --baseDir /home/ci/workspace coverage.xml 
```

The base directory will be subtracted from the filepaths in coverage.xml

## Usage

```shell script
php vendor/bin/phpfci inspect coverage.xml reports/checkstyle.xml
```
 

## About us

At 123inkt (Part of Digital Revolution B.V.), every day more than 30 developers are working on improving our internal ERP and our several shops. Do you want to join us? [We are looking for developers](https://www.123inkt.nl/page/werken_ict.html).
