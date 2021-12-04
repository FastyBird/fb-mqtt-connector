#!/usr/bin/python3

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
FastyBird MQTT connector plugin clients module base client
"""

# Python base dependencies
import uuid
from typing import Any, Dict, Optional, Tuple

# Library dependencies
from paho.mqtt.client import MQTT_ERR_SUCCESS, Client, MQTTMessage

# Library libs
from fb_mqtt_connector_plugin.clients.base import BaseClient
from fb_mqtt_connector_plugin.handlers.handler import Handler
from fb_mqtt_connector_plugin.logger import Logger


class PahoClient(BaseClient):  # pylint: disable=too-many-instance-attributes
    """
    Paho MQTT client

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         clients

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __paho_client: Client

    __id: uuid.UUID
    __state: bool = True

    __server_host: str = "localhost"
    __server_port: int = 1883
    __server_username: Optional[str] = None
    __server_password: Optional[str] = None

    __handler: Handler

    # -----------------------------------------------------------------------------

    def __init__(  # pylint: disable=too-many-arguments
        self,
        client_id: uuid.UUID,
        client_state: bool,
        server_host: str,
        server_port: int,
        server_username: Optional[str],
        server_password: Optional[str],
        handler: Handler,
        logger: Logger,
    ) -> None:
        super().__init__(logger=logger)

        self.__paho_client = Client(client_id.__str__())

        # Set up external MQTT broker callbacks
        self.__paho_client.on_connect = self.__on_connect
        self.__paho_client.on_disconnect = self.__on_disconnect
        self.__paho_client.on_message = self.__on_message
        self.__paho_client.on_subscribe = self.__on_subscribe
        self.__paho_client.on_unsubscribe = self.__on_unsubscribe
        self.__paho_client.on_log = self.__on_log

        self.__id = client_id
        self.__state = client_state

        self.__server_host = server_host
        self.__server_port = server_port
        self.__server_username = server_username
        self.__server_password = server_password

        self.__handler = handler

    # -----------------------------------------------------------------------------

    @property
    def id(self) -> uuid.UUID:
        """Client unique identifier"""
        return self.__id

    # -----------------------------------------------------------------------------

    @property
    def enabled(self) -> bool:
        """Is client enabled?"""
        return self.__state

    # -----------------------------------------------------------------------------

    def enable(self) -> bool:
        """Enable client communication"""
        self.__state = True

        return True

    # -----------------------------------------------------------------------------

    def disable(self) -> bool:
        """Disable client communication"""
        self.__state = False

        return True

    # -----------------------------------------------------------------------------

    def is_connected(self) -> bool:
        """Check if client is connected to broker"""
        return self.__paho_client.is_connected()

    # -----------------------------------------------------------------------------

    def connect(self) -> None:
        """Connect to broker"""
        if self.__server_username is not None:
            self.__paho_client.username_pw_set(username=self.__server_username, password=self.__server_password)

        self.__paho_client.connect(
            host=self.__server_host,
            port=self.__server_port,
        )

    # -----------------------------------------------------------------------------

    def disconnect(self) -> None:
        """Disconnect from broker"""
        self.__paho_client.disconnect()

    # -----------------------------------------------------------------------------

    def start(self) -> None:
        """Start communication"""
        self.__paho_client.loop_start()

    # -----------------------------------------------------------------------------

    def stop(self) -> None:
        """Stop communication"""
        self.__paho_client.loop_stop()

    # -----------------------------------------------------------------------------

    def subscribe(self, topic: str, qos: int = 0) -> Tuple[bool, Optional[int]]:
        """Subscribe to broker"""
        result, message_id = self.__paho_client.subscribe(topic=topic, qos=qos)

        if result == MQTT_ERR_SUCCESS:
            return True, message_id

        return False, None

    # -----------------------------------------------------------------------------

    def publish(self, topic: str, payload: str, qos: int = 0) -> bool:
        """Send message to broker"""
        result = self.__paho_client.publish(topic=topic, payload=payload, qos=qos)

        if result.rc != 0:
            return False

        return True

    # -----------------------------------------------------------------------------

    def __on_connect(  # pylint: disable=unused-argument
        self,
        client: Client,
        userdata: Any,
        flags: Dict,
        response_code: Optional[int],
    ) -> None:
        """On connection to broker established event"""
        self.__handler.on_connect(client=self)

    # -----------------------------------------------------------------------------

    def __on_disconnect(  # pylint: disable=unused-argument
        self,
        client: Client,
        userdata: Any,
        response_code: Optional[int],
    ) -> None:
        """On connection to broker closed event"""
        self.__handler.on_disconnect(client=self)

    # -----------------------------------------------------------------------------

    def __on_log(  # pylint: disable=unused-argument
        self,
        client: Client,
        userdata: Any,
        level: int,
        buf: str,
    ) -> None:
        """On log message result"""
        self.__handler.on_log(
            client=self,
            level=level,
            message=buf,
        )

    # -----------------------------------------------------------------------------

    def __on_subscribe(  # pylint: disable=unused-argument
        self,
        client: Client,
        userdata: Any,
        message_id: int,
        granted_qos: int,
    ) -> None:
        """On topic subscribed event"""
        self.__handler.on_subscribe(
            client=self,
            message_id=message_id,
            granted_qos=granted_qos,
        )

    # -----------------------------------------------------------------------------

    def __on_unsubscribe(  # pylint: disable=unused-argument
        self,
        client: Client,
        userdata: Any,
        message_id: int,
    ) -> None:
        """On topic unsubscribed event"""
        self.__handler.on_unsubscribe(
            client=self,
            message_id=message_id,
        )

    # -----------------------------------------------------------------------------

    def __on_message(  # pylint: disable=unused-argument
        self,
        client: Client,
        userdata: Any,
        message: MQTTMessage,
    ) -> None:
        """On broker message event"""
        self.__handler.on_message(
            client=self,
            topic=message.topic,
            payload=message.payload.decode("utf-8", "ignore"),
            qos=message.qos,
            retained=message.retain,
        )
