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
FastyBird MQTT connector plugin consumers module entities
"""

# Python base dependencies
import json
import re
import uuid
from abc import ABC
from typing import Dict, List, Optional, Set, Tuple, Union

# Library dependencies
from fastnumbers import fast_real
from modules_metadata.devices_module import (
    ConfigurationBooleanFieldAttribute,
    ConfigurationField,
    ConfigurationNumberFieldAttribute,
    ConfigurationSelectFieldAttribute,
    ConfigurationTextFieldAttribute,
)
from modules_metadata.types import ControlName, DataType

# Library libs
from fb_mqtt_connector_plugin.exceptions import (
    InvalidArgumentException,
    InvalidStateException,
    ParsePayloadException,
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

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         consumers

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __client_id: uuid.UUID
    __device: str
    __parent: Optional[str] = None
    __retained: bool = False

    # -----------------------------------------------------------------------------

    def __init__(self, client_id: uuid.UUID, device: str, parent: Optional[str] = None) -> None:
        self.__client_id = client_id
        self.__device = device
        self.__parent = parent

    # -----------------------------------------------------------------------------

    @property
    def client_id(self) -> uuid.UUID:
        """Connector unique identifier"""
        return self.__client_id

    # -----------------------------------------------------------------------------

    @property
    def device(self) -> str:
        """Entity device identifier"""
        return self.__device

    # -----------------------------------------------------------------------------

    @property
    def parent(self) -> Optional[str]:
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


class AttributeEntity(BaseEntity):
    """
    Base attribute message entity

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         consumers

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    NAME = "name"
    PROPERTIES = "devices"
    STATE = "state"
    CHANNELS = "channels"
    EXTENSIONS = "extensions"
    CONTROLS = "controls"

    __attribute: str
    __value: Union[str, List[str]]

    # -----------------------------------------------------------------------------

    def __init__(  # pylint: disable=too-many-arguments
        self,
        client_id: uuid.UUID,
        device: str,
        attribute: str,
        value: str,
        parent: Optional[str] = None,
    ) -> None:
        if attribute not in self.allowed_attributes:
            raise InvalidArgumentException(f"Provided attribute '{attribute}' is not in allowed range")

        super().__init__(client_id=client_id, device=device, parent=parent)

        self.__attribute = attribute
        self.__parse_value(value)

    # -----------------------------------------------------------------------------

    @property
    def attribute(self) -> str:
        """Entity attribute"""
        return self.__attribute

    # -----------------------------------------------------------------------------

    @property
    def value(self) -> Union[str, List[str]]:
        """Entity value"""
        return self.__value

    # -----------------------------------------------------------------------------

    @property
    def allowed_attributes(self) -> List[str]:
        """List of entity allowed attributes"""
        return []

    # -----------------------------------------------------------------------------

    def __parse_value(self, value: str) -> None:
        """Parse value against entity attribute type"""
        if self.attribute == self.NAME:
            self.__value = clean_name(value)

        elif self.attribute in (
            self.PROPERTIES,
            self.CHANNELS,
            self.EXTENSIONS,
            self.CONTROLS,
        ):
            cleaned_value = clean_payload(value)

            cleaned_value_parts = cleaned_value.strip().split(",")
            cleaned_value_parts = [item.strip() for item in cleaned_value_parts if item.strip()]

            self.__value = list(set(cleaned_value_parts))

        else:
            self.__value = clean_payload(value)


class DeviceAttributeEntity(AttributeEntity):
    """
    Device attribute message entity

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         consumers

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

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         consumers

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __channel: str

    # -----------------------------------------------------------------------------

    def __init__(  # pylint: disable=too-many-arguments
        self,
        client_id: uuid.UUID,
        device: str,
        channel: str,
        attribute: str,
        value: str,
        parent: Optional[str] = None,
    ) -> None:
        super().__init__(
            client_id=client_id,
            device=device,
            attribute=attribute,
            value=value,
            parent=parent,
        )

        self.__channel = channel

    # -----------------------------------------------------------------------------

    @property
    def channel(self) -> str:
        """Entity channel identifier"""
        return self.__channel

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

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         consumers

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

    def __init__(  # pylint: disable=too-many-arguments
        self,
        client_id: uuid.UUID,
        device: str,
        parameter: str,
        value: str,
        parent: Optional[str] = None,
    ) -> None:
        if parameter not in self.allowed_parameters:
            raise InvalidArgumentException(f"Provided hardware attribute '{parameter}' is not in allowed range")

        super().__init__(client_id=client_id, device=device, parent=parent)

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

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         consumers

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    MANUFACTURER = "manufacturer"
    VERSION = "version"

    __parameter: str
    __value: str

    # -----------------------------------------------------------------------------

    def __init__(  # pylint: disable=too-many-arguments
        self,
        client_id: uuid.UUID,
        device: str,
        parameter: str,
        value: str,
        parent: Optional[str] = None,
    ) -> None:
        if parameter not in self.allowed_parameters:
            raise InvalidArgumentException(f"Provided firmware attribute '{parameter}' is not in allowed range")

        super().__init__(client_id=client_id, device=device, parent=parent)

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

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         consumers

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __name: str
    __value: Optional[str] = None
    __attributes: Set["PropertyAttributeEntity"] = set()

    # -----------------------------------------------------------------------------

    def __init__(
        self,
        client_id: uuid.UUID,
        device: str,
        name: str,
        parent: Optional[str] = None,
    ) -> None:
        super().__init__(client_id=client_id, device=device, parent=parent)

        self.__name = name

    # -----------------------------------------------------------------------------

    @property
    def name(self) -> str:
        """Entity name"""
        return self.__name

    # -----------------------------------------------------------------------------

    @property
    def value(self) -> Optional[str]:
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

    def add_attribute(self, attribute: "PropertyAttributeEntity") -> None:
        """Validate and create property attribute"""
        self.__attributes.add(attribute)


class DevicePropertyEntity(PropertyEntity):
    """
    Device property message entity

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         consumers

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """


class ChannelPropertyEntity(PropertyEntity):
    """
    Channel property message entity

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         consumers

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __channel: str

    # -----------------------------------------------------------------------------

    def __init__(  # pylint: disable=too-many-arguments
        self,
        client_id: uuid.UUID,
        device: str,
        channel: str,
        name: str,
        parent: Optional[str] = None,
    ) -> None:
        super().__init__(client_id=client_id, device=device, name=name, parent=parent)

        self.__channel = channel

    # -----------------------------------------------------------------------------

    @property
    def channel(self) -> str:
        """Entity channel identifier"""
        return self.__channel


class PropertyAttributeEntity(ABC):
    """
    Property attribute entity

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         consumers

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
    __value: Union[str, bool, Tuple[float, float], List[str], DataType, None] = None

    # -----------------------------------------------------------------------------

    def __init__(self, attribute: str, value: str) -> None:
        if attribute not in self.allowed_attributes:
            raise InvalidArgumentException(f"Provided property parameter '{attribute}' is not in allowed range")

        self.__attribute = attribute
        self.__parse_value(value=value)

    # -----------------------------------------------------------------------------

    @property
    def attribute(self) -> str:
        """Entity attribute"""
        return self.__attribute

    # -----------------------------------------------------------------------------

    @property
    def value(self) -> Union[str, bool, Tuple[float, float], List[str], DataType, None]:
        """Entity value"""
        if self.__value is None:
            return None

        if self.attribute in (self.SETTABLE, self.QUERYABLE):
            return self.__value == "true"

        return self.__value

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

    # -----------------------------------------------------------------------------

    def __parse_value(self, value: str) -> None:  # pylint: disable=too-many-branches
        cleaned_value = clean_payload(value)

        if self.attribute in (
            PropertyAttributeEntity.SETTABLE,
            PropertyAttributeEntity.QUERYABLE,
        ):
            self.__value = bool(cleaned_value.lower() == "true")

        elif self.attribute == PropertyAttributeEntity.NAME:
            self.__value = clean_name(cleaned_value)

        elif self.attribute == PropertyAttributeEntity.DATA_TYPE:
            if not DataType.has_value(cleaned_value):
                raise ParsePayloadException("Provided payload is not valid")

            self.__value = DataType(cleaned_value)

        elif self.attribute == PropertyAttributeEntity.FORMAT:
            if len(re.findall(r"([a-zA-Z0-9]+)?:([a-zA-Z0-9]+)?", ":")) == 1:
                start, end = re.findall(r"([a-zA-Z0-9]+)?:([a-zA-Z0-9]+)?", ":").pop()

                if start and start.isnumeric() is False:
                    raise ParsePayloadException("Provided payload is not valid")

                if end and end.isnumeric() is False:
                    raise ParsePayloadException("Provided payload is not valid")

                start = fast_real(start) if start else None
                end = fast_real(end) if end else None

                if start and end and start > end:
                    raise ParsePayloadException("Provided payload is not valid")

                self.__value = start, end

            elif "," in cleaned_value:
                cleaned_value_parts = cleaned_value.strip().split(",")
                cleaned_value_parts = [item.strip() for item in cleaned_value_parts if item.strip()]

                self.__value = list(set(cleaned_value_parts))

            elif cleaned_value == "none" or cleaned_value is False:
                self.__value = None

            elif cleaned_value not in PropertyAttributeEntity.FORMAT_ALLOWED_PAYLOADS:
                raise ParsePayloadException("Provided payload is not valid")

            else:
                self.__value = cleaned_value

        else:
            self.__value = None if cleaned_value == "none" or cleaned_value is False else cleaned_value


class ControlEntity(BaseEntity):
    """
    Base control message entity

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         consumers

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    CONFIG = ControlName.CONFIGURE.value
    RESET = ControlName.RESET.value
    REBOOT = ControlName.REBOOT.value
    RECONNECT = "reconnect"
    FACTORY_RESET = "factory-reset"
    OTA = "ota"

    __control: str
    __value: Union[str, Dict[str, Union[str, int, float, bool, None]], None] = None
    __schema: Union[
        Set[
            Dict[
                str,
                Union[
                    str,
                    int,
                    float,
                    bool,
                    Dict[
                        str,
                        Union[
                            str,
                            int,
                            float,
                            List[Dict[str, Union[str, int, float]]],
                            None,
                        ],
                    ],
                    None,
                ],
            ],
        ],
        None,
    ] = None

    def __init__(
        self,
        client_id: uuid.UUID,
        device: str,
        control: str,
        parent: Optional[str] = None,
    ):
        if control not in self.allowed_controls:
            raise InvalidArgumentException(f"Provided control '{control}' is not in allowed range")

        super().__init__(client_id=client_id, device=device, parent=parent)

    # -----------------------------------------------------------------------------

    @property
    def control(self) -> str:
        """Entity type"""
        return self.__control

    # -----------------------------------------------------------------------------

    @property
    def value(self) -> Union[str, Dict[str, Union[str, int, float, bool, None]], None]:
        """Entity value"""
        return self.__value

    # -----------------------------------------------------------------------------

    def set_value(self, value: Optional[str]) -> None:
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
                raise ParsePayloadException("Control config payload is not valid JSON value") from ex

    # -----------------------------------------------------------------------------

    @property
    def schema(
        self,
    ) -> Union[
        Set[
            Dict[
                str,
                Union[
                    str,
                    int,
                    float,
                    bool,
                    Dict[
                        str,
                        Union[
                            str,
                            int,
                            float,
                            List[Dict[str, Union[str, int, float]]],
                            None,
                        ],
                    ],
                    None,
                ],
            ],
        ],
        None,
    ]:
        """Config control schema"""
        if self.control != self.CONFIG:
            raise InvalidStateException(f"Schema could be get only for '{self.CONFIG}' control type")

        return self.__schema

    # -----------------------------------------------------------------------------

    def set_schema(self, schema: str) -> None:  # pylint: disable=too-many-branches
        """Config control schema setter"""
        if self.control != self.CONFIG:
            raise InvalidStateException(f"Schema could be set only for '{self.CONFIG}' control type")

        try:
            decoded_schema = json.loads(schema)

            if isinstance(decoded_schema, (list, set)) is False:
                raise InvalidArgumentException("Received configuration value is not valid")

        except json.JSONDecodeError as ex:
            raise ParsePayloadException("Control payload is not valid JSON value") from ex

        self.__schema = set()

        for row in decoded_schema:
            if "type" not in row or "identifier" not in row or "name" not in row:
                continue

            row_type = row.get("type", None)

            schema_row = {
                "identifier": row.get("identifier"),
                "type": row.get("type"),
                "name": row.get("name"),
                "comment": row.get("comment") if "comment" in row and row.get("comment", None) else None,
                "default": None,
            }

            if row_type == ConfigurationField.NUMBER.value:
                schema_row["data_type"] = DataType.FLOAT

                for number_field in ConfigurationNumberFieldAttribute:
                    schema_row[number_field.value] = (
                        fast_real(row.get(number_field.value)) if number_field.value in row else None
                    )

            elif row_type == ConfigurationField.TEXT.value:
                schema_row["data_type"] = DataType.STRING

                for text_field in ConfigurationTextFieldAttribute:
                    schema_row[text_field.value] = str(row.get(text_field.value)) if text_field.value in row else None

            elif row_type == ConfigurationField.BOOLEAN.value:
                schema_row["data_type"] = DataType.BOOLEAN

                for boolean_field in ConfigurationBooleanFieldAttribute:
                    schema_row[boolean_field.value] = (
                        str(row.get(boolean_field.value)) if boolean_field.value in row else None
                    )

            elif row_type == ConfigurationField.SELECT.value:
                schema_row["data_type"] = DataType.ENUM

                select_values = []

                if ConfigurationSelectFieldAttribute.VALUES.value in row and isinstance(
                    row.get(ConfigurationSelectFieldAttribute.VALUES.value), list
                ):
                    for value in row.get(ConfigurationSelectFieldAttribute.VALUES.value):
                        if isinstance(value, dict) and "value" in value and "name" in value:
                            select_values.append(
                                {
                                    "value": str(value.get("value")),
                                    "name": str(value.get("name")),
                                }
                            )

                schema_row[ConfigurationSelectFieldAttribute.VALUES.value] = select_values

                schema_row[ConfigurationSelectFieldAttribute.DEFAULT.value] = (
                    str(row.get("default")) if "default" in row else None
                )

            self.__schema.add(schema_row)

    # -----------------------------------------------------------------------------

    @property
    def allowed_controls(self) -> List[str]:
        """List of entity allowed controls"""
        return []


class DeviceControlEntity(ControlEntity):
    """
    Device control message entity

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         consumers

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

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         consumers

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __channel: str

    # -----------------------------------------------------------------------------

    def __init__(  # pylint: disable=too-many-arguments
        self,
        client_id: uuid.UUID,
        device: str,
        channel: str,
        control: str,
        parent: Optional[str] = None,
    ):
        super().__init__(client_id=client_id, device=device, control=control, parent=parent)

        self.__channel = channel

    # -----------------------------------------------------------------------------

    @property
    def channel(self) -> str:
        """Entity channel identifier"""
        return self.__channel

    # -----------------------------------------------------------------------------

    @property
    def allowed_controls(self) -> List[str]:
        """List of entity allowed controls"""
        return [
            self.CONFIG,
            self.RESET,
        ]
