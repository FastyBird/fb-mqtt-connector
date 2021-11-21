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
MQTT connector plugin API v1 messages publisher
"""

# Python base dependencies
import json
from typing import Dict, Optional, Set, Union

# Library dependencies
from kink import inject

# Library libs
from mqtt_connector_plugin.client import MqttClient
from mqtt_connector_plugin.entities.entities import ControlEntity
from mqtt_connector_plugin.exceptions import InvalidArgumentException
from mqtt_connector_plugin.logger import Logger
from mqtt_connector_plugin.publishers.base import BasePublisher


@inject(alias=BasePublisher)
class ApiV1Publisher(BasePublisher):
    """
    MQTT topic v1 publisher

    @package        FastyBird:MqttConnectorPlugin!
    @module         apiv1

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __DEVICE_PROPERTY_TOPIC = "/fb/v1/{DEVICE_ID}/$property/{IDENTIFIER}/set"
    __DEVICE_CHILD_PROPERTY_TOPIC = (
        "/fb/v1/{PARENT_ID}/$child/{DEVICE_ID}/$property/{IDENTIFIER}/set"
    )

    __DEVICE_CONTROL_TOPIC = "/fb/v1/{DEVICE_ID}/$control/{CONTROL}/set"
    __DEVICE_CHILD_CONTROL_TOPIC = (
        "/fb/v1/{PARENT_ID}/$child/{DEVICE_ID}/$control/{CONTROL}/set"
    )

    __CHANNEL_PROPERTY_TOPIC = (
        "/fb/v1/{DEVICE_ID}/$channel/{CHANNEL_ID}/$property/{IDENTIFIER}/set"
    )
    __CHANNEL_CHILD_PROPERTY_TOPIC = "/fb/v1/{PARENT_ID}/$child/{DEVICE_ID}/$channel/{CHANNEL_ID}/$property/{IDENTIFIER}/set"

    __CHANNEL_CONTROL_TOPIC = (
        "/fb/v1/{DEVICE_ID}/$channel/{CHANNEL_ID}/$control/{CONTROL}/set"
    )
    __CHANNEL_CHILD_CONTROL_TOPIC = "/fb/v1/{PARENT_ID}/$child/{DEVICE_ID}/$channel/{CHANNEL_ID}/$control/{CONTROL}/set"

    __client: MqttClient

    # -----------------------------------------------------------------------------

    def __init__(self, client: MqttClient, logger: Logger) -> None:
        """Configure mqtt client"""
        BasePublisher.__init__(self, logger=logger)

        self.__client = client

    # -----------------------------------------------------------------------------

    def publish_device_property(
        self,
        device: str,
        identifier: str,
        payload: str,
        parent: Optional[str] = None,
    ) -> None:
        """Publish device property set message"""
        topic = self.__build_topic(
            topic=self.__DEVICE_CHILD_PROPERTY_TOPIC
            if parent is not None
            else self.__DEVICE_PROPERTY_TOPIC,
            data={
                "PARENT_ID": parent,
                "DEVICE_ID": device,
                "IDENTIFIER": identifier,
            },
        )

        self.__publish_message(topic=topic, payload=payload)

    # -----------------------------------------------------------------------------

    def publish_device_configuration(
        self,
        device: str,
        payload: Union[
            Dict[str, Union[str, int, float, bool, None]],
            Set[Dict[str, Union[str, int, float, bool, None]]],
        ],
        parent: Optional[str] = None,
    ) -> None:
        """Publish device configure set message"""
        self.publish_device_command(
            device=device,
            command=ControlEntity.CONFIG,
            payload=json.dumps(payload),
            parent=parent,
        )

    # -----------------------------------------------------------------------------

    def publish_device_command(
        self,
        device: str,
        command: str,
        payload: str = "true",
        parent: Optional[str] = None,
    ) -> None:
        """Publish device control command message"""
        topic = self.__build_topic(
            topic=self.__DEVICE_CHILD_CONTROL_TOPIC
            if parent is not None
            else self.__DEVICE_CONTROL_TOPIC,
            data={
                "PARENT_ID": parent,
                "DEVICE_ID": device,
                "CONTROL": command,
            },
        )

        if command == ControlEntity.CONFIG:
            try:
                json.loads(payload)

            except json.JSONDecodeError as ex:
                raise InvalidArgumentException(
                    "Invalid payload for device command provided"
                ) from ex

        self.__publish_message(topic=topic, payload=payload)

    # -----------------------------------------------------------------------------

    def publish_channel_property(  # pylint: disable=too-many-arguments
        self,
        device: str,
        channel: str,
        identifier: str,
        payload: str,
        parent: Optional[str] = None,
    ) -> None:
        """Publish channel property set message"""
        topic = self.__build_topic(
            topic=self.__CHANNEL_CHILD_PROPERTY_TOPIC
            if parent is not None
            else self.__CHANNEL_PROPERTY_TOPIC,
            data={
                "PARENT_ID": parent,
                "DEVICE_ID": device,
                "CHANNEL_ID": channel,
                "IDENTIFIER": identifier,
            },
        )

        self.__publish_message(topic=topic, payload=payload)

    # -----------------------------------------------------------------------------

    def publish_channel_configuration(
        self,
        device: str,
        channel: str,
        payload: Union[
            Dict[str, Union[str, int, float, bool, None]],
            Set[Dict[str, Union[str, int, float, bool, None]]],
        ],
        parent: Optional[str] = None,
    ) -> None:
        """Publish channel configure set message"""
        self.publish_channel_command(
            device=device,
            channel=channel,
            command=ControlEntity.CONFIG,
            payload=json.dumps(payload),
            parent=parent,
        )

    # -----------------------------------------------------------------------------

    def publish_channel_command(  # pylint: disable=too-many-arguments
        self,
        device: str,
        channel: str,
        command: str,
        payload: str = "true",
        parent: Optional[str] = None,
    ) -> None:
        """Publish channel control command message"""
        topic = self.__build_topic(
            topic=self.__CHANNEL_CHILD_CONTROL_TOPIC
            if parent is not None
            else self.__CHANNEL_CONTROL_TOPIC,
            data={
                "PARENT_ID": parent,
                "DEVICE_ID": device,
                "CHANNEL_ID": channel,
                "CONTROL": command,
            },
        )

        if command == ControlEntity.CONFIG:
            try:
                json.loads(payload)

            except json.JSONDecodeError as ex:
                raise InvalidArgumentException(
                    "Invalid payload for channel command provided"
                ) from ex

        self.__publish_message(topic=topic, payload=payload)

    # -----------------------------------------------------------------------------

    @staticmethod
    def __build_topic(topic: str, data: Dict[str, Optional[str]]) -> str:
        build_topic = topic

        for key, value in data.items():
            if value is not None:
                build_topic = build_topic.replace(f"{{{key}}}", value)

        return build_topic

    # -----------------------------------------------------------------------------

    def __publish_message(self, topic: str, payload: str) -> None:
        result = self.__client.publish(topic=topic, payload=payload, qos=1)

        if result:
            self._logger.info("Published message to: %s", topic)

        else:
            self._logger.error("Message could not be published to: %s", topic)
