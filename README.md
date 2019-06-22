# Queue-consumer-process

[![Travis CI](https://api.travis-ci.org/qlimix/queue-consumer-process.svg?branch=master)](https://travis-ci.org/qlimix/queue-consumer-process)
[![Coveralls](https://img.shields.io/coveralls/github/qlimix/queue-consumer-process.svg)](https://coveralls.io/github/qlimix/queue-consumer-process)
[![Packagist](https://img.shields.io/packagist/v/qlimix/queue-consumer-process.svg)](https://packagist.org/packages/qlimix/queue-consumer-process)
[![MIT License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://github.com/qlimix/queue-consumer-process/blob/master/LICENSE)

A queue consumer process implementation.

## Install

Using Composer:

~~~
$ composer require qlimix/queue-consumer-process
~~~

## usage
```php
<?php

use Qlimix\Queue\Consumer\QueueConsumerProcess;

$processor = new FooBarProcessor();
$process = new QueueConsumerProcess($processor);

$processor->run($control, $output);
```

## Testing
To run all unit tests locally with PHPUnit:

~~~
$ vendor/bin/phpunit
~~~

## Quality
To ensure code quality run grumphp which will run all tools:

~~~
$ vendor/bin/grumphp run
~~~

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.
