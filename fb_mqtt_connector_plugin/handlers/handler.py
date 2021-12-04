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
FastyBird MQTT connector plugin handlers module proxy
"""

# Python base dependencies
from typing import List, Set

# Library dependencies
from kink import inject

# Library libs
from fb_mqtt_connector_plugin.clients.base import BaseClient
from fb_mqtt_connector_plugin.handlers.base import BaseHandler


@inject
class Handler:
    """
    MQTT topic handler

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         handlers

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __handlers: Set[BaseHandler]

    # -----------------------------------------------------------------------------

    def __init__(
        self,
        handlers: List[BaseHandler],
    ) -> None:
        self.__handlers = set(handlers)

    # -----------------------------------------------------------------------------

    def on_connect(self, client: BaseClient) -> None:
        """On connection to broker established event"""
        for handler in self.__handlers:
            handler.on_connect(client=client)

    # -----------------------------------------------------------------------------

    def on_disconnect(self, client: BaseClient) -> None:
        """On connection to broker closed event"""
        for handler in self.__handlers:
            handler.on_disconnect(client=client)

    # -----------------------------------------------------------------------------

    def on_log(self, client: BaseClient, level: int, message: str) -> None:
        """On log message result"""
        for handler in self.__handlers:
            handler.on_log(
                client=client,
                level=level,
                message=message,
            )

    # -----------------------------------------------------------------------------

    def on_subscribe(self, client: BaseClient, message_id: int, granted_qos: int) -> None:
        """On topic subscribed event"""
        for handler in self.__handlers:
            handler.on_subscribe(
                client=client,
                message_id=message_id,
                granted_qos=granted_qos,
            )

    # -----------------------------------------------------------------------------

    def on_unsubscribe(self, client: BaseClient, message_id: int) -> None:
        """On topic unsubscribed event"""
        for handler in self.__handlers:
            handler.on_unsubscribe(
                client=client,
                message_id=message_id,
            )

    # -----------------------------------------------------------------------------

    def on_message(  # pylint: disable=too-many-arguments
        self,
        client: BaseClient,
        topic: str,
        payload: str,
        qos: int,
        retained: bool,
    ) -> None:
        """On broker message event"""
        for handler in self.__handlers:
            handler.on_message(
                client=client,
                topic=topic,
                payload=payload,
                qos=qos,
                retained=retained,
            )
