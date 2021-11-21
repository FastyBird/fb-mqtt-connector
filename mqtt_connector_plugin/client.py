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
MQTT connector plugin MQTT clients container
"""

# Python base dependencies
import random
import string
import uuid
from time import sleep
from typing import Optional, Set, Tuple

# Library dependencies
from kink import inject
from paho.mqtt.client import Client as PahoClient

# Library libs
from mqtt_connector_plugin.handlers.handler import MessagesHandler
from mqtt_connector_plugin.logger import Logger


class ClientSettings:
    """
    MQTT client configuration

    @package        FastyBird:MqttConnectorPlugin!
    @module         client

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __host: str = "127.0.0.1"
    __port: int = 1883
    __client_id: Optional[str] = None
    __username: Optional[str] = None
    __password: Optional[str] = None

    # -----------------------------------------------------------------------------

    def __init__(  # pylint: disable=too-many-arguments
        self,
        host: str = "127.0.0.1",
        port: int = 1883,
        client_id: Optional[str] = None,
        username: Optional[str] = None,
        password: Optional[str] = None,
    ) -> None:
        self.__host = host
        self.__port = port
        self.__client_id = client_id
        self.__username = username
        self.__password = password

    # -----------------------------------------------------------------------------

    @property
    def host(self) -> str:
        """Get connector broker host address"""
        return self.__host

    # -----------------------------------------------------------------------------

    @property
    def port(self) -> int:
        """Get connector broker port"""
        return self.__port

    # -----------------------------------------------------------------------------

    @property
    def client_id(self) -> str:
        """Get connector client identifier"""
        if self.__client_id is None:
            return "".join(random.choice(string.ascii_lowercase) for _ in range(23))

        return self.__client_id

    # -----------------------------------------------------------------------------

    @property
    def username(self) -> Optional[str]:
        """Get connector broker username"""
        return self.__username

    # -----------------------------------------------------------------------------

    @property
    def password(self) -> Optional[str]:
        """Get connector broker password"""
        return self.__password


@inject
class Client:
    """
    MQTT clients proxy

    @package        FastyBird:MqttConnectorPlugin!
    @module         client

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __mqtt_clients: Set[Tuple[ClientSettings, PahoClient]] = set()

    __logger: Logger

    # -----------------------------------------------------------------------------

    @inject
    def __init__(
        self,
        logger: Logger,
    ) -> None:
        self.__logger = logger

    # -----------------------------------------------------------------------------

    def add_client(self, settings: ClientSettings, client: PahoClient) -> None:
        """Append new client"""
        self.__mqtt_clients.add((settings, client))

    # -----------------------------------------------------------------------------

    def connect(self) -> None:
        """Connect to all brokers"""
        for settings, client in self.__mqtt_clients:
            self.__connect(client=client, settings=settings)

    # -----------------------------------------------------------------------------

    def disconnect(self) -> None:
        """Disconnect from all brokers"""
        for _, client in self.__mqtt_clients:
            client.disconnect()

    # -----------------------------------------------------------------------------

    def stop(self) -> None:
        """Stop MQTT clients loop"""
        for _, client in self.__mqtt_clients:
            client.loop_stop()

    # -----------------------------------------------------------------------------

    def check_connection(self) -> None:
        """Check connection to MQTT brokers"""
        for settings, client in self.__mqtt_clients:
            if not client.is_connected():
                self.__connect(client=client, settings=settings)

    # -----------------------------------------------------------------------------

    def publish(self, topic: str, payload: str, qos: int = 0, client_id: Optional[str] = None) -> bool:
        """Publish payload to all brokers & clients"""
        result = True

        for settings, client in self.__mqtt_clients:
            if client_id is None or settings.client_id == client_id:
                client_result = client.publish(topic=topic, payload=payload, qos=qos)

                if client_result.rc != 0:
                    result = False

        return result

    # -----------------------------------------------------------------------------

    def __connect(self, client: PahoClient, settings: ClientSettings) -> None:
        while not client.is_connected():
            try:
                if settings.username is not None:
                    client.username_pw_set(username=settings.username, password=settings.password)

                client.connect(
                    host=settings.host,
                    port=settings.port,
                )

                client.loop_start()

                if not client.is_connected():
                    sleep(1)

            except ConnectionRefusedError as ex:
                self.__logger.exception(ex)

                sleep(10)


@inject
class PahoClientFactory:  # pylint: disable=too-few-public-methods
    """
    PAHO MQTT client factory

    @package        FastyBird:MqttConnectorPlugin!
    @module         client

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __client: Client
    __messages_handler: MessagesHandler

    __logger: Logger

    # -----------------------------------------------------------------------------

    @inject
    def __init__(
        self,
        client: Client,
        messages_handler: MessagesHandler,
        logger: Logger,
    ) -> None:
        self.__client = client
        self.__messages_handler = messages_handler

        self.__logger = logger

    # -----------------------------------------------------------------------------

    def create(  # pylint: disable=too-many-arguments
        self,
        host: str,
        port: int,
        client_id: uuid.UUID,
        username: Optional[str],
        password: Optional[str],
    ) -> None:
        """Create new instance of Paho MQTT client"""
        client = PahoClient(
            client_id=client_id.__str__(),
            userdata={
                "client_id": client_id,
            },
        )

        # Set up external MQTT broker callbacks
        client.on_connect = self.__messages_handler.on_connect
        client.on_disconnect = self.__messages_handler.on_disconnect
        client.on_message = self.__messages_handler.on_message
        client.on_subscribe = self.__messages_handler.on_subscribe
        client.on_unsubscribe = self.__messages_handler.on_unsubscribe
        client.on_log = self.__messages_handler.on_log

        self.__client.add_client(
            settings=ClientSettings(
                host=host,
                port=port,
                client_id=client_id.__str__(),
                username=username,
                password=password,
            ),
            client=client,
        )

        self.__logger.debug(
            "Created MQTT client: %s to broker: %s:%d",
            client_id.__str__(),
            host,
            port,
        )
