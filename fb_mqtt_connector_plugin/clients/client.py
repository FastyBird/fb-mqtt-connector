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
FastyBird MQTT connector plugin client
"""

# Python base dependencies
import uuid
from time import sleep
from typing import List, Optional, Set, Union

# Library dependencies
from kink import inject

# Library libs
from fb_mqtt_connector_plugin.clients.base import BaseClient
from fb_mqtt_connector_plugin.clients.paho import PahoClient
from fb_mqtt_connector_plugin.handlers.handler import Handler
from fb_mqtt_connector_plugin.logger import Logger


@inject
class Client:
    """
    MQTT clients proxy

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         client

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __mqtt_clients: Set[BaseClient] = set()

    __logger: Logger

    # -----------------------------------------------------------------------------

    @inject
    def __init__(
        self,
        logger: Logger,
    ) -> None:
        self.__logger = logger

    # -----------------------------------------------------------------------------

    def connect(self) -> None:
        """Connect to all brokers"""
        for client in self.__mqtt_clients:
            if client.enabled:
                self.__connect(client=client)

    # -----------------------------------------------------------------------------

    def disconnect(self) -> None:
        """Disconnect from all brokers"""
        for client in self.__mqtt_clients:
            client.disconnect()

    # -----------------------------------------------------------------------------

    def stop(self) -> None:
        """Stop MQTT clients loop"""
        for client in self.__mqtt_clients:
            client.stop()
            client.disconnect()

    # -----------------------------------------------------------------------------

    def check_connection(self) -> None:
        """Check connection to MQTT brokers"""
        for client in self.__mqtt_clients:
            if client.enabled and not client.is_connected():
                self.__connect(client=client)

    # -----------------------------------------------------------------------------

    def publish(self, topic: str, payload: str, qos: int = 0, client_id: Optional[str] = None) -> bool:
        """Publish payload to all brokers & clients"""
        result = True

        for client in self.__mqtt_clients:
            if client_id is None or client.id == client_id:
                client_result = client.publish(topic=topic, payload=payload, qos=qos)

                if client_result:
                    result = False

        return result

    # -----------------------------------------------------------------------------

    def register_client(self, client: BaseClient) -> None:
        """Append new client"""
        self.__mqtt_clients.add(client)

    # -----------------------------------------------------------------------------

    def remove_client(self, client_id: Union[uuid.UUID, List[uuid.UUID]]) -> bool:
        """Remove client from clients"""
        process_clients_ids = self.__build_clients_ids_list(client_id=client_id)

        if process_clients_ids is None or len(process_clients_ids) == 0:
            return False

        for client in self.__mqtt_clients:
            if client.id in process_clients_ids:
                self.__mqtt_clients.remove(client)

        return False

    # -----------------------------------------------------------------------------

    def reset_clients(self) -> None:
        """Reset registered clients"""
        for client in self.__mqtt_clients:
            client.stop()
            client.disconnect()

        self.__mqtt_clients = set()

    # -----------------------------------------------------------------------------

    def enable_client(self, client_id: Union[uuid.UUID, List[uuid.UUID], None] = None) -> bool:
        """Enable one or more clients"""
        process_clients_ids = self.__build_clients_ids_list(client_id=client_id)

        result = False

        for client in self.__mqtt_clients:
            if process_clients_ids is None or client.id in process_clients_ids:
                result = client.enable()

        return result

    # -----------------------------------------------------------------------------

    def disable_client(self, client_id: Union[uuid.UUID, List[uuid.UUID], None] = None) -> bool:
        """Disable one or more clients"""
        process_clients_ids = self.__build_clients_ids_list(client_id=client_id)

        result = False

        for client in self.__mqtt_clients:
            if process_clients_ids is None or client.id in process_clients_ids:
                result = client.disable()

        return result

    # -----------------------------------------------------------------------------

    def __connect(self, client: BaseClient) -> None:
        while not client.is_connected():
            try:
                client.connect()

                client.start()

                if not client.is_connected():
                    sleep(1)

            except ConnectionRefusedError as ex:
                self.__logger.exception(ex)

                sleep(10)

    # -----------------------------------------------------------------------------

    @staticmethod
    def __build_clients_ids_list(client_id: Union[uuid.UUID, List[uuid.UUID], None]) -> Optional[List[uuid.UUID]]:
        if isinstance(client_id, uuid.UUID):
            return [client_id]

        if isinstance(client_id, List):
            return client_id

        return None


@inject
class ClientFactory:  # pylint: disable=too-few-public-methods
    """
    Plugin client factory

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         client

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __client: Client
    __handler: Handler

    __logger: Logger

    # -----------------------------------------------------------------------------

    @inject
    def __init__(
        self,
        client: Client,
        handler: Handler,
        logger: Logger,
    ) -> None:
        self.__client = client
        self.__handler = handler

        self.__logger = logger

    # -----------------------------------------------------------------------------

    def create(  # pylint: disable=too-many-arguments
        self,
        client_id: uuid.UUID,
        server_host: str,
        server_port: int,
        server_username: Optional[str] = None,
        server_password: Optional[str] = None,
    ) -> None:
        """Create new instance of Paho MQTT client"""
        client = PahoClient(
            client_id=client_id,
            client_state=True,
            server_host=server_host,
            server_port=server_port,
            server_username=server_username,
            server_password=server_password,
            handler=self.__handler,
            logger=self.__logger,
        )

        self.__client.register_client(client=client)

        self.__logger.debug(
            "Created MQTT client: %s to broker: %s:%d",
            client_id.__str__(),
            server_host,
            server_port,
        )
