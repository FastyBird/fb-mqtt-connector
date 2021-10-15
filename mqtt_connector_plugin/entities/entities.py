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
MQTT connector plugin entities
"""

# Library dependencies
import json
import re
from abc import ABC
from typing import List, Dict, Set
from fastnumbers import fast_real
from modules_metadata.types import DataType, ControlName

# Library libs
from mqtt_connector_plugin.exceptions import (
    InvalidArgumentException,
    InvalidStateException,
    ParseMessageException,
)


def clean_name(name: str) -> str:
    """Clean name value"""
    return re.sub(r"[^A-Za-z0-9.,_ -]", "", name)


def clean_payload(payload: str) -> str:
    """Clean payload value"""
    return re.sub(r"[^A-Za-z0-9.:_°, -%µ³/\"]", "", payload)


class BaseEntity(ABC):
    """
    Base entity

    @package        FastyBird:MqttConnectorPlugin!
    @module         entities

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    __device: str
    __parent: str or None = None
    __retained: bool = False

    # -----------------------------------------------------------------------------

    def __init__(self, device: str, parent: str or None = None) -> None:
        self.__device = device
        self.__parent = parent

    # -----------------------------------------------------------------------------

    @property
    def device(self) -> str:
        """Entity device identifier"""
        return self.__device

    # -----------------------------------------------------------------------------

    @property
    def parent(self) -> str or None:
        """Entity parent device identifier"""
        return self.__parent

    # -----------------------------------------------------------------------------

    @property
    def retained(self) -> bool:
        """Entity retained flag"""
        return self.__retained

    # -----------------------------------------------------------------------------

    @retained.setter
    def retained(self, retained: bool) -> None:
        """Entity retained flag setter"""
        self.__retained = retained

    # -----------------------------------------------------------------------------

    def to_dict(self) -> Dict[str, str or bool or None]:
        """Entity to dictionary converter"""
        return {
            "device": self.device,
            "parent": self.parent,
            "retained": self.retained,
        }


class AttributeEntity(BaseEntity):
    """
    Base attribute message entity

    @package        FastyBird:MqttConnectorPlugin!
    @module         entities

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    NAME = "name"
    PROPERTIES = "devices"
    STATE = "state"
    CHANNELS = "channels"
    EXTENSIONS = "extensions"
    CONTROLS = "controls"

    __attribute: str
    __value: str or List[str]

    # -----------------------------------------------------------------------------

    def __init__(self, device: str, attribute: str, value: str, parent: str or None = None) -> None:
        if attribute not in self.allowed_attributes:
            raise InvalidArgumentException(f"Provided attribute '{attribute}' is not in allowed range")

        super().__init__(device=device, parent=parent)

        self.__attribute = attribute
        self.__value = self.__parse_value(value)

    # -----------------------------------------------------------------------------

    @property
    def attribute(self) -> str:
        """Entity attribute"""
        return self.__attribute

    # -----------------------------------------------------------------------------

    @property
    def value(self) -> str or List[str]:
        """Entity value"""
        return self.__value

    # -----------------------------------------------------------------------------

    def to_dict(self) -> Dict:
        """Entity to dictionary converter"""
        return {**super().to_dict(), **{
            self.attribute: self.value,
        }}

    # -----------------------------------------------------------------------------

    @property
    def allowed_attributes(self) -> List[str]:
        """List of entity allowed attributes"""
        return []

    # -----------------------------------------------------------------------------

    def __parse_value(self, value: str) -> str or List[str]:
        """Parse value against entity attribute type"""
        if self.attribute == self.NAME:
            return clean_name(value)

        cleaned_value = clean_payload(value)

        if self.attribute in (self.PROPERTIES, self.CHANNELS, self.EXTENSIONS, self.CONTROLS):
            cleaned_value = cleaned_value.strip().split(",")
            cleaned_value = [item.strip() for item in cleaned_value if item.strip()]

            return list(set(cleaned_value))

        return cleaned_value


class DeviceAttributeEntity(AttributeEntity):
    """
    Device attribute message entity

    @package        FastyBird:MqttConnectorPlugin!
    @module         entities

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    @property
    def allowed_attributes(self) -> List[str]:
        """List of entity allowed attributes"""
        return [
            self.NAME,
            self.PROPERTIES,
            self.STATE,
            self.CHANNELS,
            self.EXTENSIONS,
            self.CONTROLS,
        ]


class ChannelAttributeEntity(AttributeEntity):
    """
    Channel attribute message entity

    @package        FastyBird:MqttConnectorPlugin!
    @module         entities

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    __channel: str

    # -----------------------------------------------------------------------------

    def __init__(  # pylint: disable=too-many-arguments
        self,
        device: str,
        channel: str,
        attribute: str,
        value: str,
        parent: str or None = None,
    ) -> None:
        super().__init__(device=device, attribute=attribute, value=value, parent=parent)

        self.__channel = channel

    # -----------------------------------------------------------------------------

    @property
    def channel(self) -> str:
        """Entity channel identifier"""
        return self.__channel

    # -----------------------------------------------------------------------------

    def to_dict(self) -> Dict:
        """Entity to dictionary converter"""
        return {**super().to_dict(), **{
            "channel": self.channel,
        }}

    # -----------------------------------------------------------------------------

    @property
    def allowed_attributes(self) -> List[str]:
        """List of entity allowed attributes"""
        return [
            self.NAME,
            self.PROPERTIES,
            self.CONTROLS,
        ]


class HardwareEntity(BaseEntity):
    """
    Device hardware message entity

    @package        FastyBird:MqttConnectorPlugin!
    @module         entities

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    MAC_ADDRESS = "mac-address"
    MANUFACTURER = "manufacturer"
    MODEL = "model"
    VERSION = "version"
    SN = "serial-number"

    __parameter: str
    __value: str

    # -----------------------------------------------------------------------------

    def __init__(self, device: str, parameter: str, value: str, parent: str or None = None) -> None:
        if parameter not in self.allowed_parameters:
            raise InvalidArgumentException(f"Provided hardware attribute '{parameter}' is not in allowed range")

        super().__init__(device=device, parent=parent)

        self.__parameter = parameter
        self.__value = clean_payload(value)

    # -----------------------------------------------------------------------------

    @property
    def parameter(self) -> str:
        """Entity parameter"""
        return self.__parameter

    # -----------------------------------------------------------------------------

    @property
    def value(self) -> str:
        """Entity parameter value"""
        return self.__value

    # -----------------------------------------------------------------------------

    def to_dict(self) -> Dict:
        """Entity to dictionary converter"""
        return {**super().to_dict(), **{
            self.parameter: self.value,
        }}

    # -----------------------------------------------------------------------------

    @property
    def allowed_parameters(self) -> List[str]:
        """List of entity allowed parameters"""
        return [
            self.MAC_ADDRESS,
            self.MANUFACTURER,
            self.MODEL,
            self.VERSION,
            self.SN,
        ]


class FirmwareEntity(BaseEntity):
    """
    Device firmware message entity

    @package        FastyBird:MqttConnectorPlugin!
    @module         entities

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    MANUFACTURER = "manufacturer"
    VERSION = "version"

    __parameter: str
    __value: str

    # -----------------------------------------------------------------------------

    def __init__(self, device: str, parameter: str, value: str, parent: str or None = None) -> None:
        if parameter not in self.allowed_parameters:
            raise InvalidArgumentException(f"Provided firmware attribute '{parameter}' is not in allowed range")

        super().__init__(device=device, parent=parent)

        self.__parameter = parameter
        self.__value = clean_payload(value)

    # -----------------------------------------------------------------------------

    @property
    def parameter(self) -> str:
        """Entity parameter"""
        return self.__parameter

    # -----------------------------------------------------------------------------

    @property
    def value(self) -> str:
        """Entity parameter value"""
        return self.__value

    # -----------------------------------------------------------------------------

    def to_dict(self) -> Dict:
        """Entity to dictionary converter"""
        return {**super().to_dict(), **{
            self.parameter: self.value,
        }}

    # -----------------------------------------------------------------------------

    @property
    def allowed_parameters(self) -> List[str]:
        """List of entity allowed parameters"""
        return [
            self.MANUFACTURER,
            self.VERSION,
        ]


class PropertyEntity(BaseEntity):
    """
    Base property message entity

    @package        FastyBird:MqttConnectorPlugin!
    @module         entities

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    __name: str
    __value: str or None = None
    __attributes: Set["PropertyAttributeEntity"] = set()

    # -----------------------------------------------------------------------------

    def __init__(self, device: str, name: str, parent: str or None = None) -> None:
        super().__init__(device=device, parent=parent)

        self.__name = name

    # -----------------------------------------------------------------------------

    @property
    def name(self) -> str:
        """Entity name"""
        return self.__name

    # -----------------------------------------------------------------------------

    @property
    def value(self) -> str or None:
        """Entity value"""
        return self.__value

    # -----------------------------------------------------------------------------

    @value.setter
    def value(self, value: str) -> None:
        """Entity value setter"""
        self.__value = value

    # -----------------------------------------------------------------------------

    @property
    def attributes(self) -> Set["PropertyAttributeEntity"]:
        """List of entity attributes"""
        return self.__attributes

    # -----------------------------------------------------------------------------

    def add_attribute(self, attribute: str, value: str) -> None:  # pylint: disable=too-many-branches
        """Validate and create property attribute"""
        if attribute not in PropertyAttributeEntity.allowed_attributes:
            raise ParseMessageException("Provided topic is not valid")

        cleaned_value = clean_payload(value)

        if attribute in (PropertyAttributeEntity.SETTABLE, PropertyAttributeEntity.QUERYABLE):
            cleaned_value = "true" if cleaned_value.lower() == "true" else "false"

        elif attribute == PropertyAttributeEntity.NAME:
            cleaned_value = clean_name(cleaned_value)

        elif attribute == PropertyAttributeEntity.DATA_TYPE:
            if DataType.has_value(cleaned_value) is False:
                raise ParseMessageException("Provided payload is not valid")

        elif attribute == PropertyAttributeEntity.FORMAT:
            if len(re.findall(r"([a-zA-Z0-9]+)?:([a-zA-Z0-9]+)?", ":")) == 1:
                start, end = re.findall(r"([a-zA-Z0-9]+)?:([a-zA-Z0-9]+)?", ":").pop()

                if start and start.isnumeric() is False:
                    raise ParseMessageException("Provided payload is not valid")

                if end and end.isnumeric() is False:
                    raise ParseMessageException("Provided payload is not valid")

                start = fast_real(start) if start else None
                end = fast_real(end) if end else None

                if start and end and start > end:
                    ParseMessageException("Provided payload is not valid")

                cleaned_value = f"{start}:{end}"

            elif "," in cleaned_value:
                cleaned_value = cleaned_value.strip().split(",")
                cleaned_value = [item.strip() for item in cleaned_value if item.strip()]

                cleaned_value = list(set(cleaned_value))

            elif cleaned_value == "none" or cleaned_value is False:
                cleaned_value = None

            elif cleaned_value not in PropertyAttributeEntity.FORMAT_ALLOWED_PAYLOADS:
                ParseMessageException("Provided payload is not valid")

        else:
            cleaned_value = None if cleaned_value == "none" or cleaned_value is False else cleaned_value

        self.__attributes.add(PropertyAttributeEntity(attribute=attribute, value=cleaned_value))

    # -----------------------------------------------------------------------------

    def to_dict(self) -> Dict:
        """Entity to dictionary converter"""
        result = {**super().to_dict(), **{
            "identifier": self.name,
        }}

        for attribute in self.attributes:
            result[attribute.attribute] = attribute.value

        if self.value is not None:
            result["value"] = self.value

        return result


class DevicePropertyEntity(PropertyEntity):
    """
    Device property message entity

    @package        FastyBird:MqttConnectorPlugin!
    @module         entities

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """


class ChannelPropertyEntity(PropertyEntity):
    """
    Channel property message entity

    @package        FastyBird:MqttConnectorPlugin!
    @module         entities

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    __channel: str

    # -----------------------------------------------------------------------------

    def __init__(self, device: str, channel: str, name: str, parent: str or None = None) -> None:
        super().__init__(device=device, name=name, parent=parent)

        self.__channel = channel

    # -----------------------------------------------------------------------------

    @property
    def channel(self) -> str:
        """Entity channel identifier"""
        return self.__channel

    # -----------------------------------------------------------------------------

    def to_dict(self) -> Dict:
        """Entity to dictionary converter"""
        return {**super().to_dict(), **{
            "channel": self.channel,
        }}


class PropertyAttributeEntity(ABC):
    """
    Property attribute entity

    @package        FastyBird:MqttConnectorPlugin!
    @module         entities

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    NAME = "name"
    SETTABLE = "settable"
    QUERYABLE = "queryable"
    DATA_TYPE = "data-type"
    FORMAT = "format"
    UNIT = "unit"

    FORMAT_ALLOWED_PAYLOADS = [
        "rgb",
        "hsv",
    ]

    __attribute: str
    __value: str or None = None

    # -----------------------------------------------------------------------------

    def __init__(self, attribute: str, value: str or None = None) -> None:
        if attribute not in self.allowed_attributes:
            raise InvalidArgumentException(f"Provided property parameter '{attribute}' is not in allowed range")

        self.__attribute = attribute
        self.__value = value

    # -----------------------------------------------------------------------------

    @property
    def attribute(self) -> str:
        """Entity attribute"""
        return self.__attribute

    # -----------------------------------------------------------------------------

    @property
    def value(self) -> str or bool or None:
        """Entity value"""
        if self.__value is None:
            return None

        if self.attribute in (self.SETTABLE, self.QUERYABLE):
            return self.__value == "true"

        return self.__value

    # -----------------------------------------------------------------------------

    def to_dict(self) -> Dict:
        """Entity to dictionary converter"""
        return {
            self.attribute: self.value,
        }

    # -----------------------------------------------------------------------------

    @property
    def allowed_attributes(self) -> List[str]:
        """List of entity allowed attributes"""
        return [
            self.NAME,
            self.SETTABLE,
            self.QUERYABLE,
            self.DATA_TYPE,
            self.FORMAT,
            self.UNIT,
        ]


class ControlEntity(BaseEntity):
    """
    Base control message entity

    @package        FastyBird:MqttConnectorPlugin!
    @module         entities

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    CONFIG = ControlName(ControlName.CONFIGURE).value
    RESET = ControlName(ControlName.RESET).value
    REBOOT = ControlName(ControlName.REBOOT).value
    RECONNECT = "reconnect"
    FACTORY_RESET = "factory-reset"
    OTA = "ota"

    TYPE_BOOLEAN = "boolean"
    TYPE_NUMBER = "number"
    TYPE_FLOAT = "float"
    TYPE_SELECT = "select"
    TYPE_TEXT = "text"

    __control: str
    __value: str or Dict[str, str or int or float or bool or None] or None = None
    __schema: Set[Dict[str, str or int or float or bool]] or None = None

    def __init__(self, device: str, control: str, parent: str or None = None):
        if control not in self.allowed_controls:
            raise InvalidArgumentException(f"Provided control '{control}' is not in allowed range")

        super().__init__(device=device, parent=parent)

    # -----------------------------------------------------------------------------

    @property
    def control(self) -> str:
        """Entity type"""
        return self.__control

    # -----------------------------------------------------------------------------

    @property
    def value(self) -> str or Dict[str, str or int or float or bool or None] or None:
        """Entity value"""
        return self.__value

    # -----------------------------------------------------------------------------

    @value.setter
    def value(self, value: str or None) -> None:
        """Entity value setter"""
        self.__value = value

        if self.control == self.CONFIG and value is not None:
            try:
                decoded_value = json.loads(value)

                if isinstance(decoded_value, dict):
                    self.__value = decoded_value

                else:
                    raise InvalidArgumentException("Received configuration value is not valid")

            except json.JSONDecodeError as ex:
                raise ParseMessageException('Control config payload is not valid JSON value') from ex

    # -----------------------------------------------------------------------------

    @property
    def schema(self) -> Set[Dict[str, str or int or float or bool]] or None:
        """Config control schema"""
        if self.control != self.CONFIG:
            raise InvalidStateException(f"Schema could be get only for '{self.CONFIG}' control type")

        return self.__schema

    # -----------------------------------------------------------------------------

    @schema.setter
    def schema(self, schema: str) -> None:  # pylint: disable=too-many-branches
        """Config control schema setter"""
        if self.control != self.CONFIG:
            raise InvalidStateException(f"Schema could be set only for '{self.CONFIG}' control type")

        try:
            decoded_schema = json.loads(schema)

            if isinstance(decoded_schema, (list, set)) is False:
                raise InvalidArgumentException("Received configuration value is not valid")

        except json.JSONDecodeError as ex:
            raise ParseMessageException("Control payload is not valid JSON value") from ex

        self.__schema = set()

        for row in decoded_schema:
            if "type" not in row or "name" not in row:
                continue

            row_type = row.get("type", None)

            schema_row = {
                "identifier": row.get("identifier"),
                "name": row.get("name") if "name" in row and row.get("name", None) else None,
                "comment": row.get("comment") if "comment" in row and row.get("comment", None) else None,
                "default": None,
            }

            schema_row_params = {
                "type": row_type,
            }

            if row_type in (self.TYPE_NUMBER, self.TYPE_FLOAT):
                if row_type == self.TYPE_NUMBER:
                    schema_row["data_type"] = DataType(DataType.INT)

                else:
                    schema_row["data_type"] = DataType(DataType.FLOAT)

                for field in ["min", "max", "step", "default"]:
                    schema_row_params[field] = fast_real(row.get(field)) if field in row else None

            elif row_type == self.TYPE_TEXT:
                schema_row["data_type"] = DataType(DataType.STRING)

                schema_row_params["default"] = str(row.get("default")) if "default" in row else None

            elif row_type == self.TYPE_BOOLEAN:
                schema_row["data_type"] = DataType(DataType.BOOLEAN)

                schema_row_params["default"] = bool(row.get("default", False)) if "default" in row else None

            elif row_type == self.TYPE_SELECT:
                schema_row["data_type"] = DataType(DataType.ENUM)

                schema_row_params["select_values"] = []

                if "values" in row and isinstance(row.get("values"), list):
                    for value in row.get("values"):
                        if isinstance(value, dict) and "value" in value and "name" in value:
                            schema_row_params["select_values"].append({
                                "value": value.get("value"),
                                "name": value.get("name"),
                            })

                schema_row_params["default"] = str(row.get("default")) if "default" in row else None

            schema_row["params"] = schema_row_params

            self.__schema.add(schema_row)

    # -----------------------------------------------------------------------------

    def to_dict(self) -> Dict:
        """Entity to dictionary converter"""
        result = {**super().to_dict(), **{
            "identifier": self.control,
        }}

        if self.value is not None:
            result["value"] = self.value

        if self.schema is not None and self.control == self.CONFIG:
            result["schema"] = self.schema

        return result

    # -----------------------------------------------------------------------------

    @property
    def allowed_controls(self) -> List[str]:
        """List of entity allowed controls"""
        return []


class DeviceControlEntity(ControlEntity):
    """
    Device control message entity

    @package        FastyBird:MqttConnectorPlugin!
    @module         entities

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    @property
    def allowed_controls(self) -> List[str]:
        """List of entity allowed controls"""
        return [
            self.CONFIG,
            self.RESET,
            self.RECONNECT,
            self.FACTORY_RESET,
            self.OTA,
        ]


class ChannelControlEntity(ControlEntity):
    """
    Channel control message entity

    @package        FastyBird:MqttConnectorPlugin!
    @module         entities

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    __channel: str

    # -----------------------------------------------------------------------------

    def __init__(self, device: str, channel: str, control: str, parent: str or None = None):
        super().__init__(device=device, control=control, parent=parent)

        self.__channel = channel

    # -----------------------------------------------------------------------------

    @property
    def channel(self) -> str:
        """Entity channel identifier"""
        return self.__channel

    # -----------------------------------------------------------------------------

    def to_dict(self) -> Dict:
        """Entity to dictionary converter"""
        return {**super().to_dict(), **{
            "channel": self.channel,
        }}

    # -----------------------------------------------------------------------------

    @property
    def allowed_controls(self) -> List[str]:
        """List of entity allowed controls"""
        return [
            self.CONFIG,
            self.RESET,
        ]
