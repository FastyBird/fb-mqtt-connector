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
MQTT connector plugin messages publisher
"""

# Library dependencies
from abc import ABC, abstractmethod
from typing import Dict, Set

# Library libs
from mqtt_connector_plugin.logger import Logger


class BasePublisher(ABC):
    """
    MQTT messages publisher

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
    def publish_device_property(
        self,
        device: str,
        identifier: str,
        payload: str,
        parent: str or None = None,
    ) -> None:
        """Publish device property set message"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def publish_device_configuration(
        self,
        device: str,
        payload: Dict[str, any] or Set[Dict[any]],
        parent: str or None = None,
    ) -> None:
        """Publish device configure set message"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def publish_device_command(
        self,
        device: str,
        command: str,
        payload: str = "true",
        parent: str or None = None,
    ) -> None:
        """Publish device control command message"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def publish_channel_property(  # pylint: disable=too-many-arguments
        self,
        device: str,
        channel: str,
        identifier: str,
        payload: str,
        parent: str or None = None,
    ) -> None:
        """Publish channel property set message"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def publish_channel_configuration(
        self,
        device: str,
        channel: str,
        payload: Dict[str, any] or Set[Dict[any]],
        parent: str or None = None,
    ) -> None:
        """Publish channel configure set message"""

    # -----------------------------------------------------------------------------

    @abstractmethod
    def publish_channel_command(  # pylint: disable=too-many-arguments
        self,
        device: str,
        channel: str,
        command: str,
        payload: str = "true",
        parent: str or None = None,
    ) -> None:
        """Publish channel control command message"""
