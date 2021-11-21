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
MQTT connector plugin messages handler proxy
"""

# Python base dependencies
from typing import Any, Dict, List, Optional, Set

# Library dependencies
from kink import inject
from paho.mqtt.client import Client, MQTTMessage

# Library libs
from mqtt_connector_plugin.handlers.base import BaseHandler


@inject
class MessagesHandler:
    """
    MQTT topic handler

    @package        FastyBird:MqttConnectorPlugin!
    @module         handler

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __handlers: Set[BaseHandler]

    # -----------------------------------------------------------------------------

    def __init__(
        self,
        handlers: Optional[List[BaseHandler]] = None,
    ) -> None:
        self.__handlers = set() if handlers is None else set(handlers)

    # -----------------------------------------------------------------------------

    def on_connect(
        self, client: Client, userdata: Any, flags: Dict, response_code: Optional[int]
    ) -> None:
        """On connection to broker established event"""
        for handler in self.__handlers:
            handler.on_connect(
                client=client,
                userdata=userdata,
                flags=flags,
                response_code=response_code,
            )

    # -----------------------------------------------------------------------------

    def on_disconnect(
        self, client: Client, userdata: Any, response_code: Optional[int]
    ) -> None:
        """On connection to broker closed event"""
        for handler in self.__handlers:
            handler.on_disconnect(
                client=client,
                userdata=userdata,
                response_code=response_code,
            )

    # -----------------------------------------------------------------------------

    def on_log(self, client: Client, userdata: Any, level: int, buf: str) -> None:
        """On log message result"""
        for handler in self.__handlers:
            handler.on_log(
                client=client,
                userdata=userdata,
                level=level,
                buf=buf,
            )

    # -----------------------------------------------------------------------------

    def on_subscribe(
        self, client: Client, userdata: Any, message_id: int, granted_qos: int
    ) -> None:
        """On topic subscribed event"""
        for handler in self.__handlers:
            handler.on_subscribe(
                client=client,
                userdata=userdata,
                message_id=message_id,
                granted_qos=granted_qos,
            )

    # -----------------------------------------------------------------------------

    def on_unsubscribe(self, client: Client, userdata: Any, message_id: int) -> None:
        """On topic unsubscribed event"""
        for handler in self.__handlers:
            handler.on_unsubscribe(
                client=client,
                userdata=userdata,
                message_id=message_id,
            )

    # -----------------------------------------------------------------------------

    def on_message(self, client: Client, userdata: Any, message: MQTTMessage) -> None:
        """On broker message event"""
        for handler in self.__handlers:
            handler.on_message(
                client=client,
                userdata=userdata,
                message=message,
            )
