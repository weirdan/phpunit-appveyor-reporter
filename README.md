# phpunit-appveyor-reporter

This reporter logs PHPUnit tests results to AppVeyor tests tab in real time.

![Screenshot](https://raw.githubusercontent.com/weirdan/phpunit-appveyor-reporter/master/assets/screenshot.png)

## Installation

```shell
composer require --dev weirdan/phpunit-appveyor-reporter
```

## Usage

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit>
    <![CDATA[ 
        .............................................
    ]]>
    <extensions>
        <extension class="Weirdan\PhpUnitAppVeyorReporter\Listener"></extension>
    </extensions>
</phpunit>
```
