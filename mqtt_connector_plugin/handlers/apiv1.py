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
MQTT connector plugin API v1 handler
"""

# Python base dependencies
from typing import Any, Dict, List, Optional

# Library dependencies
from kink import inject
from paho.mqtt.client import MQTT_ERR_SUCCESS, Client, MQTTMessage

# Library libs
from mqtt_connector_plugin.api.v1parser import V1Parser
from mqtt_connector_plugin.api.v1validator import V1Validator
from mqtt_connector_plugin.consumers.consumer import MessagesConsumer
from mqtt_connector_plugin.exceptions import (
    InvalidArgumentException,
    InvalidStateException,
    ParseMessageException,
)
from mqtt_connector_plugin.handlers.base import BaseHandler
from mqtt_connector_plugin.logger import Logger
from mqtt_connector_plugin.subscriptions.repository import SubscriptionsRepository


@inject(alias=BaseHandler)
class ApiV1Handler(BaseHandler):
    """
    MQTT topic v1 handler

    @package        FastyBird:MqttConnectorPlugin!
    @module         apiv1

    @author         Adam Kadlec <adam.kadlec@fastybird.com>
    """

    __API_TOPICS: List[str] = [
        "/fb/v1/+/+",
        "/fb/v1/+/+/+",
        "/fb/v1/+/+/+/+",
        "/fb/v1/+/+/+/+/+",
        "/fb/v1/+/+/+/+/+/+",
        "/fb/v1/+/+/+/+/+/+/+",
        "/fb/v1/+/$child/+/+",
        "/fb/v1/+/$child/+/+/+",
        "/fb/v1/+/$child/+/+/+/+",
        "/fb/v1/+/$child/+/+/+/+/+",
        "/fb/v1/+/$child/+/+/+/+/+/+",
        "/fb/v1/+/$child/+/+/+/+/+/+/+",
    ]

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

    def on_connect(self, client: Client, userdata: Any, flags: Dict, response_code: Optional[int]) -> None:
        """On connection to broker established event"""
        for topic in self.__API_TOPICS:
            result, message_id = client.subscribe(topic=topic, qos=0)

            if result == MQTT_ERR_SUCCESS:
                self.__subscriptions_repository.create(topic=topic, qos=0, mid=message_id)

    # -----------------------------------------------------------------------------

    def on_disconnect(self, client: Client, userdata: Any, response_code: Optional[int]) -> None:
        """On connection to broker closed event"""

    # -----------------------------------------------------------------------------

    def on_log(self, client: Client, userdata: Any, level: int, buf: str) -> None:
        """On log message result"""

    # -----------------------------------------------------------------------------

    def on_subscribe(self, client: Client, userdata: Any, message_id: int, granted_qos: int) -> None:
        """On topic subscribed event"""

    # -----------------------------------------------------------------------------

    def on_unsubscribe(self, client: Client, userdata: Any, message_id: int) -> None:
        """On topic unsubscribed event"""

    # -----------------------------------------------------------------------------

    def on_message(self, client: Client, userdata: Any, message: MQTTMessage) -> None:
        """On broker message event"""
        if (
            V1Validator.validate_convention(message.topic) is False
            or V1Validator.validate_version(message.topic) is False
            or V1Validator.validate_is_command(message.topic) is True
        ):
            return

        if V1Validator.validate(message.topic) is False:
            self._logger.warning(
                "Received topic is not valid MQTT v1 convention topic: %s",
                message.topic,
            )

            return

        client_id = self.extract_client_id(userdata=userdata)

        if client_id is None:
            return

        try:
            entity = V1Parser.parse_message(
                client_id=client_id,
                topic=message.topic,
                payload=message.payload.decode("utf-8", "ignore"),
                retained=message.retain,
            )

        except ParseMessageException as ex:
            self._logger.error("Received message could not be successfully parsed to entity")
            self._logger.exception(ex)

            return

        except (InvalidArgumentException, InvalidStateException) as ex:
            self._logger.error("One or more parsed values from message are not valid")
            self._logger.exception(ex)

            return

        self.__consumer.append(entity=entity)
