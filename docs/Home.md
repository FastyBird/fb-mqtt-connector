<p align="center">
	<img src="https://github.com/fastybird/.github/blob/main/assets/repo_title.png?raw=true" alt="FastyBird"/>
</p>

> [!IMPORTANT]
This documentation is meant to be used by developers or users which has basic programming skills. If you are regular user
please use FastyBird IoT documentation which is available on [docs.fastybird.com](https://docs.fastybird.com).

The FastyBird MQTT Connector is an addition to the [FastyBird](https://www.fastybird.com) [IoT](https://en.wikipedia.org/wiki/Internet_of_things)
ecosystem that facilitates integration with devices using the [FastyBird MQTT Convention](https://github.com/FastyBird/mqtt-convention) protocol.
This enables users to effortlessly connect and control their devices using the [FastyBird MQTT Convention](https://github.com/FastyBird/mqtt-convention)
protocol within the [FastyBird](https://www.fastybird.com) [IoT](https://en.wikipedia.org/wiki/Internet_of_things) ecosystem,
providing a convenient and intuitive interface for managing and monitoring their devices.

# About Connector

This connector has some services divided into namespaces. All services are preconfigured and imported into application
container automatically.

```
\FastyBird\Connector\FbMqtt
  \API - Services and helpers related to API - for validation and data parsing
  \Clients - Services which handle communication with MQTT broker
  \Commands - Services used for user console interface
  \Entities - All entities used by connector
  \Helpers - Useful helpers for reading values, bulding entities etc.
  \Queue - Services related to connector internal communication
  \Schemas - {JSON:API} schemas mapping for API requests
  \Translations - Connector translations
  \Writers - Services for handling request from other services
```

All services, helpers, etc. are written to be self-descriptive :wink:.

> [!TIP]
To better understand what some parts of the connector meant to be used for, please refer to the [Naming Convention](Naming-Convention) page.

## Using Connector

The connector is ready to be used as is. Has configured all services in application container and there is no need to develop
some other services or bridges.

> [!TIP]
Find fundamental details regarding the installation and configuration of this connector on the [Configuration](Configuration) page.

This connector is equipped with interactive console. With this console commands you could manage almost all connector features.

* **fb:fb-mqtt-connector:install**: is used for connector installation and configuration. With interactive menu you could manage connector, bridges and device.
* **fb:fb-mqtt-connector:execute**: is used for connector execution. It is simple command that will trigger all services which are related to communication with devices via MQTT broker and other [FastyBird](https://www.fastybird.com) [IoT](https://en.wikipedia.org/wiki/Internet_of_things) ecosystem services like state storage, or user interface communication.

Each console command could be triggered like this :nerd_face:

```shell
php bin/fb-console fb:fb-mqtt-connector:install
```

> [!NOTE]
The path to the console command may vary depending on your FastyBird application distribution. For more information, refer to the FastyBird documentation.
