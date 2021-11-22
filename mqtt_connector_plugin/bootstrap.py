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
MQTT connector plugin DI container
"""

# pylint: disable=no-value-for-parameter

# Python base dependencies
import logging

# Library dependencies
from kink import di

# Library libs
from mqtt_connector_plugin.client import Client, PahoClientFactory
from mqtt_connector_plugin.connector import MqttConnector
from mqtt_connector_plugin.consumers.consumer import MessagesConsumer
from mqtt_connector_plugin.handlers.apiv1 import ApiV1Handler
from mqtt_connector_plugin.handlers.common import CommonHandler
from mqtt_connector_plugin.handlers.handler import MessagesHandler
from mqtt_connector_plugin.logger import Logger
from mqtt_connector_plugin.publishers.apiv1 import ApiV1Publisher
from mqtt_connector_plugin.publishers.publisher import MessagesPublisher
from mqtt_connector_plugin.subscriptions.repository import SubscriptionsRepository


def create_container(logger: logging.Logger = logging.getLogger("dummy")) -> None:
    """Create FB MQTT connector services"""
    di[Logger] = Logger(logger=logger)
    di["fb-mqtt-connector-plugin_logger"] = di[Logger]

    di[SubscriptionsRepository] = SubscriptionsRepository()
    di["fb-mqtt-connector-plugin_subscription-repository"] = di[SubscriptionsRepository]

    # Entities consumers
    di[MessagesConsumer] = MessagesConsumer(
        logger=di[Logger],
    )
    di["fb-mqtt-connector-plugin_consumer-proxy"] = di[MessagesConsumer]

    # Clients handlers
    di[CommonHandler] = CommonHandler(
        subscriptions_repository=di[SubscriptionsRepository],
        consumer=di[MessagesConsumer],
        logger=di[Logger],
    )
    di["fb-mqtt-connector-plugin_mqtt-handler-common"] = di[CommonHandler]
    di[ApiV1Handler] = ApiV1Handler(
        subscriptions_repository=di[SubscriptionsRepository],
        consumer=di[MessagesConsumer],
        logger=di[Logger],
    )
    di["fb-mqtt-connector-plugin_mqtt-handler-api-v1"] = di[ApiV1Handler]

    # Messages handler proxy
    di[MessagesHandler] = MessagesHandler()  # type: ignore[call-arg]
    di["fb-mqtt-connector-plugin_mqtt-handler-proxy"] = di[MessagesHandler]

    di[Client] = Client(logger=di[Logger])
    di["fb-mqtt-connector-plugin_clients-proxy"] = di[Client]

    di[PahoClientFactory] = PahoClientFactory(
        client=di[Client],
        messages_handler=di[MessagesHandler],
        logger=di[Logger],
    )
    di["fb-mqtt-connector-plugin_paho-client-factory"] = di[PahoClientFactory]

    # MQTT messages publishers
    di[ApiV1Publisher] = ApiV1Publisher(
        client=di[Client],
        logger=di[Logger],
    )
    di["fb-mqtt-connector-plugin_publisher-api-v1"] = di[ApiV1Publisher]

    di[MessagesPublisher] = MessagesPublisher()  # type: ignore[call-arg]
    di["fb-mqtt-connector-plugin_publisher-proxy"] = di[MessagesPublisher]

    # MQTT connector
    di[MqttConnector] = MqttConnector(
        mqtt_client=di[Client],
        consumer=di[MessagesConsumer],
        logger=di[Logger],
    )
    di["fb-mqtt-connector-plugin_connector"] = di[MqttConnector]
