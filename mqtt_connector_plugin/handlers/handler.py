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

# Library dependencies
from typing import List
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
    __handlers: List[BaseHandler]

    # -----------------------------------------------------------------------------

    def __init__(
        self,
        handlers: List[BaseHandler],
    ) -> None:
        self.__handlers = handlers

    # -----------------------------------------------------------------------------

    def on_connect(self, client: Client, userdata, flags, response_code) -> None:
        """On connection to broker established event"""
        for handler in self.__handlers:
            handler.on_connect(
                client=client,
                userdata=userdata,
                flags=flags,
                response_code=response_code,
            )

    # -----------------------------------------------------------------------------

    def on_disconnect(self, client: Client, userdata, response_code) -> None:
        """On connection to broker closed event"""
        for handler in self.__handlers:
            handler.on_disconnect(
                client=client,
                userdata=userdata,
                response_code=response_code,
            )

    # -----------------------------------------------------------------------------

    def on_log(self, client: Client, userdata, level, buf) -> None:
        """On log message result"""
        for handler in self.__handlers:
            handler.on_log(
                client=client,
                userdata=userdata,
                level=level,
                buf=buf,
            )

    # -----------------------------------------------------------------------------

    def on_subscribe(self, client: Client, userdata, message_id, granted_qos) -> None:
        """On topic subscribed event"""
        for handler in self.__handlers:
            handler.on_subscribe(
                client=client,
                userdata=userdata,
                message_id=message_id,
                granted_qos=granted_qos,
            )

    # -----------------------------------------------------------------------------

    def on_unsubscribe(self, client: Client, userdata, message_id) -> None:
        """On topic unsubscribed event"""
        for handler in self.__handlers:
            handler.on_unsubscribe(
                client=client,
                userdata=userdata,
                message_id=message_id,
            )

    # -----------------------------------------------------------------------------

    def on_message(self, client: Client, userdata, message: MQTTMessage) -> None:
        """On broker message event"""
        for handler in self.__handlers:
            handler.on_message(
                client=client,
                userdata=userdata,
                message=message,
            )
