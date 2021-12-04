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
FastyBird MQTT connector plugin publishers module proxy
"""

# Python base dependencies
from typing import Dict, List, Optional, Set, Union

# Library dependencies
from kink import inject

# Library libs
from fb_mqtt_connector_plugin.publishers.base import BasePublisher


@inject
class Publisher:
    """
    MQTT messages publisher

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         publishers

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __publishers: Set[BasePublisher]

    # -----------------------------------------------------------------------------

    def __init__(
        self,
        publishers: List[BasePublisher],
    ) -> None:
        self.__publishers = set(publishers)

    # -----------------------------------------------------------------------------

    def publish_device_property(  # pylint: disable=too-many-arguments
        self,
        device: str,
        identifier: str,
        payload: str,
        parent: Optional[str] = None,
        client_id: Optional[str] = None,
    ) -> None:
        """Publish device property set message"""
        for publisher in self.__publishers:
            publisher.publish_device_property(
                device=device,
                parent=parent,
                identifier=identifier,
                payload=payload,
                client_id=client_id,
            )

    # -----------------------------------------------------------------------------

    def publish_device_configuration(
        self,
        device: str,
        payload: Union[
            Dict[str, Union[str, int, float, bool, None]],
            Set[Dict[str, Union[str, int, float, bool, None]]],
        ],
        parent: Optional[str] = None,
        client_id: Optional[str] = None,
    ) -> None:
        """Publish device configure set message"""
        for publisher in self.__publishers:
            publisher.publish_device_configuration(
                device=device,
                parent=parent,
                payload=payload,
                client_id=client_id,
            )

    # -----------------------------------------------------------------------------

    def publish_device_command(  # pylint: disable=too-many-arguments
        self,
        device: str,
        command: str,
        payload: str = "true",
        parent: Optional[str] = None,
        client_id: Optional[str] = None,
    ) -> None:
        """Publish device control command message"""
        for publisher in self.__publishers:
            publisher.publish_device_command(
                device=device,
                parent=parent,
                command=command,
                payload=payload,
                client_id=client_id,
            )

    # -----------------------------------------------------------------------------

    def publish_channel_property(  # pylint: disable=too-many-arguments
        self,
        device: str,
        channel: str,
        identifier: str,
        payload: str,
        parent: Optional[str] = None,
        client_id: Optional[str] = None,
    ) -> None:
        """Publish channel property set message"""
        for publisher in self.__publishers:
            publisher.publish_channel_property(
                device=device,
                parent=parent,
                channel=channel,
                identifier=identifier,
                payload=payload,
                client_id=client_id,
            )

    # -----------------------------------------------------------------------------

    def publish_channel_configuration(  # pylint: disable=too-many-arguments
        self,
        device: str,
        channel: str,
        payload: Union[
            Dict[str, Union[str, int, float, bool, None]],
            Set[Dict[str, Union[str, int, float, bool, None]]],
        ],
        parent: Optional[str] = None,
        client_id: Optional[str] = None,
    ) -> None:
        """Publish channel configure set message"""
        for publisher in self.__publishers:
            publisher.publish_channel_configuration(
                device=device,
                parent=parent,
                channel=channel,
                payload=payload,
                client_id=client_id,
            )

    # -----------------------------------------------------------------------------

    def publish_channel_command(  # pylint: disable=too-many-arguments
        self,
        device: str,
        channel: str,
        command: str,
        payload: str = "true",
        parent: Optional[str] = None,
        client_id: Optional[str] = None,
    ) -> None:
        """Publish channel control command message"""
        for publisher in self.__publishers:
            publisher.publish_channel_command(
                device=device,
                parent=parent,
                channel=channel,
                command=command,
                payload=payload,
                client_id=client_id,
            )
