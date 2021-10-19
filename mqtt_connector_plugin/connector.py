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
MQTT connector plugin
"""

# Library dependencies
from threading import Thread
from time import sleep
from typing import List
from kink import inject

# Library libs
from mqtt_connector_plugin.client import MqttClient, ClientSettings
from mqtt_connector_plugin.consumers.consumer import MessagesConsumer
from mqtt_connector_plugin.logger import Logger


class MqttConnector(Thread):
    """
    MQTT connector

    @package        FastyBird:MqttConnectorPlugin!
    @module         connector

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    __stopped: bool = False

    __mqtt_client: MqttClient

    __consumer: MessagesConsumer

    __logger: Logger

    # -----------------------------------------------------------------------------

    @inject
    def __init__(
        self,
        mqtt_client: MqttClient,
        consumer: MessagesConsumer,
        logger: Logger,
    ) -> None:
        Thread.__init__(
            self,
            name="FB MQTT connector plugin thread",
            daemon=True,
        )

        self.__mqtt_client = mqtt_client

        self.__consumer = consumer

        self.__logger = logger

    # -----------------------------------------------------------------------------

    def initialize(self, connectors: List[ClientSettings]) -> None:
        """Initialize plugin connectors"""
        self.__mqtt_client.initialize(connectors=connectors)

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

        self.__logger.info("MQTT connector was closed")
