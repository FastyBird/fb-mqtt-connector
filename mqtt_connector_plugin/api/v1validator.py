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
MQTT v1 convention validator
"""

# Python base dependencies
import re


class V1Validator:
    """
    MQTT topic validator

    @package        FastyBird:MqttConnectorPlugin!
    @module         v1validator

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    # TOPIC: /fb/*
    CONVENTION_PREFIX_REGEXP = r"^\/fb\/.*$"

    # TOPIC: /fb/v1/*
    API_VERSION_REGEXP = r"^\/fb\/v1\/.*$"

    # TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/*
    DEVICE_CHILD_PARTIAL_REGEXP = (
        r"^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/(.*)$"
    )
    # TOPIC: /fb/v1/<device-name>/$channel/<channel-name>/*
    CHANNEL_PARTIAL_REGEXP = r"^\/fb\/v1\/([a-z0-9-]+)\/\$channel\/([a-z0-9-]+)\/.*$"
    # TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/$channel/<channel-name>/*
    CHILD_DEVICE_CHANNEL_PARTIAL_REGEXP = (
        r"^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/\$channel\/([a-z0-9-]+)\/.*$"
    )

    # TOPIC: /fb/v1/<device-name>/<$state|$name|$devices|$control|$channels|$extensions>
    DEVICE_ATTRIBUTE_REGEXP = (
        r"^\/fb\/v1\/([a-z0-9-]+)\/\$(state|name|devices|control|channels|extensions)$"
    )
    # TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/<$state|$name|$devices|$control|$channels|$extensions>
    DEVICE_CHILD_ATTRIBUTE_REGEXP = r"^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/\$(state|name|devices|control|channels|extensions)$"

    # TOPIC: /fb/v1/<device-name>/$hw/<mac-address|manufacturer|model|version>
    DEVICE_HW_INFO_REGEXP = (
        r"^\/fb\/v1\/([a-z0-9-]+)\/\$hw\/(mac-address|manufacturer|model|version)$"
    )
    # TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/$hw/<mac-address|manufacturer|model|version>
    DEVICE_CHILD_HW_INFO_REGEXP = r"^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/\$hw\/(mac-address|manufacturer|model|version)$"
    # TOPIC: /fb/v1/<device-name>/$fw/<manufacturer|name|version>
    DEVICE_FW_INFO_REGEXP = (
        r"^\/fb\/v1\/([a-z0-9-]+)\/\$fw\/(manufacturer|name|version)$"
    )
    # TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/$fw/<manufacturer|name|version>
    DEVICE_CHILD_FW_INFO_REGEXP = r"^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/\$fw\/(manufacturer|name|version)$"

    # TOPIC: /fb/v1/<device-name>/$property/<property-name>[/<$name|$type|$settable|$queryable|$datatype|$format|$unit>]
    DEVICE_PROPERTY_REGEXP = r"^\/fb\/v1\/([a-z0-9-]+)\/\$property\/([a-z0-9-]+)(\/\$(name|type|settable|queryable|datatype|format|unit))?$"
    # TOPIC: /fb/v1/<device-name>/$property/<property-name>/set
    DEVICE_PROPERTY_SET_REGEXP = (
        r"^\/fb\/v1\/([a-z0-9-]+)\/\$property\/([a-z0-9-]+)\/set?$"
    )
    # TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/$property/<property-name>[
    # /<$name|$type|$settable|$queryable|$datatype|$format|$unit>]
    DEVICE_CHILD_PROPERTY_REGEXP = (
        r"^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/\$property\/([a-z0-9-]+)(\/\$("
        r"name|type|settable|queryable|datatype|format|unit))?$"
    )
    # TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/$property/<property-name>/set
    DEVICE_CHILD_PROPERTY_SET_REGEXP = r"^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/\$property\/([a-z0-9-]+)\/set?$"

    # TOPIC: /fb/v1/<device-name>/$control/<configure|reset|reconnect|factory-reset|ota>[/$schema]
    DEVICE_CONTROL_REGEXP = r"^\/fb\/v1\/([a-z0-9-]+)\/\$control\/(configure|reset|reconnect|factory-reset|ota)(\/\$(schema))?$"
    # TOPIC: /fb/v1/<device-name>/$child/<child-device-name>/$control/<configure|reset|reconnect|factory-reset|ota>[
    # /$schema]
    DEVICE_CHILD_CONTROL_REGEXP = (
        r"^\/fb\/v1\/([a-z0-9-]+)\/\$child\/([a-z0-9-]+)\/\$control\/(configure|reset|reconnect|factory-reset|ota)("
        r"\/\$(schema))?$"
    )

    # TOPIC: /fb/v1/*/$channel/<channel-name>/<$name|$devices|$control>
    CHANNEL_ATTRIBUTE_REGEXP = (
        r"\/(.*)\/\$channel\/([a-z0-9-]+)\/\$(name|devices|control)$"
    )
    # TOPIC: /fb/v1/*/$channel/<channel-name>/$property/<property-name>[
    # /<$name|$type|$settable|$queryable|$datatype|$format|$unit>]
    CHANNEL_PROPERTY_REGEXP = (
        r"\/(.*)\/\$channel\/([a-z0-9-]+)\/\$property\/([a-z0-9-]+)(\/\$("
        r"name|type|settable|queryable|datatype|format|unit))?$"
    )
    # TOPIC: /fb/v1/*/$channel/<channel-name>/$property/<property-name>/set
    CHANNEL_PROPERTY_SET_REGEXP = (
        r"\/(.*)\/\$channel\/([a-z0-9-]+)\/\$property\/([a-z0-9-]+)\/set?$"
    )
    # TOPIC: /fb/v1/*/$channel/<channel-name>/$control/<configure>[/$schema]
    CHANNEL_CONTROL_REGEXP = (
        r"\/(.*)\/\$channel\/([a-z0-9-]+)\/\$control\/(configure)(\/\$(schema))?$"
    )

    # -----------------------------------------------------------------------------

    @classmethod
    def validate(cls, topic: str) -> bool:
        """Validate topic against sets of regular expressions"""
        # Check if message is sent from broker
        if len(re.findall(r".*/set$", topic)) > 0:
            return False

        if (
            cls.validate_convention(topic) is False
            or cls.validate_version(topic) is False
        ):
            return False

        if (
            cls.validate_device_attribute(topic)
            or cls.validate_device_hardware_info(topic)
            or cls.validate_device_firmware_info(topic)
            or cls.validate_device_property(topic)
            or cls.validate_device_control(topic)
        ):
            return True

        # Check for channel subscriptions
        if cls.validate_channel_part(topic):
            if (
                cls.validate_channel_attribute(topic)
                or cls.validate_channel_property(topic)
                or cls.validate_channel_control(topic)
            ):
                return True

        return False

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_convention(cls, topic: str) -> bool:
        """Validate topic against convention regular expression"""
        return len(re.findall(cls.CONVENTION_PREFIX_REGEXP, topic)) == 1

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_version(cls, topic: str) -> bool:
        """Validate topic against version regular expression"""
        return len(re.findall(cls.API_VERSION_REGEXP, topic)) == 1

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_is_command(cls, topic: str) -> bool:
        """Validate topic against version regular expression"""
        return len(re.findall(r".*/set$", topic)) == 1

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_device_attribute(cls, topic: str) -> bool:
        """Validate topic against device attribute regular expression"""
        if len(re.findall(cls.DEVICE_ATTRIBUTE_REGEXP, topic)) == 1:
            return True

        return (
            cls.validate_child_device_part(topic)
            and len(re.findall(cls.DEVICE_CHILD_ATTRIBUTE_REGEXP, topic)) == 1
        )

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_child_device_part(cls, topic: str) -> bool:
        """Validate topic against device child part regular expression"""
        return len(re.findall(cls.DEVICE_CHILD_PARTIAL_REGEXP, topic)) == 1

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_device_hardware_info(cls, topic: str) -> bool:
        """Validate topic against device hardware info regular expression"""
        if len(re.findall(cls.DEVICE_HW_INFO_REGEXP, topic)) == 1:
            return True

        return (
            cls.validate_child_device_part(topic)
            and len(re.findall(cls.DEVICE_CHILD_HW_INFO_REGEXP, topic)) == 1
        )

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_device_firmware_info(cls, topic: str) -> bool:
        """Validate topic against device firmware info regular expression"""
        if len(re.findall(cls.DEVICE_FW_INFO_REGEXP, topic)) == 1:
            return True

        return (
            cls.validate_child_device_part(topic)
            and len(re.findall(cls.DEVICE_CHILD_FW_INFO_REGEXP, topic)) == 1
        )

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_device_property(cls, topic: str) -> bool:
        """Validate topic against device property regular expression"""
        if len(re.findall(cls.DEVICE_PROPERTY_REGEXP, topic)) == 1:
            return True

        return (
            cls.validate_child_device_part(topic)
            and len(re.findall(cls.DEVICE_CHILD_PROPERTY_REGEXP, topic)) == 1
        )

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_device_property_set(cls, topic: str) -> bool:
        """Validate topic against device property regular expression"""
        if len(re.findall(cls.DEVICE_PROPERTY_SET_REGEXP, topic)) == 1:
            return True

        return (
            cls.validate_child_device_part(topic)
            and len(re.findall(cls.DEVICE_CHILD_PROPERTY_SET_REGEXP, topic)) == 1
        )

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_device_control(cls, topic: str) -> bool:
        """Validate topic against device control regular expression"""
        if len(re.findall(cls.DEVICE_CONTROL_REGEXP, topic)) == 1:
            return True

        return (
            cls.validate_child_device_part(topic)
            and len(re.findall(cls.DEVICE_CHILD_CONTROL_REGEXP, topic)) == 1
        )

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_channel_part(cls, topic: str) -> bool:
        """Validate topic against channel part regular expression"""
        if len(re.findall(cls.CHANNEL_PARTIAL_REGEXP, topic)) == 1:
            return True

        return (
            cls.validate_child_device_part(topic)
            and len(re.findall(cls.CHILD_DEVICE_CHANNEL_PARTIAL_REGEXP, topic)) == 1
        )

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_channel_attribute(cls, topic: str) -> bool:
        """Validate topic against channel control attribute expression"""
        return (
            cls.validate_channel_part(topic)
            and len(re.findall(cls.CHANNEL_ATTRIBUTE_REGEXP, topic)) == 1
        )

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_channel_property(cls, topic: str) -> bool:
        """Validate topic against channel property regular expression"""
        return (
            cls.validate_channel_part(topic)
            and len(re.findall(cls.CHANNEL_PROPERTY_REGEXP, topic)) == 1
        )

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_channel_property_set(cls, topic: str) -> bool:
        """Validate topic against channel property regular expression"""
        return (
            cls.validate_channel_part(topic)
            and len(re.findall(cls.CHANNEL_PROPERTY_SET_REGEXP, topic)) == 1
        )

    # -----------------------------------------------------------------------------

    @classmethod
    def validate_channel_control(cls, topic: str) -> bool:
        """Validate topic against channel control regular expression"""
        return (
            cls.validate_channel_part(topic)
            and len(re.findall(cls.CHANNEL_CONTROL_REGEXP, topic)) == 1
        )
