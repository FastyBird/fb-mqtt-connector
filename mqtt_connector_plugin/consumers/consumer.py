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
MQTT messages consumers
"""

# Python base dependencies
from abc import ABC, abstractmethod
from queue import Full as QueueFull
from queue import Queue
from typing import List, Optional, Set

# Library dependencies
from kink import inject

# Library libs
from mqtt_connector_plugin.entities.entities import BaseEntity
from mqtt_connector_plugin.exceptions import InvalidStateException
from mqtt_connector_plugin.logger import Logger


class IConsumer(ABC):  # pylint: disable=too-few-public-methods
    """
    Entity consumer interface

    @package        FastyBird:MqttConnectorPlugin!
    @module         consumer

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    @abstractmethod
    def consume(self, entity: BaseEntity) -> None:
        """Consume provided received entity"""


@inject
class MessagesConsumer:
    """
    MQTT parsed entities consumer

    @package        FastyBird:MqttConnectorPlugin!
    @module         consumer

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __consumers: Set[IConsumer]
    __queue: Queue

    __logger: Logger

    # -----------------------------------------------------------------------------

    @inject
    def __init__(
        self,
        logger: Logger,
        consumers: Optional[List[IConsumer]] = None,
    ):
        if consumers is None:
            self.__consumers = set()

        else:
            self.__consumers = set(consumers)

        self.__logger = logger

        self.__queue = Queue(maxsize=1000)

    # -----------------------------------------------------------------------------

    def append(self, entity: BaseEntity) -> None:
        """Append new entity for consume"""
        try:
            self.__queue.put(item=entity)

        except QueueFull:
            self.__logger.error("Connector processing queue is full. New messages could not be added")

    # -----------------------------------------------------------------------------

    def consume(self) -> None:
        """Consume received message"""
        try:
            if not self.__queue.empty():
                entity = self.__queue.get()

                if isinstance(entity, BaseEntity):
                    for consumer in self.__consumers:
                        consumer.consume(entity=entity)

        except InvalidStateException as ex:
            self.__logger.error("Received message could not be consumed")
            self.__logger.exception(ex)

    # -----------------------------------------------------------------------------

    def is_empty(self) -> bool:
        """Check if all messages are consumed"""
        return self.__queue.empty()

    # -----------------------------------------------------------------------------

    def register_consumer(
        self,
        consumer: IConsumer,
    ) -> None:
        """Register new consumer to proxy"""
        self.__consumers.add(consumer)
