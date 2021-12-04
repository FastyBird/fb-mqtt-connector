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
FastyBird MQTT connector plugin handlers module base handler
"""

# Python base dependencies
from abc import ABC, abstractmethod

# Library libs
from fb_mqtt_connector_plugin.clients.base import BaseClient
from fb_mqtt_connector_plugin.logger import Logger


class BaseHandler(ABC):
    """
    MQTT messages base handler

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         handlers

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    _logger: Logger

    # -----------------------------------------------------------------------------

    def __init__(
        self,
        logger: Logger,
    ) -> None:
        self._logger = logger

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_connect(self, client: BaseClient) -> None:
        """On connection to broker established event"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_disconnect(self, client: BaseClient) -> None:
        """On connection to broker closed event"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_log(self, client: BaseClient, level: int, message: str) -> None:
        """On log message result"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_subscribe(self, client: BaseClient, message_id: int, granted_qos: int) -> None:
        """On topic subscribed event"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_unsubscribe(self, client: BaseClient, message_id: int) -> None:
        """On topic unsubscribed event"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_message(  # pylint: disable=too-many-arguments
        self,
        client: BaseClient,
        topic: str,
        payload: str,
        qos: int,
        retained: bool,
    ) -> None:
        """On broker message event"""
