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

# Library dependencies
import random
import string
from time import sleep
from typing import Dict, Tuple, List
from kink import inject
from paho.mqtt.client import Client as PahoClient, MQTTMessageInfo

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
    __connector_id: str
    __broker_host: str = "127.0.0.1"
    __broker_port: int = 1883
    __client_id: str or None = None

    # -----------------------------------------------------------------------------

    def __init__(
        self,
        connector_id: str,
        broker_host: str = "127.0.0.1",
        broker_port: int = 1883,
        client_id: str or None = None,
    ) -> None:
        self.__connector_id = connector_id
        self.__broker_host = broker_host
        self.__broker_port = broker_port
        self.__client_id = client_id

    # -----------------------------------------------------------------------------

    @property
    def connector_id(self) -> str:
        """Get connector identifier"""
        return self.__connector_id

    # -----------------------------------------------------------------------------

    @property
    def broker_host(self) -> str:
        """Get connector broker host address"""
        return self.__broker_host

    # -----------------------------------------------------------------------------

    @property
    def broker_port(self) -> int:
        """Get connector broker port"""
        return self.__broker_port

    # -----------------------------------------------------------------------------

    @property
    def client_id(self) -> str:
        """Get connector client identifier"""
        if self.__client_id is None:
            return "".join(random.choice(string.ascii_lowercase) for _ in range(23))

        return self.__client_id


@inject
class MqttClient:
    """
    MQTT clients proxy

    @package        FastyBird:MqttConnectorPlugin!
    @module         client

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    __mqtt_clients: Dict[str, Tuple[ClientSettings, PahoClient]] = {}

    __messages_handler: MessagesHandler

    __logger: Logger

    # -----------------------------------------------------------------------------

    @inject
    def __init__(
        self,
        messages_handler: MessagesHandler,
        logger: Logger,
    ) -> None:
        self.__messages_handler = messages_handler

        self.__logger = logger

    # -----------------------------------------------------------------------------

    def initialize(self, connectors: List[ClientSettings]) -> None:
        """Initialize all brokers connections"""
        for connector in connectors:
            client = PahoClient(connector.client_id)

            # Set up external MQTT broker callbacks
            client.on_connect = self.__messages_handler.on_connect
            client.on_disconnect = self.__messages_handler.on_disconnect
            client.on_message = self.__messages_handler.on_message
            client.on_subscribe = self.__messages_handler.on_subscribe
            client.on_unsubscribe = self.__messages_handler.on_unsubscribe
            client.on_log = self.__messages_handler.on_log

            self.__mqtt_clients[connector.connector_id] = (connector, client)

    # -----------------------------------------------------------------------------

    def connect(self) -> None:
        """Connect to all brokers"""
        for settings, client in self.__mqtt_clients.values():
            self.__connect(client=client, settings=settings)

    # -----------------------------------------------------------------------------

    def disconnect(self) -> None:
        """Disconnect from all brokers"""
        for _, client in self.__mqtt_clients.values():
            client.disconnect()

    # -----------------------------------------------------------------------------

    def stop(self) -> None:
        """Stop MQTT clients loop"""
        for _, client in self.__mqtt_clients.values():
            client.loop_stop()

    # -----------------------------------------------------------------------------

    def check_connection(self) -> None:
        """Check connection to MQTT brokers"""
        for settings, client in self.__mqtt_clients.values():
            if not client.is_connected():
                self.__connect(client=client, settings=settings)

    # -----------------------------------------------------------------------------

    def publish(self, topic: str, payload: str, qos: int = 0) -> bool:
        """Publish payload to all brokers & clients"""
        result: bool = True

        for _, client in self.__mqtt_clients.values():
            client_result: MQTTMessageInfo = client.publish(topic=topic, payload=payload, qos=qos)

            if client_result.rc != 0:
                result = False

        return result

    # -----------------------------------------------------------------------------

    def __connect(self, client: PahoClient, settings: ClientSettings) -> None:
        while not client.is_connected():
            try:
                client.connect(
                    host=settings.broker_host,
                    port=settings.broker_port,
                )

                client.loop_start()

                if not client.is_connected():
                    sleep(1)

            except ConnectionRefusedError as ex:
                self.__logger.exception(ex)

                sleep(10)
