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
from abc import ABC, abstractmethod
from typing import Optional, Tuple

# Library libs
from fb_mqtt_connector_plugin.logger import Logger


class BaseClient(ABC):
    """
    Base client

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         clients

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

    @property
    @abstractmethod
    def id(self) -> uuid.UUID:  # pylint: disable=invalid-name
        """Client unique identifier"""

    # -----------------------------------------------------------------------------

    @property
    @abstractmethod
    def enabled(self) -> bool:
        """Client state"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def enable(self) -> bool:
        """Enable client communication"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def disable(self) -> bool:
        """Disable client communication"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def is_connected(self) -> bool:
        """Check if client is connected to broker"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def connect(self) -> None:
        """Connect to broker"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def disconnect(self) -> None:
        """Disconnect from broker"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def start(self) -> None:
        """Start communication"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def stop(self) -> None:
        """Stop communication"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def subscribe(self, topic: str, qos: int = 0) -> Tuple[bool, Optional[int]]:
        """Subscribe to broker"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def publish(self, topic: str, payload: str, qos: int = 0) -> bool:
        """Send message to broker"""
