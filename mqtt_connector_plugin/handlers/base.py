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

# Python base dependencies
import uuid
from abc import ABC, abstractmethod
from typing import Any, Dict, Optional

# Library dependencies
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
    def on_connect(
        self, client: Client, userdata: Any, flags: Dict, response_code: Optional[int]
    ) -> None:
        """On connection to broker established event"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_disconnect(
        self, client: Client, userdata: Any, response_code: Optional[int]
    ) -> None:
        """On connection to broker closed event"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_log(self, client: Client, userdata: Any, level: int, buf: str) -> None:
        """On log message result"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_subscribe(
        self, client: Client, userdata: Any, message_id: int, granted_qos: int
    ) -> None:
        """On topic subscribed event"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_unsubscribe(self, client: Client, userdata: Any, message_id: int) -> None:
        """On topic unsubscribed event"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def on_message(self, client: Client, userdata: Any, message: MQTTMessage) -> None:
        """On broker message event"""

    # -----------------------------------------------------------------------------

    def extract_connector_id(self, userdata: Any) -> Optional[uuid.UUID]:
        """Extract connector identifier from user data"""
        connector_id: Optional[uuid.UUID] = None

        if (
            isinstance(userdata, dict)
            and userdata.get("connector_id", None) is not None
        ):
            userdata_client_id = userdata.get("connector_id", None)

            if isinstance(userdata_client_id, uuid.UUID):
                connector_id = userdata_client_id

            else:
                try:
                    connector_id = uuid.UUID(userdata_client_id, version=4)

                except ValueError:
                    self._logger.warning(
                        "Connector identifier could not be extracted from user data"
                    )

                    return None

        if connector_id is None:
            self._logger.warning(
                "Connector identifier could not be extracted from user data"
            )

        return connector_id
