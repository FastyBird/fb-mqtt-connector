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
FastyBird MQTT connector plugin handlers module common handler
"""

# Python base dependencies
import re
from typing import List, Optional

# Library dependencies
from kink import inject
from modules_metadata.devices_module import DevicePropertyName

# Library libs
from fb_mqtt_connector_plugin.clients.base import BaseClient
from fb_mqtt_connector_plugin.consumers.consumer import Consumer
from fb_mqtt_connector_plugin.consumers.entities import DevicePropertyEntity
from fb_mqtt_connector_plugin.handlers.base import BaseHandler
from fb_mqtt_connector_plugin.logger import Logger
from fb_mqtt_connector_plugin.subscriptions.repository import SubscriptionsRepository


@inject(alias=BaseHandler)
class CommonHandler(BaseHandler):
    """
    MQTT topic common handler

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         handlers

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __COMMON_TOPICS: List[str] = [
        "$SYS/broker/log/#",
    ]

    __SYS_TOPIC_REGEX = r"^\$SYS\/broker\/log\/([a-zA-Z0-9]+)?"
    __NEW_CLIENT_MESSAGE_PAYLOAD = "New client connected from"

    __subscriptions_repository: SubscriptionsRepository
    __consumer: Consumer

    # -----------------------------------------------------------------------------

    def __init__(
        self,
        subscriptions_repository: SubscriptionsRepository,
        consumer: Consumer,
        logger: Logger,
    ) -> None:
        BaseHandler.__init__(self, logger=logger)

        self.__subscriptions_repository = subscriptions_repository
        self.__consumer = consumer

    # -----------------------------------------------------------------------------

    def on_connect(self, client: BaseClient) -> None:
        """On connection to broker established event"""
        self._logger.info("Connected to MQTT broker")

        for topic in self.__COMMON_TOPICS:
            result, message_id = client.subscribe(topic=topic, qos=0)

            if result and message_id is not None:
                self.__subscriptions_repository.create(topic=topic, qos=0, mid=message_id)

    # -----------------------------------------------------------------------------

    def on_disconnect(self, client: BaseClient) -> None:
        """On connection to broker closed event"""
        self._logger.info("Disconnected from MQTT broker")

    # -----------------------------------------------------------------------------

    def on_log(self, client: BaseClient, level: int, message: str) -> None:
        """On log message result"""

    # -----------------------------------------------------------------------------

    def on_subscribe(self, client: BaseClient, message_id: int, granted_qos: int) -> None:
        """On topic subscribed event"""
        subscription = self.__subscriptions_repository.get_by_id(mid=message_id)

        if subscription is not None:
            self._logger.info("Subscribed to topic: %s", subscription.topic)

        else:
            self._logger.warning("Subscribed to unknown topic")

    # -----------------------------------------------------------------------------

    def on_unsubscribe(self, client: BaseClient, message_id: int) -> None:
        """On topic unsubscribed event"""
        subscription = self.__subscriptions_repository.get_by_id(mid=message_id)

        if subscription is not None:
            self.__subscriptions_repository.delete(subscription=subscription)

            self._logger.info("Unsubscribed from topic: %s", subscription.topic)

        else:
            self._logger.warning("Unsubscribed from unknown topic")

    # -----------------------------------------------------------------------------

    def on_message(  # pylint: disable=too-many-arguments
        self,
        client: BaseClient,
        topic: str,
        payload: str,
        qos: int,
        retained: bool,
    ) -> None:
        """On broker message event"""
        if len(re.findall(self.__SYS_TOPIC_REGEX, topic)) == 1:
            result: List[tuple] = re.findall(self.__SYS_TOPIC_REGEX, topic)
            log_level = str(result.pop()).lower()

            if log_level == "n":
                self._logger.info(payload)

                if self.__NEW_CLIENT_MESSAGE_PAYLOAD in payload:
                    payload_parts = payload.split(",")

                    try:
                        ip_address: Optional[str] = payload_parts[5]

                    except IndexError:
                        ip_address = None

                    try:
                        device_id: Optional[str] = payload_parts[7]

                    except IndexError:
                        device_id = None

                    try:
                        username: Optional[str] = payload_parts[10]

                    except IndexError:
                        username = None

                    if ip_address and device_id and username:
                        entity = DevicePropertyEntity(
                            client_id=client.id,
                            device=device_id,
                            name=DevicePropertyName.IP_ADDRESS.value,
                        )
                        entity.value = ip_address

                        self.__consumer.append(entity=entity)

            elif log_level == "e":
                self._logger.error(payload)

            elif log_level == "i":
                self._logger.info(payload)

            else:
                self._logger.debug(payload)
