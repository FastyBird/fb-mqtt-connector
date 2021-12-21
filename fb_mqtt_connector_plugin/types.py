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
FastyBird BUS types
"""

# Python base dependencies
from enum import Enum, unique


@unique
class ProtocolVersion(Enum):
    """
    Communication protocol version

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         types

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    V1: int = 0x01

    # -----------------------------------------------------------------------------

    @classmethod
    def has_value(cls, value: int) -> bool:
        """Check if value exists in enum"""
        return value in cls._value2member_map_  # pylint: disable=no-member


@unique
class ClientType(Enum):
    """
    Plugin client types

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         types

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    PAHO: str = "paho"

    # -----------------------------------------------------------------------------

    @classmethod
    def has_value(cls, value: str) -> bool:
        """Check if value exists in enum"""
        return value in cls._value2member_map_  # pylint: disable=no-member
