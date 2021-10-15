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
MQTT connector plugin messages handler
"""

# Library dependencies
from abc import ABC, abstractmethod
from paho.mqtt.client import Client, MQTTMessage

# Library libs
from mqtt_connector_plugin.logger import Logger


class BaseHandler(ABC):
    """
    MQTT messages base handler

    @package        FastyBird:MqttConnectorPlugin!
    @module         base

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
    def on_connect(self, client: Client, userdata, flags, response_code) -> None:
        """On connection to broker established event"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_disconnect(self, client: Client, userdata, response_code) -> None:
        """On connection to broker closed event"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_log(self, client: Client, userdata, level, buf) -> None:
        """On log message result"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_subscribe(self, client: Client, userdata, message_id, granted_qos) -> None:
        """On topic subscribed event"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_unsubscribe(self, client: Client, userdata, message_id) -> None:
        """On topic unsubscribed event"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_message(self, client: Client, userdata, message: MQTTMessage) -> None:
        """On broker message event"""
