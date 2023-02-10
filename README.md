# FastyBird IoT MQTT connector

[![Build Status](https://badgen.net/github/checks/FastyBird/fb-mqtt-connector/main?cache=300&style=flast-square)](https://github.com/FastyBird/fb-mqtt-connector/actions)
[![Licence](https://badgen.net/github/license/FastyBird/fb-mqtt-connector?cache=300&style=flast-square)](https://github.com/FastyBird/fb-mqtt-connector/blob/main/LICENSE.md)
[![Code coverage](https://badgen.net/coveralls/c/github/FastyBird/fb-mqtt-connector?cache=300&style=flast-square)](https://coveralls.io/r/FastyBird/fb-mqtt-connector)
[![Mutation testing](https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FFastyBird%2Ffb-mqtt-connector%2Fmain)](https://dashboard.stryker-mutator.io/reports/github.com/FastyBird/fb-mqtt-connector/main)

![PHP](https://badgen.net/packagist/php/FastyBird/fb-mqtt-connector?cache=300&style=flast-square)
[![Latest stable](https://badgen.net/packagist/v/FastyBird/fb-mqtt-connector/latest?cache=300&style=flast-square)](https://packagist.org/packages/FastyBird/fb-mqtt-connector)
[![Downloads total](https://badgen.net/packagist/dt/FastyBird/fb-mqtt-connector?cache=300&style=flast-square)](https://packagist.org/packages/FastyBird/fb-mqtt-connector)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)

***

## What is FastyBird IoT MQTT connector?

FastyBird MQTT connector is extension for [FastyBird](https://www.fastybird.com) [IoT](https://en.wikipedia.org/wiki/Internet_of_things) ecosystem
which is integrating [MQTT](https://mqtt.org) protocol
via [FastyBird MQTT Convention](https://github.com/FastyBird/mqtt-convention) for connected devices.

FastyBird MQTT Connector is a distributed extension that is developed in [PHP](https://www.php.net), built on the [Nette](https://nette.org) and [Symfony](https://symfony.com) frameworks,
and is licensed under [Apache2](http://www.apache.org/licenses/LICENSE-2.0).

### Features:

- [FastyBird MQTT v1 convention](https://github.com/FastyBird/mqtt-convention) devices support
- Integration with the [FastyBird](https://www.fastybird.com) [IoT](https://en.wikipedia.org/wiki/Internet_of_things) [devices module](https://github.com/FastyBird/devices-module) for easy management and monitoring of Modbus devices
- Advanced device management features, such as controlling power status, measuring energy consumption, and reading sensor data
- [{JSON:API}](https://jsonapi.org/) schemas for full API access, providing a standardized and consistent way for developers to access and manipulate Shelly device data
- Regular updates with new features and bug fixes, ensuring that the Shelly Connector is always up-to-date and reliable.

## Requirements

FastyBird MQTT connector is tested against PHP 8.1 and require installed [Process Control](https://www.php.net/manual/en/book.pcntl.php)
PHP extension.

## Installation

### Manual installation

The best way to install **fastybird/fb-mqtt-connector** is using [Composer](http://getcomposer.org/):

```sh
composer require fastybird/fb-mqtt-connector
```

### Marketplace installation [WIP]

You could install this connector in your [FastyBird](https://www.fastybird.com) [IoT](https://en.wikipedia.org/wiki/Internet_of_things)
application under marketplace section.

## Documentation

Learn how to connect FastyBird MQTT devices and manage them with [FastyBird](https://www.fastybird.com) [IoT](https://en.wikipedia.org/wiki/Internet_of_things) system
in [documentation](https://github.com/FastyBird/fb-mqtt-connector/wiki).

## Feedback

Use the [issue tracker](https://github.com/FastyBird/fastybird/issues) for bugs
or [mail](mailto:code@fastybird.com) or [Tweet](https://twitter.com/fastybird) us for any idea that can improve the
project.

Thank you for testing, reporting and contributing.

## Changelog

For release info check [release page](https://github.com/FastyBird/fastybird/releases).

## Contribute

The sources of this package are contained in the [FastyBird monorepo](https://github.com/FastyBird/fastybird). We welcome contributions for this package on [FastyBird/fastybird](https://github.com/FastyBird/).

## Maintainers

<table>
	<tbody>
		<tr>
			<td align="center">
				<a href="https://github.com/akadlec">
					<img alt="akadlec" width="80" height="80" src="https://avatars3.githubusercontent.com/u/1866672?s=460&amp;v=4" />
				</a>
				<br>
				<a href="https://github.com/akadlec">Adam Kadlec</a>
			</td>
		</tr>
	</tbody>
</table>

***
Homepage [https://www.fastybird.com](https://www.fastybird.com) and
repository [https://github.com/fastybird/fb-mqtt-connector](https://github.com/fastybird/fb-mqtt-connector).
