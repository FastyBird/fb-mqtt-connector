# FastyBird IoT MQTT connector

[![Build Status](https://badgen.net/github/checks/FastyBird/fb-mqtt-connector/master?cache=300&style=flast-square)](https://github.com/FastyBird/fb-mqtt-connector/actions)
[![Licence](https://badgen.net/github/license/FastyBird/fb-mqtt-connector?cache=300&style=flast-square)](https://github.com/FastyBird/fb-mqtt-connector/blob/master/LICENSE.md)
[![Code coverage](https://badgen.net/coveralls/c/github/FastyBird/fb-mqtt-connector?cache=300&style=flast-square)](https://coveralls.io/r/FastyBird/fb-mqtt-connector)

![PHP](https://badgen.net/packagist/php/FastyBird/fb-mqtt-connector?cache=300&style=flast-square)
[![Latest stable](https://badgen.net/packagist/v/FastyBird/fb-mqtt-connector/latest?cache=300&style=flast-square)](https://packagist.org/packages/FastyBird/fb-mqtt-connector)
[![Downloads total](https://badgen.net/packagist/dt/FastyBird/fb-mqtt-connector?cache=300&style=flast-square)](https://packagist.org/packages/FastyBird/fb-mqtt-connector)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)

## What is FastyBird IoT MQTT connector?

FastyBird IoT MQTT connector is a [FastyBird](https://www.fastybird.com) [IoT](https://en.wikipedia.org/wiki/Internet_of_things)
extension which is integrating [MQTT](https://mqtt.org) protocol
via [FastyBird MQTT Convention](https://github.com/FastyBird/mqtt-convention) for connected devices

[FastyBird](https://www.fastybird.com) [IoT](https://en.wikipedia.org/wiki/Internet_of_things) FB MQTT connector is
an [Apache2 licensed](http://www.apache.org/licenses/LICENSE-2.0) distributed extension, developed
in [PHP](https://www.php.net) with [Nette framework](https://nette.org).

### Features:

- FastyBird MQTT v1 convention devices support
- FastyBird MQTT connector management
  for [FastyBird](https://www.fastybird.com) [IoT](https://en.wikipedia.org/wiki/Internet_of_things) [devices module](https://github.com/FastyBird/devices-module)
- FastyBird MQTT device management
  for [FastyBird](https://www.fastybird.com) [IoT](https://en.wikipedia.org/wiki/Internet_of_things) [devices module](https://github.com/FastyBird/devices-module)
- [{JSON:API}](https://jsonapi.org/) schemas for full api access

## Requirements

[FastyBird](https://www.fastybird.com) IoT MQTT connector is tested against PHP 8.1
and [ReactPHP Socket](https://github.com/reactphp/socket) 1.11 async, streaming plaintext TCP/IP and secure TLS socket server and client connections
and [Nette framework](https://nette.org/en/) 3.0 PHP framework for real programmers

## Installation

### Manual installation

The best way to install **fastybird/fb-mqtt-connector** is using [Composer](http://getcomposer.org/):

```sh
composer require fastybird/fb-mqtt-connector
```

### Marketplace installation

You could install this connector in
your [FastyBird](https://www.fastybird.com) [IoT](https://en.wikipedia.org/wiki/Internet_of_things) application under
marketplace section

## Documentation

Learn how to connect and handle devices connected via MQTT protocol with FastyBird IoT Convention
in [documentation](https://github.com/FastyBird/fb-mqtt-connector/blob/master/.docs/en/index.md).

## Feedback

Use the [issue tracker](https://github.com/FastyBird/fb-mqtt-connector/issues) for bugs
or [mail](mailto:code@fastybird.com) or [Tweet](https://twitter.com/fastybird) us for any idea that can improve the
project.

Thank you for testing, reporting and contributing.

## Changelog

For release info check [release page](https://github.com/FastyBird/fb-mqtt-connector/releases)

## Maintainers

<table>
	<tbody>
		<tr>
			<td align="center">
				<a href="https://github.com/akadlec">
					<img width="80" height="80" src="https://avatars3.githubusercontent.com/u/1866672?s=460&amp;v=4">
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
