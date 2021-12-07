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
FastyBird MQTT connector plugin DI container
"""

# pylint: disable=no-value-for-parameter

# Python base dependencies
import logging

# Library dependencies
from kink import di

# Library libs
from fb_mqtt_connector_plugin.clients.client import Client, ClientFactory
from fb_mqtt_connector_plugin.connector import FbMqttConnector
from fb_mqtt_connector_plugin.consumers.consumer import Consumer
from fb_mqtt_connector_plugin.handlers.apiv1 import ApiV1Handler
from fb_mqtt_connector_plugin.handlers.common import CommonHandler
from fb_mqtt_connector_plugin.handlers.handler import Handler
from fb_mqtt_connector_plugin.logger import Logger
from fb_mqtt_connector_plugin.publishers.apiv1 import ApiV1Publisher
from fb_mqtt_connector_plugin.publishers.publisher import Publisher
from fb_mqtt_connector_plugin.subscriptions.repository import SubscriptionsRepository


def create_container(logger: logging.Logger = logging.getLogger("dummy")) -> None:
    """Create FB MQTT connector services"""
    di[Logger] = Logger(logger=logger)
    di["fb-mqtt-connector-plugin_logger"] = di[Logger]

    di[SubscriptionsRepository] = SubscriptionsRepository()
    di["fb-mqtt-connector-plugin_subscription-repository"] = di[SubscriptionsRepository]

    # Entities consumers
    di[Consumer] = Consumer(
        logger=di[Logger],
    )
    di["fb-mqtt-connector-plugin_consumer-proxy"] = di[Consumer]

    # Clients handlers
    di[CommonHandler] = CommonHandler(
        subscriptions_repository=di[SubscriptionsRepository],
        consumer=di[Consumer],
        logger=di[Logger],
    )
    di["fb-mqtt-connector-plugin_mqtt-handler-common"] = di[CommonHandler]
    di[ApiV1Handler] = ApiV1Handler(
        subscriptions_repository=di[SubscriptionsRepository],
        consumer=di[Consumer],
        logger=di[Logger],
    )
    di["fb-mqtt-connector-plugin_mqtt-handler-api-v1"] = di[ApiV1Handler]

    # Messages handler proxy
    di[Handler] = Handler()  # type: ignore[call-arg]
    di["fb-mqtt-connector-plugin_mqtt-handler-proxy"] = di[Handler]

    di[Client] = Client(logger=di[Logger])
    di["fb-mqtt-connector-plugin_clients-proxy"] = di[Client]

    di[ClientFactory] = ClientFactory(
        client=di[Client],
        handler=di[Handler],
        logger=di[Logger],
    )
    di["fb-mqtt-connector-plugin_paho-client-factory"] = di[ClientFactory]

    # MQTT messages publishers
    di[ApiV1Publisher] = ApiV1Publisher(
        client=di[Client],
        logger=di[Logger],
    )
    di["fb-mqtt-connector-plugin_publisher-api-v1"] = di[ApiV1Publisher]

    di[Publisher] = Publisher()  # type: ignore[call-arg]
    di["fb-mqtt-connector-plugin_publisher-proxy"] = di[Publisher]

    # MQTT connector
    di[FbMqttConnector] = FbMqttConnector(
        client=di[Client],
        client_factory=di[ClientFactory],
        consumer=di[Consumer],
        logger=di[Logger],
    )
    di["fb-mqtt-connector-plugin_connector"] = di[FbMqttConnector]
