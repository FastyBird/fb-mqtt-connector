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
from typing import Optional

# Library dependencies
from kink import inject

# Library libs
from fb_mqtt_connector_plugin.clients.client import Client, ClientFactory
from fb_mqtt_connector_plugin.consumers.consumer import Consumer
from fb_mqtt_connector_plugin.logger import Logger
from fb_mqtt_connector_plugin.types import ClientType, ProtocolVersion


class FbMqttConnector:
    """
    FastyBird MQTT connector

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         connector

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __stopped: bool = False

    __client: Client
    __client_factory: ClientFactory

    __consumer: Consumer

    __logger: Logger

    # -----------------------------------------------------------------------------

    @inject
    def __init__(
        self,
        client: Client,
        client_factory: ClientFactory,
        consumer: Consumer,
        logger: Logger,
    ) -> None:
        self.__client = client
        self.__client_factory = client_factory

        self.__consumer = consumer

        self.__logger = logger

    # -----------------------------------------------------------------------------

    def configure_client(  # pylint: disable=too-many-arguments
        self,
        client_id: uuid.UUID,
        client_type: ClientType,
        server_host: str,
        server_port: int,
        protocol_version: ProtocolVersion,
        server_username: Optional[str] = None,
        server_password: Optional[str] = None,
    ) -> None:
        """Configure MQTT client & append it to client proxy"""
        self.__client_factory.create(
            client_id=client_id,
            client_type=client_type,
            server_host=server_host,
            server_port=server_port,
            server_username=server_username,
            server_password=server_password,
            protocol_version=protocol_version,
        )

    # -----------------------------------------------------------------------------

    def enable_client(self, client_id: uuid.UUID) -> bool:
        """Enable client"""
        return self.__client.enable_client(client_id=client_id)

    # -----------------------------------------------------------------------------

    def disable_client(self, client_id: uuid.UUID) -> bool:
        """Disable client connector"""
        return self.__client.disable_client(client_id=client_id)

    # -----------------------------------------------------------------------------

    def remove_client(self, client_id: uuid.UUID) -> bool:
        """Remove client from connector"""
        return self.__client.remove_client(client_id=client_id)

    # -----------------------------------------------------------------------------

    def start(self) -> None:
        """Start connector services"""
        self.__stopped = False

        try:
            self.__client.connect()

        except Exception as ex:  # pylint: disable=broad-except
            self.__logger.exception(ex)

            try:
                self.stop()

            except Exception as ex:  # pylint: disable=broad-except
                self.__logger.exception(ex)

        self.__logger.info("Connector FB MQTT has been started.")

    # -----------------------------------------------------------------------------

    def stop(self) -> None:
        """Close all opened connections & stop connector thread"""
        self.__stopped = True

        try:
            self.__client.disconnect()

        except Exception as ex:  # pylint: disable=broad-except
            self.__logger.exception(ex)

        self.__logger.info("Connector FB MQTT has been stopped.")

    # -----------------------------------------------------------------------------

    def has_unfinished_tasks(self) -> bool:
        """Check if connector has some unfinished task"""
        return not self.__consumer.is_empty()

    # -----------------------------------------------------------------------------

    def loop(self) -> None:
        """Run connector service"""
        if self.__stopped and not self.has_unfinished_tasks():
            self.__logger.warning("Connector FB MQTT is stopped")

            return

        self.__consumer.loop()

        if not self.__stopped:
            self.__client.loop()
