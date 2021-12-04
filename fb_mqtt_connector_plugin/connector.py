#     Copyright 2021. FastyBird s.r.o.
#
#     Licensed under the Apache License, Version 2.0 (the "License");
#     you may not use this file except in compliance with the License.
#     You may obtain a copy of the License at
#
#         http://www.apache.org/licenses/LICENSE-2.0
#
#     Unless required by applicable law or agreed to in writing, software
#     distributed under the License is distributed on an "AS IS" BASIS,
#     WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#     See the License for the specific language governing permissions and
#     limitations under the License.

"""
FastyBird MQTT connector
"""

# Python base dependencies
import uuid
from threading import Thread
from time import sleep
from typing import Optional

# Library dependencies
from kink import inject

# Library libs
from fb_mqtt_connector_plugin.clients.client import Client, ClientFactory
from fb_mqtt_connector_plugin.consumers.consumer import Consumer
from fb_mqtt_connector_plugin.logger import Logger


class FbMqttConnector(Thread):
    """
    FastyBird MQTT connector

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         connector

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __stopped: bool = False

    __mqtt_client: Client
    __mqtt_client_factory: ClientFactory

    __consumer: Consumer

    __logger: Logger

    # -----------------------------------------------------------------------------

    @inject
    def __init__(
        self,
        mqtt_client: Client,
        mqtt_client_factory: ClientFactory,
        consumer: Consumer,
        logger: Logger,
    ) -> None:
        Thread.__init__(
            self,
            name="FB MQTT connector plugin thread",
            daemon=True,
        )

        self.__mqtt_client = mqtt_client
        self.__mqtt_client_factory = mqtt_client_factory

        self.__consumer = consumer

        self.__logger = logger

    # -----------------------------------------------------------------------------

    def configure_client(  # pylint: disable=too-many-arguments
        self,
        client_id: uuid.UUID,
        server_host: str,
        server_port: int,
        server_username: Optional[str] = None,
        server_password: Optional[str] = None,
    ) -> None:
        """Configure MQTT client & append it to client proxy"""
        self.__mqtt_client_factory.create(
            client_id=client_id,
            server_host=server_host,
            server_port=server_port,
            server_username=server_username,
            server_password=server_password,
        )

    # -----------------------------------------------------------------------------

    def enable_client(self, client_id: uuid.UUID) -> bool:
        """Enable client"""
        return self.__mqtt_client.enable_client(client_id=client_id)

    # -----------------------------------------------------------------------------

    def disable_client(self, client_id: uuid.UUID) -> bool:
        """Disable client connector"""
        return self.__mqtt_client.disable_client(client_id=client_id)

    # -----------------------------------------------------------------------------

    def remove_client(self, client_id: uuid.UUID) -> bool:
        """Remove client from connector"""
        return self.__mqtt_client.remove_client(client_id=client_id)

    # -----------------------------------------------------------------------------

    def start(self) -> None:
        """Start connector services"""
        self.__stopped = False

        super().start()

    # -----------------------------------------------------------------------------

    def stop(self) -> None:
        """Close all opened connections & stop connector thread"""
        self.__stopped = True

        self.__logger.info("Connector FB MQTT has been stopped.")

    # -----------------------------------------------------------------------------

    def run(self) -> None:
        """Process MQTT connectors messages"""
        try:
            self.__mqtt_client.connect()

        except Exception as ex:  # pylint: disable=broad-except
            self.__logger.exception(ex)

            try:
                self.stop()

            except Exception as ex:  # pylint: disable=broad-except
                self.__logger.exception(ex)

        while True:
            self.__consumer.consume()

            # All records have to be processed before thread is closed
            if self.__stopped and self.__consumer.is_empty():
                break

            self.__mqtt_client.check_connection()

            sleep(0.01)

        try:
            self.__mqtt_client.disconnect()

        except Exception as ex:  # pylint: disable=broad-except
            self.__logger.exception(ex)

        self.__mqtt_client.stop()

        self.__logger.info("Connector FB MQTT was closed")
