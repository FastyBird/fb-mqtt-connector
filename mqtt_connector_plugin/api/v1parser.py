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
MQTT v1 convention parser
"""

# Library dependencies
import re
from typing import List

# Library libs
from mqtt_connector_plugin.api.v1validator import V1Validator
from mqtt_connector_plugin.entities.entities import (
    BaseEntity,
    DeviceAttributeEntity,
    DeviceControlEntity,
    DevicePropertyEntity,
    HardwareEntity,
    FirmwareEntity,
    ChannelAttributeEntity,
    ChannelPropertyEntity,
    ChannelControlEntity,
)
from mqtt_connector_plugin.exceptions import ParseMessageException


class V1Parser:
    """
    MQTT topic parser

    @package        FastyBird:MqttConnectorPlugin!
    @module         v1parser

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    @classmethod
    def parse_message(cls, topic: str, payload: str, retained: bool = False) -> BaseEntity:
        """Parse received message topic & value"""
        if V1Validator.validate(topic) is False:
            raise ParseMessageException("Provided topic is not valid")

        is_child: bool = V1Validator.validate_child_device_part(topic)

        if V1Validator.validate_device_attribute(topic):
            entity = cls.parse_device_attribute(topic=topic, payload=payload, is_child=is_child)
            entity.retained = retained

            return entity

        if V1Validator.validate_device_hardware_info(topic):
            entity = cls.parse_device_hardware_info(topic=topic, payload=payload, is_child=is_child)
            entity.retained = retained

            return entity

        if V1Validator.validate_device_firmware_info(topic):
            entity = cls.parse_device_firmware_info(topic=topic, payload=payload, is_child=is_child)
            entity.retained = retained

            return entity

        if V1Validator.validate_device_property(topic):
            entity = cls.parse_device_property(topic=topic, payload=payload, is_child=is_child)
            entity.retained = retained

            return entity

        if V1Validator.validate_device_control(topic):
            entity = cls.parse_device_control(topic=topic, payload=payload, is_child=is_child)
            entity.retained = retained

            return entity

        if V1Validator.validate_channel_part(topic):
            if is_child:
                result: List[tuple] = re.findall(V1Validator.CHILD_DEVICE_CHANNEL_PARTIAL_REGEXP, topic)
                parent, device, channel = result.pop()

            else:
                result: List[tuple] = re.findall(V1Validator.CHANNEL_PARTIAL_REGEXP, topic)
                device, channel = result.pop()
                parent = None

            return cls.parse_channel_message(
                device=device,
                channel=channel,
                parent=parent,
                topic=topic,
                payload=payload,
                retained=retained,
            )

        raise ParseMessageException("Provided topic is not valid")

    # -----------------------------------------------------------------------------

    @classmethod
    def parse_channel_message(  # pylint: disable=too-many-arguments
        cls,
        device: str,
        channel: str,
        parent: str or None,
        topic: str,
        payload: str,
        retained: bool = False,
    ) -> BaseEntity:
        """Parse received message topic & value for device channel"""
        if V1Validator.validate_channel_attribute(topic):
            entity = cls.parse_channel_attribute(
                device=device,
                parent=parent,
                channel=channel,
                topic=topic,
                payload=payload,
            )
            entity.retained = retained

            return entity

        if V1Validator.validate_channel_property(topic):
            entity = cls.parse_channel_property(
                device=device,
                parent=parent,
                channel=channel,
                topic=topic,
                payload=payload,
            )
            entity.retained = retained

            return entity

        if V1Validator.validate_channel_control(topic):
            entity = cls.parse_channel_control(
                device=device,
                parent=parent,
                channel=channel,
                topic=topic,
                payload=payload,
            )
            entity.retained = retained

            return entity

        raise ParseMessageException("Provided topic is not valid")

    # -----------------------------------------------------------------------------

    @staticmethod
    def parse_device_attribute(topic: str, payload: str, is_child: bool = False) -> DeviceAttributeEntity:
        """Parse device attribute topic & value"""
        if is_child is True:
            result: List[tuple] = re.findall(V1Validator.CHILD_DEVICE_CHANNEL_PARTIAL_REGEXP, topic)
            parent, device, attribute = result.pop()

        else:
            result: List[tuple] = re.findall(V1Validator.DEVICE_ATTRIBUTE_REGEXP, topic)
            device, attribute = result.pop()
            parent = None

        return DeviceAttributeEntity(
            device=device,
            attribute=attribute,
            value=payload,
            parent=parent,
        )

    # -----------------------------------------------------------------------------

    @staticmethod
    def parse_device_hardware_info(topic: str, payload: str, is_child: bool = False) -> HardwareEntity:
        """Parse device hardware info topic & value"""
        if is_child is True:
            result: List[tuple] = re.findall(V1Validator.DEVICE_CHILD_HW_INFO_REGEXP, topic)
            parent, device, hardware = result.pop()

        else:
            result: List[tuple] = re.findall(V1Validator.DEVICE_ATTRIBUTE_REGEXP, topic)
            device, hardware = result.pop()
            parent = None

        return HardwareEntity(
            device=device,
            parameter=hardware,
            value=payload,
            parent=parent,
        )

    # -----------------------------------------------------------------------------

    @staticmethod
    def parse_device_firmware_info(topic: str, payload: str, is_child: bool = False) -> FirmwareEntity:
        """Parse device firmware info topic & value"""
        if is_child is True:
            result: List[tuple] = re.findall(V1Validator.DEVICE_CHILD_FW_INFO_REGEXP, topic)
            parent, device, firmware = result.pop()

        else:
            result: List[tuple] = re.findall(V1Validator.DEVICE_FW_INFO_REGEXP, topic)
            device, firmware = result.pop()
            parent = None

        return FirmwareEntity(
            device=device,
            parameter=firmware,
            value=payload,
            parent=parent,
        )

    # -----------------------------------------------------------------------------

    @staticmethod
    def parse_device_property(topic: str, payload: str, is_child: bool = False) -> DevicePropertyEntity:
        """Parse device property topic & value"""
        if is_child is True:
            result: List[tuple] = re.findall(V1Validator.DEVICE_CHILD_PROPERTY_REGEXP, topic)
            parent, device, name, _, attribute = result.pop()

        else:
            result: List[tuple] = re.findall(V1Validator.DEVICE_PROPERTY_REGEXP, topic)
            device, name, _, attribute = result.pop()
            parent = None

        entity = DevicePropertyEntity(
            device=device,
            name=name,
            parent=parent,
        )

        if attribute:
            entity.add_attribute(attribute=attribute, value=payload)

        else:
            entity.value = payload

        return entity

    # -----------------------------------------------------------------------------

    @staticmethod
    def parse_device_control(topic: str, payload: str, is_child: bool = False) -> DeviceControlEntity:
        """Parse device control topic & value"""
        if is_child is True:
            result: List[tuple] = re.findall(V1Validator.DEVICE_CHILD_CONTROL_REGEXP, topic)
            parent, device, control, _, attribute = result.pop()

        else:
            result: List[tuple] = re.findall(V1Validator.DEVICE_CONTROL_REGEXP, topic)
            device, control, _, attribute = result.pop()
            parent = None

        entity = DeviceControlEntity(
            device=device,
            control=control,
            parent=parent,
        )

        if attribute:
            entity.schema = payload

        else:
            entity.value = payload

        return entity

    # -----------------------------------------------------------------------------

    @staticmethod
    def parse_channel_attribute(
        device: str,
        parent: str or None,
        channel: str,
        topic: str,
        payload: str,
    ) -> ChannelAttributeEntity:
        """Parse channel control attribute & value"""
        result: List[tuple] = re.findall(V1Validator.CHANNEL_ATTRIBUTE_REGEXP, topic)
        _, __, attribute = result.pop()

        return ChannelAttributeEntity(
            device=device,
            channel=channel,
            attribute=attribute,
            value=payload,
            parent=parent,
        )

    # -----------------------------------------------------------------------------

    @staticmethod
    def parse_channel_property(
        device: str,
        parent: str or None,
        channel: str,
        topic: str,
        payload: str,
    ) -> ChannelPropertyEntity:
        """Parse channel property topic & value"""
        result: List[tuple] = re.findall(V1Validator.CHANNEL_PROPERTY_REGEXP, topic)
        _, __, name, ___, attribute = result.pop()

        entity = ChannelPropertyEntity(
            device=device,
            channel=channel,
            name=name,
            parent=parent,
        )

        if attribute:
            entity.add_attribute(attribute=attribute, value=payload)

        else:
            entity.value = payload

        return entity

    # -----------------------------------------------------------------------------

    @staticmethod
    def parse_channel_control(
        device: str,
        parent: str or None,
        channel: str,
        topic: str,
        payload: str,
    ) -> ChannelControlEntity:
        """Parse channel control topic & value"""
        result: List[tuple] = re.findall(V1Validator.CHANNEL_CONTROL_REGEXP, topic)
        _, __, control, ___, attribute = result.pop()

        entity = ChannelControlEntity(
            device=device,
            channel=channel,
            control=control,
            parent=parent,
        )

        if attribute:
            entity.schema = payload

        else:
            entity.value = payload

        return entity
