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
MQTT connector plugin common handler
"""

# Library dependencies
import re
from typing import List
from kink import inject
from modules_metadata.devices_module import DevicePropertyName
from paho.mqtt.client import Client, MQTTMessage, MQTT_ERR_SUCCESS

# Library libs
from mqtt_connector_plugin.consumers.consumer import MessagesConsumer
from mqtt_connector_plugin.handlers.base import BaseHandler
from mqtt_connector_plugin.logger import Logger
from mqtt_connector_plugin.entities.entities import DevicePropertyEntity
from mqtt_connector_plugin.subscriptions.repository import SubscriptionsRepository


@inject(alias=BaseHandler)
class CommonHandler(BaseHandler):
    """
    MQTT topic common handler

    @package        FastyBird:MqttConnectorPlugin!
    @module         common

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """
    __COMMON_TOPICS: List[str] = [
        "$SYS/broker/log/#",
    ]

    __SYS_TOPIC_REGEX = r"^\$SYS\/broker\/log\/([a-zA-Z0-9]+)?"
    __NEW_CLIENT_MESSAGE_PAYLOAD = "New client connected from"

    __subscriptions_repository: SubscriptionsRepository
    __consumer: MessagesConsumer

    # -----------------------------------------------------------------------------

    def __init__(
        self,
        subscriptions_repository: SubscriptionsRepository,
        consumer: MessagesConsumer,
        logger: Logger,
    ) -> None:
        BaseHandler.__init__(self, logger=logger)

        self.__subscriptions_repository = subscriptions_repository
        self.__consumer = consumer

    # -----------------------------------------------------------------------------

    def on_connect(self, client: Client, userdata, flags, response_code) -> None:
        """On connection to broker established event"""
        self._logger.info("Connected to MQTT broker")

        for topic in self.__COMMON_TOPICS:
            result, message_id = client.subscribe(topic=topic, qos=0)

            if result == MQTT_ERR_SUCCESS:
                self.__subscriptions_repository.create(topic=topic, qos=0, mid=message_id)

    # -----------------------------------------------------------------------------

    def on_disconnect(self, client: Client, userdata, response_code) -> None:
        """On connection to broker closed event"""
        self._logger.info("Disconnected from MQTT broker")

    # -----------------------------------------------------------------------------

    def on_log(self, client: Client, userdata, level, buf) -> None:
        """On log message result"""

    # -----------------------------------------------------------------------------

    def on_subscribe(self, client: Client, userdata, message_id, granted_qos) -> None:
        """On topic subscribed event"""
        subscription = self.__subscriptions_repository.get_by_id(mid=message_id)

        if subscription is not None:
            self._logger.info("Subscribed to topic: %s", subscription.topic)

        else:
            self._logger.warning("Subscribed to unknown topic")

    # -----------------------------------------------------------------------------

    def on_unsubscribe(self, client: Client, userdata, message_id) -> None:
        """On topic unsubscribed event"""
        subscription = self.__subscriptions_repository.get_by_id(mid=message_id)

        if subscription is not None:
            self.__subscriptions_repository.delete(subscription=subscription)

            self._logger.info("Unsubscribed from topic: %s", subscription.topic)

        else:
            self._logger.warning("Unsubscribed from unknown topic")

    # -----------------------------------------------------------------------------

    def on_message(self, client: Client, userdata, message: MQTTMessage) -> None:
        """On broker message event"""
        if len(re.findall(self.__SYS_TOPIC_REGEX, message.topic)) == 1:
            result: List[tuple] = re.findall(self.__SYS_TOPIC_REGEX, message.topic)
            log_level = str(result.pop()).lower()

            if log_level == "n":
                self._logger.info(message.payload)

                if self.__NEW_CLIENT_MESSAGE_PAYLOAD in message.payload:
                    payload_parts = message.payload.split(",") + \
                                    [None, None, None, None, None, None, None, None, None, None, None]

                    ip_address = payload_parts[5]
                    device_id = payload_parts[7]
                    username = payload_parts[10]

                    if ip_address and device_id and username:
                        entity = DevicePropertyEntity(
                            device=device_id,
                            name=DevicePropertyName.IP_ADDRESS.value,
                        )
                        entity.value = ip_address

                        self.__consumer.append(entity=entity)

            elif log_level == "e":
                self._logger.error(message.payload)

            elif log_level == "i":
                self._logger.info(message.payload)

            else:
                self._logger.debug(message.payload)
