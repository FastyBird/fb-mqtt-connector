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
FastyBird MQTT connector plugin handlers module handler for API v1
"""

# Python base dependencies
from typing import List

# Library dependencies
from kink import inject

# Library libs
from fb_mqtt_connector_plugin.api.v1parser import V1Parser
from fb_mqtt_connector_plugin.api.v1validator import V1Validator
from fb_mqtt_connector_plugin.clients.base import BaseClient
from fb_mqtt_connector_plugin.consumers.consumer import Consumer
from fb_mqtt_connector_plugin.exceptions import (
    InvalidArgumentException,
    InvalidStateException,
    ParsePayloadException,
)
from fb_mqtt_connector_plugin.handlers.base import BaseHandler
from fb_mqtt_connector_plugin.logger import Logger
from fb_mqtt_connector_plugin.subscriptions.repository import SubscriptionsRepository


@inject(alias=BaseHandler)
class ApiV1Handler(BaseHandler):
    """
    MQTT topic v1 handler

    @package        FastyBird:FbMqttConnectorPlugin!
    @module         handlers

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
        for topic in self.__API_TOPICS:
            result, message_id = client.subscribe(topic=topic, qos=0)

            if result and message_id is not None:
                self.__subscriptions_repository.create(topic=topic, qos=0, mid=message_id)

    # -----------------------------------------------------------------------------

    def on_disconnect(self, client: BaseClient) -> None:
        """On connection to broker closed event"""

    # -----------------------------------------------------------------------------

    def on_log(self, client: BaseClient, level: int, message: str) -> None:
        """On log message result"""

    # -----------------------------------------------------------------------------

    def on_subscribe(self, client: BaseClient, message_id: int, granted_qos: int) -> None:
        """On topic subscribed event"""

    # -----------------------------------------------------------------------------

    def on_unsubscribe(self, client: BaseClient, message_id: int) -> None:
        """On topic unsubscribed event"""

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
        if (
            V1Validator.validate_convention(topic) is False
            or V1Validator.validate_version(topic) is False
            or V1Validator.validate_is_command(topic) is True
        ):
            return

        if V1Validator.validate(topic) is False:
            self._logger.warning(
                "Received topic is not valid MQTT v1 convention topic: %s",
                topic,
            )

            return

        try:
            entity = V1Parser.parse_message(
                client_id=client.id,
                topic=topic,
                payload=payload,
                retained=retained,
            )

        except ParsePayloadException as ex:
            self._logger.error("Received message could not be successfully parsed to entity")
            self._logger.exception(ex)

            return

        except (InvalidArgumentException, InvalidStateException) as ex:
            self._logger.error("One or more parsed values from message are not valid")
            self._logger.exception(ex)

            return

        self.__consumer.append(entity=entity)
