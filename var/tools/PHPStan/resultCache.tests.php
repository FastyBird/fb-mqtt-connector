<?php declare(strict_types = 1);

return [
	'lastFullAnalysisTime' => 1665744270,
	'meta' => array (
  'cacheVersion' => 'v10-collectedData',
  'phpstanVersion' => '1.8.9',
  'phpVersion' => 80110,
  'projectConfig' => '{parameters: {featureToggles: {bleedingEdge: true, skipCheckGenericClasses: [], explicitMixedInUnknownGenericNew: true, explicitMixedForGlobalVariables: true, explicitMixedViaIsArray: true, arrayFilter: true, arrayUnpacking: true, nodeConnectingVisitorCompatibility: false, nodeConnectingVisitorRule: true, disableCheckMissingIterableValueType: true, strictUnnecessaryNullsafePropertyFetch: true, looseComparison: true, consistentConstructor: true, checkUnresolvableParameterTypes: true, readOnlyByPhpDoc: true, phpDocParserRequireWhitespaceBeforeDescription: true, runtimeReflectionRules: true, notAnalysedTrait: true, curlSetOptTypes: true}, phpVersion: 80100, tmpDir: /Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/var/tools/PHPStan, exceptions: {check: {missingCheckedExceptionInThrows: true, tooWideThrowType: true}}, checkMissingCallableSignature: true, checkTooWideReturnTypesInProtectedAndPublicMethods: true, checkInternalClassCaseSensitivity: true, level: max, resultCachePath: %currentWorkingDirectory%/var/tools/PHPStan/resultCache.tests.php, bootstrapFiles: [/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tools/phpstan-bootstrap.php], scanDirectories: [/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src]}}',
  'analysedPaths' => 
  array (
    0 => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases',
  ),
  'scannedFiles' => 
  array (
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/API/V1Builder.php' => 'ea205a81fc0368bac38d05f46835720ab8dd82a5',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/API/V1Parser.php' => '7304b330fedc39302362bdd68af71a1d32e6ad26',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/API/V1Validator.php' => '222853e8b8f1fb29321e88cd06f77360bdc8e7f0',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Clients/Client.php' => '857effbd4217f59692c77ce380cd36555cacf541',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Clients/ClientFactory.php' => '3d7abcee96529494506ec0515238e6b4c203b5a5',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Clients/FbMqttV1.php' => 'c19e7ea94307e64e5c3a82e9a30752e8aab9d09c',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Clients/FbMqttV1Factory.php' => '4da7d2db0ccf86d788341b198307e2da85c2ae2a',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Clients/Flow.php' => '3a50d5782deb632ae9368e3e8aa15edd493439ee',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Connector/Connector.php' => '964385ed47ab13a83d1b91a6cfa2f31a299329e7',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Connector/ConnectorFactory.php' => '337b5a7f4ba98c78e26e5730de682d5868480cb9',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Constants.php' => '6c6f921b1eb658d62955b197307d7022a5ae2f15',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Consumers/Consumer.php' => 'ed910a92800bf3bc8de5dd3e6a4201e8a4f22702',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Consumers/Messages.php' => '307cf9686c195c52e7ad31b444b33d9ef450b670',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Consumers/Messages/Channel.php' => 'f006aade32721f64bf8245092be9a37c9f344a9d',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Consumers/Messages/ChannelProperty.php' => '998683691904e378607b5f94f14b7078f28fc890',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Consumers/Messages/Device.php' => 'd40a81cc68b0a7c36f5cf8698eec041d9045b026',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Consumers/Messages/DeviceProperty.php' => '4a2dc2c96813d2cbf88c56cb4e238c9c5bf7e220',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Consumers/Messages/ExtensionAttribute.php' => 'b3a16898ca4e0ad0170e81bc7431fca0582baa92',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Consumers/Messages/TProperty.php' => '055f68929d4dcdba60f0a0580e56faf39e812e4a',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/DI/FbMqttConnectorExtension.php' => '181829bcf9a13adc5079a05eb67536f1bf17e734',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Entities/FbMqttConnector.php' => 'ac3898b9b43982080c0d875d780dd4fdfce55eba',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Entities/FbMqttDevice.php' => '9968a4f57c496367eee4a67d2987549bf0be7d03',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Entities/Messages/Attribute.php' => 'd793a587059ee52ce88027d325dd8d49b625a005',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Entities/Messages/ChannelAttribute.php' => '6c4be4aaf1c9a495f15a2bd5e2c651b49296e1ab',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Entities/Messages/ChannelProperty.php' => '9731d7a44ec1e05fb5a0f3440a13121ed8872c5e',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Entities/Messages/DeviceAttribute.php' => 'f34044da3add2a91b1d6ef8b1d116857160a3379',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Entities/Messages/DeviceProperty.php' => '38db9732f7934c4531d479cf6bc2879d43258f92',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Entities/Messages/Entity.php' => '83e5d02b0cd46bdccc180e6435211a948c58d8bd',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Entities/Messages/ExtensionAttribute.php' => '09cb34334b0c68e5112a7fd66ffba378a504f98a',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Entities/Messages/Property.php' => 'f71bc49bd1549270401d9842cae95b49b0508df1',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Entities/Messages/PropertyAttribute.php' => '1eb13cfd429670addd9ce5ef4313dbfa9cff7e5e',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Exceptions/Exception.php' => '2793a94e95d6f87b0dd2f01e79a86dada82cb8c4',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Exceptions/InvalidArgument.php' => 'a3e6b18ff99bc453a770a0b54dfcdd5d123cdaf0',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Exceptions/InvalidState.php' => '28952528cc2ec93f84b04f0ca685df5d174745d0',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Exceptions/Logic.php' => '5680c117220d7014dcb54273ba37a6991d41cc07',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Exceptions/ParseMessage.php' => '9afc0063af175eb91fa6bbc1d2c8e122638d58a2',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Exceptions/Runtime.php' => '6070db439b9190e360b69654f42143a72bdc4d37',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Helpers/Connector.php' => '3b50ab73ffc5f709a4ba1b6957dfecee6d5a9a02',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Helpers/Database.php' => '31f46c43054dc8fc8ece8ba96001239a02689915',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Helpers/Payload.php' => '08e8e58d948734561c38d4b88a2d29b695cf2592',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Helpers/Property.php' => 'a314e06dc90d9f0d95047ee600228a6491954b22',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Hydrators/FbMqttConnector.php' => '89ff4f7e35b2bfbf779dc8fc9a846c0747396591',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Hydrators/FbMqttDevice.php' => '79b99e322152e4ec34e6f4576c63b9c752d21c92',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Schemas/FbMqttConnector.php' => '471c1582948391305b5b871c61c1ed1ac7e31a83',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Schemas/FbMqttDevice.php' => 'b6c7470e1c2733f88ba1a9629fed8a8e62c51634',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Types/ConnectorPropertyIdentifier.php' => 'a6374924e7dc897a0d7f0e666aff426e12b016a9',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Types/DeviceAttributeIdentifier.php' => '94cf3ef4ca90472fe66fe3e215723dba14f722f1',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Types/DevicePropertyIdentifier.php' => '35556256a56e6ee7e359e58839b4bd1a2ab334de',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Types/ExtensionType.php' => 'acefeee8b5b772b5f104bf1c7b3eb5d3566f568b',
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/src/Types/ProtocolVersion.php' => '438288b6e12a9b80701b91442ba5c1f560719927',
  ),
  'composerLocks' => 
  array (
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/composer.lock' => 'bd3ef2ee74b9d9b9fc2cef756ccf42000378803a',
  ),
  'composerInstalled' => 
  array (
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/installed.php' => 
    array (
      'versions' => 
      array (
        'binsoul/net-mqtt' => 
        array (
          'pretty_version' => '0.8.1',
          'version' => '0.8.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../binsoul/net-mqtt',
          'aliases' => 
          array (
          ),
          'reference' => 'c46c1aff5253ba0a0f750b1120dad0f707221316',
          'dev_requirement' => false,
        ),
        'brianium/paratest' => 
        array (
          'pretty_version' => 'v6.6.4',
          'version' => '6.6.4.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../brianium/paratest',
          'aliases' => 
          array (
          ),
          'reference' => '4ce800dc32fd0292a4f05c00f347142dce1ecdda',
          'dev_requirement' => true,
        ),
        'brick/math' => 
        array (
          'pretty_version' => '0.10.2',
          'version' => '0.10.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../brick/math',
          'aliases' => 
          array (
          ),
          'reference' => '459f2781e1a08d52ee56b0b1444086e038561e3f',
          'dev_requirement' => false,
        ),
        'colinodell/json5' => 
        array (
          'pretty_version' => 'v2.2.2',
          'version' => '2.2.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../colinodell/json5',
          'aliases' => 
          array (
          ),
          'reference' => '2b0fabd1ba71fe8079a832d6097ec5c6fd92361d',
          'dev_requirement' => true,
        ),
        'composer/pcre' => 
        array (
          'pretty_version' => '3.0.0',
          'version' => '3.0.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/./pcre',
          'aliases' => 
          array (
          ),
          'reference' => 'e300eb6c535192decd27a85bc72a9290f0d6b3bd',
          'dev_requirement' => true,
        ),
        'composer/xdebug-handler' => 
        array (
          'pretty_version' => '3.0.3',
          'version' => '3.0.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/./xdebug-handler',
          'aliases' => 
          array (
          ),
          'reference' => 'ced299686f41dce890debac69273b47ffe98a40c',
          'dev_requirement' => true,
        ),
        'consistence-community/consistence' => 
        array (
          'pretty_version' => '2.1.3',
          'version' => '2.1.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../consistence-community/consistence',
          'aliases' => 
          array (
          ),
          'reference' => '66fcbc4710e3518b37f4b4e4133a6e504dc6650a',
          'dev_requirement' => false,
        ),
        'consistence-community/consistence-doctrine' => 
        array (
          'pretty_version' => '2.1.3',
          'version' => '2.1.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../consistence-community/consistence-doctrine',
          'aliases' => 
          array (
          ),
          'reference' => '55a356a004107bcb7a513e08457a6c69371796cc',
          'dev_requirement' => false,
        ),
        'consistence/consistence' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '2.*',
          ),
        ),
        'consistence/consistence-doctrine' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '2.*',
          ),
        ),
        'contributte/di' => 
        array (
          'pretty_version' => 'v0.5.3',
          'version' => '0.5.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../contributte/di',
          'aliases' => 
          array (
          ),
          'reference' => '7fb8abed72ddf6b8bd9819fb709f2c0a024d6ffc',
          'dev_requirement' => false,
        ),
        'contributte/flysystem' => 
        array (
          'pretty_version' => 'v0.3.0',
          'version' => '0.3.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../contributte/flysystem',
          'aliases' => 
          array (
          ),
          'reference' => 'be0473e0310c0292f540eb4a567cbc237b270b2f',
          'dev_requirement' => false,
        ),
        'contributte/translation' => 
        array (
          'pretty_version' => 'v0.9.4',
          'version' => '0.9.4.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../contributte/translation',
          'aliases' => 
          array (
          ),
          'reference' => '0778d049b8fc711def6b5afbb069e8777b72b003',
          'dev_requirement' => true,
        ),
        'cweagans/composer-patches' => 
        array (
          'pretty_version' => '1.7.2',
          'version' => '1.7.2.0',
          'type' => 'composer-plugin',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../cweagans/composer-patches',
          'aliases' => 
          array (
          ),
          'reference' => 'e9969cfc0796e6dea9b4e52f77f18e1065212871',
          'dev_requirement' => false,
        ),
        'dealerdirect/phpcodesniffer-composer-installer' => 
        array (
          'pretty_version' => 'v0.7.2',
          'version' => '0.7.2.0',
          'type' => 'composer-plugin',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../dealerdirect/phpcodesniffer-composer-installer',
          'aliases' => 
          array (
          ),
          'reference' => '1c968e542d8843d7cd71de3c5c9c3ff3ad71a1db',
          'dev_requirement' => true,
        ),
        'dg/bypass-finals' => 
        array (
          'pretty_version' => 'v1.4.1',
          'version' => '1.4.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../dg/bypass-finals',
          'aliases' => 
          array (
          ),
          'reference' => '4c424c3ed359220fce044f35cdf9f48b0089b2ca',
          'dev_requirement' => true,
        ),
        'doctrine/annotations' => 
        array (
          'pretty_version' => '1.13.3',
          'version' => '1.13.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../doctrine/annotations',
          'aliases' => 
          array (
          ),
          'reference' => '648b0343343565c4a056bfc8392201385e8d89f0',
          'dev_requirement' => false,
        ),
        'doctrine/cache' => 
        array (
          'pretty_version' => '1.13.0',
          'version' => '1.13.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../doctrine/cache',
          'aliases' => 
          array (
          ),
          'reference' => '56cd022adb5514472cb144c087393c1821911d09',
          'dev_requirement' => false,
        ),
        'doctrine/collections' => 
        array (
          'pretty_version' => '1.8.0',
          'version' => '1.8.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../doctrine/collections',
          'aliases' => 
          array (
          ),
          'reference' => '2b44dd4cbca8b5744327de78bafef5945c7e7b5e',
          'dev_requirement' => false,
        ),
        'doctrine/common' => 
        array (
          'pretty_version' => '3.4.3',
          'version' => '3.4.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../doctrine/common',
          'aliases' => 
          array (
          ),
          'reference' => '8b5e5650391f851ed58910b3e3d48a71062eeced',
          'dev_requirement' => false,
        ),
        'doctrine/dbal' => 
        array (
          'pretty_version' => '3.4.5',
          'version' => '3.4.5.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../doctrine/dbal',
          'aliases' => 
          array (
          ),
          'reference' => 'a5a58773109c0abb13e658c8ccd92aeec8d07f9e',
          'dev_requirement' => false,
        ),
        'doctrine/deprecations' => 
        array (
          'pretty_version' => 'v1.0.0',
          'version' => '1.0.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../doctrine/deprecations',
          'aliases' => 
          array (
          ),
          'reference' => '0e2a4f1f8cdfc7a92ec3b01c9334898c806b30de',
          'dev_requirement' => false,
        ),
        'doctrine/event-manager' => 
        array (
          'pretty_version' => '1.2.0',
          'version' => '1.2.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../doctrine/event-manager',
          'aliases' => 
          array (
          ),
          'reference' => '95aa4cb529f1e96576f3fda9f5705ada4056a520',
          'dev_requirement' => false,
        ),
        'doctrine/inflector' => 
        array (
          'pretty_version' => '2.0.5',
          'version' => '2.0.5.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../doctrine/inflector',
          'aliases' => 
          array (
          ),
          'reference' => 'ade2b3bbfb776f27f0558e26eed43b5d9fe1b392',
          'dev_requirement' => false,
        ),
        'doctrine/instantiator' => 
        array (
          'pretty_version' => '1.4.1',
          'version' => '1.4.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../doctrine/instantiator',
          'aliases' => 
          array (
          ),
          'reference' => '10dcfce151b967d20fde1b34ae6640712c3891bc',
          'dev_requirement' => false,
        ),
        'doctrine/lexer' => 
        array (
          'pretty_version' => '1.2.3',
          'version' => '1.2.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../doctrine/lexer',
          'aliases' => 
          array (
          ),
          'reference' => 'c268e882d4dbdd85e36e4ad69e02dc284f89d229',
          'dev_requirement' => false,
        ),
        'doctrine/orm' => 
        array (
          'pretty_version' => '2.13.3',
          'version' => '2.13.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../doctrine/orm',
          'aliases' => 
          array (
          ),
          'reference' => 'e750360bd52b080c4cbaaee1b48b80f7dc873b36',
          'dev_requirement' => false,
        ),
        'doctrine/persistence' => 
        array (
          'pretty_version' => '3.0.4',
          'version' => '3.0.4.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../doctrine/persistence',
          'aliases' => 
          array (
          ),
          'reference' => '05612da375f8a3931161f435f91d6704926e6ec5',
          'dev_requirement' => false,
        ),
        'evenement/evenement' => 
        array (
          'pretty_version' => 'v3.0.1',
          'version' => '3.0.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../evenement/evenement',
          'aliases' => 
          array (
          ),
          'reference' => '531bfb9d15f8aa57454f5f0285b18bec903b8fb7',
          'dev_requirement' => false,
        ),
        'fastybird/datetime-factory' => 
        array (
          'pretty_version' => 'v0.6.1',
          'version' => '0.6.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../fastybird/datetime-factory',
          'aliases' => 
          array (
          ),
          'reference' => 'c3dca87ee3e331213453621c6fee8caa8c391b6d',
          'dev_requirement' => false,
        ),
        'fastybird/devices-module' => 
        array (
          'pretty_version' => 'dev-main',
          'version' => 'dev-main',
          'type' => 'module',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../fastybird/devices-module',
          'aliases' => 
          array (
            0 => '9999999-dev',
            1 => '0.75.0',
          ),
          'reference' => '0e668eb65f194693a0e1505c4ebdff9c60f95909',
          'dev_requirement' => false,
        ),
        'fastybird/exchange' => 
        array (
          'pretty_version' => 'v1.0.0',
          'version' => '1.0.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../fastybird/exchange',
          'aliases' => 
          array (
          ),
          'reference' => 'cedb229711918e9c559bfbee4b09bedaa3fc4a8c',
          'dev_requirement' => false,
        ),
        'fastybird/json-api' => 
        array (
          'pretty_version' => 'v0.13.1',
          'version' => '0.13.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../fastybird/json-api',
          'aliases' => 
          array (
          ),
          'reference' => 'f544149d2e5828b320c015bcbca49dcc3feecb6b',
          'dev_requirement' => false,
        ),
        'fastybird/metadata' => 
        array (
          'pretty_version' => 'v1.1.0',
          'version' => '1.1.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../fastybird/metadata',
          'aliases' => 
          array (
          ),
          'reference' => '4ff3c057232fb44a999f5ff38368e18e73e1f3c1',
          'dev_requirement' => false,
        ),
        'fastybird/simple-auth' => 
        array (
          'pretty_version' => 'v0.5.1',
          'version' => '0.5.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../fastybird/simple-auth',
          'aliases' => 
          array (
          ),
          'reference' => 'e63ee4fa0eeeda4d4606302884c3651748aad66f',
          'dev_requirement' => false,
        ),
        'fig/http-message-util' => 
        array (
          'pretty_version' => '1.1.5',
          'version' => '1.1.5.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../fig/http-message-util',
          'aliases' => 
          array (
          ),
          'reference' => '9d94dc0154230ac39e5bf89398b324a86f63f765',
          'dev_requirement' => false,
        ),
        'giggsey/libphonenumber-for-php' => 
        array (
          'pretty_version' => '8.12.57',
          'version' => '8.12.57.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../giggsey/libphonenumber-for-php',
          'aliases' => 
          array (
          ),
          'reference' => '033a7285fd1102c13c4415e300734b7ce7ca0ae0',
          'dev_requirement' => false,
        ),
        'giggsey/locale' => 
        array (
          'pretty_version' => '2.2',
          'version' => '2.2.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../giggsey/locale',
          'aliases' => 
          array (
          ),
          'reference' => '9c1dca769253f6a3e81f9a5c167f53b6a54ab635',
          'dev_requirement' => false,
        ),
        'grogy/php-parallel-lint' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'infection/abstract-testframework-adapter' => 
        array (
          'pretty_version' => '0.5.0',
          'version' => '0.5.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../infection/abstract-testframework-adapter',
          'aliases' => 
          array (
          ),
          'reference' => '18925e20d15d1a5995bb85c9dc09e8751e1e069b',
          'dev_requirement' => true,
        ),
        'infection/extension-installer' => 
        array (
          'pretty_version' => '0.1.2',
          'version' => '0.1.2.0',
          'type' => 'composer-plugin',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../infection/extension-installer',
          'aliases' => 
          array (
          ),
          'reference' => '9b351d2910b9a23ab4815542e93d541e0ca0cdcf',
          'dev_requirement' => true,
        ),
        'infection/include-interceptor' => 
        array (
          'pretty_version' => '0.2.5',
          'version' => '0.2.5.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../infection/include-interceptor',
          'aliases' => 
          array (
          ),
          'reference' => '0cc76d95a79d9832d74e74492b0a30139904bdf7',
          'dev_requirement' => true,
        ),
        'infection/infection' => 
        array (
          'pretty_version' => '0.26.15',
          'version' => '0.26.15.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../infection/infection',
          'aliases' => 
          array (
          ),
          'reference' => 'cac814fab9ec3ee60bfe55f070942306c61c8eb9',
          'dev_requirement' => true,
        ),
        'ipub/doctrine-consistence' => 
        array (
          'pretty_version' => 'v0.4.2',
          'version' => '0.4.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../ipub/doctrine-consistence',
          'aliases' => 
          array (
          ),
          'reference' => '358008d95df418015511f57c656d9d3fbb0528e1',
          'dev_requirement' => false,
        ),
        'ipub/doctrine-crud' => 
        array (
          'pretty_version' => 'v3.0.0',
          'version' => '3.0.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../ipub/doctrine-crud',
          'aliases' => 
          array (
          ),
          'reference' => '3877e8749e70798961354df0894fb742c6ea9d58',
          'dev_requirement' => false,
        ),
        'ipub/doctrine-dynamic-discriminator-map' => 
        array (
          'pretty_version' => 'v1.4.0',
          'version' => '1.4.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../ipub/doctrine-dynamic-discriminator-map',
          'aliases' => 
          array (
          ),
          'reference' => 'deb0d9884721822d3bddc6039cbfb6cafc558d6e',
          'dev_requirement' => false,
        ),
        'ipub/doctrine-orm-query' => 
        array (
          'pretty_version' => 'v0.1.0',
          'version' => '0.1.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../ipub/doctrine-orm-query',
          'aliases' => 
          array (
          ),
          'reference' => 'c4618b4f50396264038708634aa2023f550d837a',
          'dev_requirement' => false,
        ),
        'ipub/doctrine-timestampable' => 
        array (
          'pretty_version' => 'v1.5.2',
          'version' => '1.5.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../ipub/doctrine-timestampable',
          'aliases' => 
          array (
          ),
          'reference' => '47cffd17e65f194e07808e1a83a0c11d0d7525a9',
          'dev_requirement' => false,
        ),
        'ipub/json-api-document' => 
        array (
          'pretty_version' => 'v0.2.2',
          'version' => '0.2.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../ipub/json-api-document',
          'aliases' => 
          array (
          ),
          'reference' => 'ed1741092a222e4adfd062f5aa09a1dde3bd6912',
          'dev_requirement' => false,
        ),
        'ipub/phone' => 
        array (
          'pretty_version' => 'v2.3.0',
          'version' => '2.3.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../ipub/phone',
          'aliases' => 
          array (
          ),
          'reference' => 'a13a98c026b936f6877ac2576f907f6fdd9dc855',
          'dev_requirement' => false,
        ),
        'ipub/slim-router' => 
        array (
          'pretty_version' => 'v0.2.0',
          'version' => '0.2.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../ipub/slim-router',
          'aliases' => 
          array (
          ),
          'reference' => 'e6c12bf43b3c66ec0a4eb48715bbf14f09857d27',
          'dev_requirement' => false,
        ),
        'jakub-onderka/php-parallel-lint' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'jean85/pretty-package-versions' => 
        array (
          'pretty_version' => '2.0.5',
          'version' => '2.0.5.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../jean85/pretty-package-versions',
          'aliases' => 
          array (
          ),
          'reference' => 'ae547e455a3d8babd07b96966b17d7fd21d9c6af',
          'dev_requirement' => true,
        ),
        'justinrainbow/json-schema' => 
        array (
          'pretty_version' => '5.2.12',
          'version' => '5.2.12.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../justinrainbow/json-schema',
          'aliases' => 
          array (
          ),
          'reference' => 'ad87d5a5ca981228e0e205c2bc7dfb8e24559b60',
          'dev_requirement' => true,
        ),
        'latte/latte' => 
        array (
          'pretty_version' => 'v2.11.5',
          'version' => '2.11.5.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../latte/latte',
          'aliases' => 
          array (
          ),
          'reference' => '89e647e51213af8a270fe9903b8735a2f6c83ad1',
          'dev_requirement' => true,
        ),
        'lcobucci/clock' => 
        array (
          'pretty_version' => '2.2.0',
          'version' => '2.2.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../lcobucci/clock',
          'aliases' => 
          array (
          ),
          'reference' => 'fb533e093fd61321bfcbac08b131ce805fe183d3',
          'dev_requirement' => false,
        ),
        'lcobucci/jwt' => 
        array (
          'pretty_version' => '4.2.1',
          'version' => '4.2.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../lcobucci/jwt',
          'aliases' => 
          array (
          ),
          'reference' => '72ac6d807ee51a70ad376ee03a2387e8646e10f3',
          'dev_requirement' => false,
        ),
        'league/flysystem' => 
        array (
          'pretty_version' => '3.6.0',
          'version' => '3.6.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../league/flysystem',
          'aliases' => 
          array (
          ),
          'reference' => '8eded334b9894dc90ebdcb7be81e3a1c9413f709',
          'dev_requirement' => false,
        ),
        'league/mime-type-detection' => 
        array (
          'pretty_version' => '1.11.0',
          'version' => '1.11.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../league/mime-type-detection',
          'aliases' => 
          array (
          ),
          'reference' => 'ff6248ea87a9f116e78edd6002e39e5128a0d4dd',
          'dev_requirement' => false,
        ),
        'myclabs/deep-copy' => 
        array (
          'pretty_version' => '1.11.0',
          'version' => '1.11.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../myclabs/deep-copy',
          'aliases' => 
          array (
          ),
          'reference' => '14daed4296fae74d9e3201d2c4925d1acb7aa614',
          'dev_requirement' => true,
        ),
        'neomerx/json-api' => 
        array (
          'pretty_version' => 'v4.0.1',
          'version' => '4.0.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../neomerx/json-api',
          'aliases' => 
          array (
          ),
          'reference' => '0e45254a4574a3118e0ed663312b43aca23b89c7',
          'dev_requirement' => false,
        ),
        'nette/bootstrap' => 
        array (
          'pretty_version' => 'v3.1.2',
          'version' => '3.1.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nette/bootstrap',
          'aliases' => 
          array (
          ),
          'reference' => '3ab4912a08af0c16d541c3709935c3478b5ee090',
          'dev_requirement' => false,
        ),
        'nette/di' => 
        array (
          'pretty_version' => 'v3.0.13',
          'version' => '3.0.13.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nette/di',
          'aliases' => 
          array (
          ),
          'reference' => '9878f2958a0a804b08430dbc719a52e493022739',
          'dev_requirement' => false,
        ),
        'nette/finder' => 
        array (
          'pretty_version' => 'v2.5.4',
          'version' => '2.5.4.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nette/finder',
          'aliases' => 
          array (
          ),
          'reference' => '4a1236db9067d86a75c3dcc0d9c2aced17f9bde8',
          'dev_requirement' => false,
        ),
        'nette/http' => 
        array (
          'pretty_version' => 'v3.1.6',
          'version' => '3.1.6.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nette/http',
          'aliases' => 
          array (
          ),
          'reference' => '65bfe68f9c611e7cd1935a5f794a560c52e4614f',
          'dev_requirement' => true,
        ),
        'nette/neon' => 
        array (
          'pretty_version' => 'v3.3.3',
          'version' => '3.3.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nette/neon',
          'aliases' => 
          array (
          ),
          'reference' => '22e384da162fab42961d48eb06c06d3ad0c11b95',
          'dev_requirement' => false,
        ),
        'nette/php-generator' => 
        array (
          'pretty_version' => 'v4.0.4',
          'version' => '4.0.4.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nette/php-generator',
          'aliases' => 
          array (
          ),
          'reference' => '80f158a2d2fa44c1785b16a3dcdabef3120b3e71',
          'dev_requirement' => false,
        ),
        'nette/robot-loader' => 
        array (
          'pretty_version' => 'v3.4.1',
          'version' => '3.4.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nette/robot-loader',
          'aliases' => 
          array (
          ),
          'reference' => 'e2adc334cb958164c050f485d99c44c430f51fe2',
          'dev_requirement' => false,
        ),
        'nette/routing' => 
        array (
          'pretty_version' => 'v3.0.3',
          'version' => '3.0.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nette/routing',
          'aliases' => 
          array (
          ),
          'reference' => '5e02bdde257029db0223d3291c281d913abd587f',
          'dev_requirement' => true,
        ),
        'nette/schema' => 
        array (
          'pretty_version' => 'v1.2.2',
          'version' => '1.2.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nette/schema',
          'aliases' => 
          array (
          ),
          'reference' => '9a39cef03a5b34c7de64f551538cbba05c2be5df',
          'dev_requirement' => false,
        ),
        'nette/utils' => 
        array (
          'pretty_version' => 'v3.2.8',
          'version' => '3.2.8.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nette/utils',
          'aliases' => 
          array (
          ),
          'reference' => '02a54c4c872b99e4ec05c4aec54b5a06eb0f6368',
          'dev_requirement' => false,
        ),
        'nettrine/annotations' => 
        array (
          'pretty_version' => 'v0.7.0',
          'version' => '0.7.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nettrine/annotations',
          'aliases' => 
          array (
          ),
          'reference' => 'fbb06d156a4edcbf37e4154e5b4ede079136388b',
          'dev_requirement' => false,
        ),
        'nettrine/cache' => 
        array (
          'pretty_version' => 'v0.3.0',
          'version' => '0.3.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nettrine/cache',
          'aliases' => 
          array (
          ),
          'reference' => '8a58596de24cdd61e45866ef8f35788675f6d2bc',
          'dev_requirement' => false,
        ),
        'nettrine/dbal' => 
        array (
          'pretty_version' => 'v0.8.0',
          'version' => '0.8.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nettrine/dbal',
          'aliases' => 
          array (
          ),
          'reference' => 'e092aac6561073e802cab948fb913e2043894155',
          'dev_requirement' => false,
        ),
        'nettrine/orm' => 
        array (
          'pretty_version' => 'v0.8.3',
          'version' => '0.8.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nettrine/orm',
          'aliases' => 
          array (
          ),
          'reference' => '04bf4aca3897c12fcba3db3bc7aeb58e557de638',
          'dev_requirement' => false,
        ),
        'nikic/fast-route' => 
        array (
          'pretty_version' => 'v1.3.0',
          'version' => '1.3.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nikic/fast-route',
          'aliases' => 
          array (
          ),
          'reference' => '181d480e08d9476e61381e04a71b34dc0432e812',
          'dev_requirement' => false,
        ),
        'nikic/php-parser' => 
        array (
          'pretty_version' => 'v4.15.1',
          'version' => '4.15.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../nikic/php-parser',
          'aliases' => 
          array (
          ),
          'reference' => '0ef6c55a3f47f89d7a374e6f835197a0b5fcf900',
          'dev_requirement' => false,
        ),
        'ondram/ci-detector' => 
        array (
          'pretty_version' => '4.1.0',
          'version' => '4.1.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../ondram/ci-detector',
          'aliases' => 
          array (
          ),
          'reference' => '8a4b664e916df82ff26a44709942dfd593fa6f30',
          'dev_requirement' => true,
        ),
        'opis/json-schema' => 
        array (
          'pretty_version' => '2.3.0',
          'version' => '2.3.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../opis/json-schema',
          'aliases' => 
          array (
          ),
          'reference' => 'c48df6d7089a45f01e1c82432348f2d5976f9bfb',
          'dev_requirement' => false,
        ),
        'opis/string' => 
        array (
          'pretty_version' => '2.0.1',
          'version' => '2.0.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../opis/string',
          'aliases' => 
          array (
          ),
          'reference' => '9ebf1a1f873f502f6859d11210b25a4bf5d141e7',
          'dev_requirement' => false,
        ),
        'opis/uri' => 
        array (
          'pretty_version' => '1.1.0',
          'version' => '1.1.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../opis/uri',
          'aliases' => 
          array (
          ),
          'reference' => '0f3ca49ab1a5e4a6681c286e0b2cc081b93a7d5a',
          'dev_requirement' => false,
        ),
        'orisai/coding-standard' => 
        array (
          'pretty_version' => '3.2.1',
          'version' => '3.2.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../orisai/coding-standard',
          'aliases' => 
          array (
          ),
          'reference' => 'a74f316ed12121678ed32a658768b17f875c6023',
          'dev_requirement' => true,
        ),
        'pds/skeleton' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'type' => 'standard',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../pds/skeleton',
          'aliases' => 
          array (
          ),
          'reference' => '95e476e5d629eadacbd721c5a9553e537514a231',
          'dev_requirement' => true,
        ),
        'phar-io/manifest' => 
        array (
          'pretty_version' => '2.0.3',
          'version' => '2.0.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phar-io/manifest',
          'aliases' => 
          array (
          ),
          'reference' => '97803eca37d319dfa7826cc2437fc020857acb53',
          'dev_requirement' => true,
        ),
        'phar-io/version' => 
        array (
          'pretty_version' => '3.2.1',
          'version' => '3.2.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phar-io/version',
          'aliases' => 
          array (
          ),
          'reference' => '4f7fd7836c6f332bb2933569e566a0d6c4cbed74',
          'dev_requirement' => true,
        ),
        'php-parallel-lint/php-parallel-lint' => 
        array (
          'pretty_version' => 'v1.3.2',
          'version' => '1.3.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../php-parallel-lint/php-parallel-lint',
          'aliases' => 
          array (
          ),
          'reference' => '6483c9832e71973ed29cf71bd6b3f4fde438a9de',
          'dev_requirement' => true,
        ),
        'phpdocumentor/reflection' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpdocumentor/reflection',
          'aliases' => 
          array (
          ),
          'reference' => '447928a45710d6313e68774cf12b5f730b909baa',
          'dev_requirement' => false,
        ),
        'phpdocumentor/reflection-common' => 
        array (
          'pretty_version' => '2.2.0',
          'version' => '2.2.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpdocumentor/reflection-common',
          'aliases' => 
          array (
          ),
          'reference' => '1d01c49d4ed62f25aa84a747ad35d5a16924662b',
          'dev_requirement' => false,
        ),
        'phpdocumentor/reflection-docblock' => 
        array (
          'pretty_version' => '5.3.0',
          'version' => '5.3.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpdocumentor/reflection-docblock',
          'aliases' => 
          array (
          ),
          'reference' => '622548b623e81ca6d78b721c5e029f4ce664f170',
          'dev_requirement' => false,
        ),
        'phpdocumentor/type-resolver' => 
        array (
          'pretty_version' => '1.6.1',
          'version' => '1.6.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpdocumentor/type-resolver',
          'aliases' => 
          array (
          ),
          'reference' => '77a32518733312af16a44300404e945338981de3',
          'dev_requirement' => false,
        ),
        'phpstan/extension-installer' => 
        array (
          'pretty_version' => '1.1.0',
          'version' => '1.1.0.0',
          'type' => 'composer-plugin',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpstan/extension-installer',
          'aliases' => 
          array (
          ),
          'reference' => '66c7adc9dfa38b6b5838a9fb728b68a7d8348051',
          'dev_requirement' => true,
        ),
        'phpstan/phpdoc-parser' => 
        array (
          'pretty_version' => '1.8.0',
          'version' => '1.8.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpstan/phpdoc-parser',
          'aliases' => 
          array (
          ),
          'reference' => '8dd908dd6156e974b9a0f8bb4cd5ad0707830f04',
          'dev_requirement' => true,
        ),
        'phpstan/phpstan' => 
        array (
          'pretty_version' => '1.8.9',
          'version' => '1.8.9.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpstan/phpstan',
          'aliases' => 
          array (
          ),
          'reference' => '3a72d9d9f2528fbd50c2d8fcf155fd9f74ade3f2',
          'dev_requirement' => true,
        ),
        'phpstan/phpstan-deprecation-rules' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'type' => 'phpstan-extension',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpstan/phpstan-deprecation-rules',
          'aliases' => 
          array (
          ),
          'reference' => 'e5ccafb0dd8d835dd65d8d7a1a0d2b1b75414682',
          'dev_requirement' => true,
        ),
        'phpstan/phpstan-doctrine' => 
        array (
          'pretty_version' => '1.3.16',
          'version' => '1.3.16.0',
          'type' => 'phpstan-extension',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpstan/phpstan-doctrine',
          'aliases' => 
          array (
          ),
          'reference' => '2d1ddae7ef9a6263ba708538ef7d0fbe362e6e25',
          'dev_requirement' => true,
        ),
        'phpstan/phpstan-nette' => 
        array (
          'pretty_version' => '1.1.0',
          'version' => '1.1.0.0',
          'type' => 'phpstan-extension',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpstan/phpstan-nette',
          'aliases' => 
          array (
          ),
          'reference' => '8dddb884521d282b85af7d4a8221e21827df426a',
          'dev_requirement' => true,
        ),
        'phpstan/phpstan-phpunit' => 
        array (
          'pretty_version' => '1.1.1',
          'version' => '1.1.1.0',
          'type' => 'phpstan-extension',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpstan/phpstan-phpunit',
          'aliases' => 
          array (
          ),
          'reference' => '4a3c437c09075736285d1cabb5c75bf27ed0bc84',
          'dev_requirement' => true,
        ),
        'phpstan/phpstan-strict-rules' => 
        array (
          'pretty_version' => '1.4.4',
          'version' => '1.4.4.0',
          'type' => 'phpstan-extension',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpstan/phpstan-strict-rules',
          'aliases' => 
          array (
          ),
          'reference' => '23e5f377ee6395a1a04842d3d6ed4bd25e7b44a6',
          'dev_requirement' => true,
        ),
        'phpunit/php-code-coverage' => 
        array (
          'pretty_version' => '9.2.17',
          'version' => '9.2.17.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpunit/php-code-coverage',
          'aliases' => 
          array (
          ),
          'reference' => 'aa94dc41e8661fe90c7316849907cba3007b10d8',
          'dev_requirement' => true,
        ),
        'phpunit/php-file-iterator' => 
        array (
          'pretty_version' => '3.0.6',
          'version' => '3.0.6.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpunit/php-file-iterator',
          'aliases' => 
          array (
          ),
          'reference' => 'cf1c2e7c203ac650e352f4cc675a7021e7d1b3cf',
          'dev_requirement' => true,
        ),
        'phpunit/php-invoker' => 
        array (
          'pretty_version' => '3.1.1',
          'version' => '3.1.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpunit/php-invoker',
          'aliases' => 
          array (
          ),
          'reference' => '5a10147d0aaf65b58940a0b72f71c9ac0423cc67',
          'dev_requirement' => true,
        ),
        'phpunit/php-text-template' => 
        array (
          'pretty_version' => '2.0.4',
          'version' => '2.0.4.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpunit/php-text-template',
          'aliases' => 
          array (
          ),
          'reference' => '5da5f67fc95621df9ff4c4e5a84d6a8a2acf7c28',
          'dev_requirement' => true,
        ),
        'phpunit/php-timer' => 
        array (
          'pretty_version' => '5.0.3',
          'version' => '5.0.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpunit/php-timer',
          'aliases' => 
          array (
          ),
          'reference' => '5a63ce20ed1b5bf577850e2c4e87f4aa902afbd2',
          'dev_requirement' => true,
        ),
        'phpunit/phpunit' => 
        array (
          'pretty_version' => '9.5.25',
          'version' => '9.5.25.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../phpunit/phpunit',
          'aliases' => 
          array (
          ),
          'reference' => '3e6f90ca7e3d02025b1d147bd8d4a89fd4ca8a1d',
          'dev_requirement' => true,
        ),
        'psr/cache' => 
        array (
          'pretty_version' => '3.0.0',
          'version' => '3.0.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../psr/cache',
          'aliases' => 
          array (
          ),
          'reference' => 'aa5030cfa5405eccfdcb1083ce040c2cb8d253bf',
          'dev_requirement' => false,
        ),
        'psr/container' => 
        array (
          'pretty_version' => '2.0.2',
          'version' => '2.0.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../psr/container',
          'aliases' => 
          array (
          ),
          'reference' => 'c71ecc56dfe541dbd90c5360474fbc405f8d5963',
          'dev_requirement' => false,
        ),
        'psr/event-dispatcher' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../psr/event-dispatcher',
          'aliases' => 
          array (
          ),
          'reference' => 'dbefd12671e8a14ec7f180cab83036ed26714bb0',
          'dev_requirement' => false,
        ),
        'psr/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-factory' => 
        array (
          'pretty_version' => '1.0.1',
          'version' => '1.0.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../psr/http-factory',
          'aliases' => 
          array (
          ),
          'reference' => '12ac7fcd07e5b077433f5f2bee95b3a771bf61be',
          'dev_requirement' => false,
        ),
        'psr/http-message' => 
        array (
          'pretty_version' => '1.0.1',
          'version' => '1.0.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../psr/http-message',
          'aliases' => 
          array (
          ),
          'reference' => 'f6561bf28d520154e4b0ec72be95418abe6d9363',
          'dev_requirement' => false,
        ),
        'psr/http-server-handler' => 
        array (
          'pretty_version' => '1.0.1',
          'version' => '1.0.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../psr/http-server-handler',
          'aliases' => 
          array (
          ),
          'reference' => 'aff2f80e33b7f026ec96bb42f63242dc50ffcae7',
          'dev_requirement' => false,
        ),
        'psr/http-server-middleware' => 
        array (
          'pretty_version' => '1.0.1',
          'version' => '1.0.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../psr/http-server-middleware',
          'aliases' => 
          array (
          ),
          'reference' => '2296f45510945530b9dceb8bcedb5cb84d40c5f5',
          'dev_requirement' => false,
        ),
        'psr/log' => 
        array (
          'pretty_version' => '1.1.4',
          'version' => '1.1.4.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../psr/log',
          'aliases' => 
          array (
          ),
          'reference' => 'd49695b909c3b7628b6289db5479a1c204601f11',
          'dev_requirement' => false,
        ),
        'psr/log-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0|2.0',
          ),
        ),
        'ramsey/collection' => 
        array (
          'pretty_version' => '1.2.2',
          'version' => '1.2.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../ramsey/collection',
          'aliases' => 
          array (
          ),
          'reference' => 'cccc74ee5e328031b15640b51056ee8d3bb66c0a',
          'dev_requirement' => false,
        ),
        'ramsey/uuid' => 
        array (
          'pretty_version' => '4.5.1',
          'version' => '4.5.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../ramsey/uuid',
          'aliases' => 
          array (
          ),
          'reference' => 'a161a26d917604dc6d3aa25100fddf2556e9f35d',
          'dev_requirement' => false,
        ),
        'ramsey/uuid-doctrine' => 
        array (
          'pretty_version' => '1.8.1',
          'version' => '1.8.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../ramsey/uuid-doctrine',
          'aliases' => 
          array (
          ),
          'reference' => '1a6f235ba3faf1cd9ba18daf5b54d8dc9d3bc7d0',
          'dev_requirement' => false,
        ),
        'react/async' => 
        array (
          'pretty_version' => 'v4.0.0',
          'version' => '4.0.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../react/async',
          'aliases' => 
          array (
          ),
          'reference' => '2aa8d89057e1059f59666e4204100636249b7be0',
          'dev_requirement' => false,
        ),
        'react/cache' => 
        array (
          'pretty_version' => 'v1.1.1',
          'version' => '1.1.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../react/cache',
          'aliases' => 
          array (
          ),
          'reference' => '4bf736a2cccec7298bdf745db77585966fc2ca7e',
          'dev_requirement' => false,
        ),
        'react/dns' => 
        array (
          'pretty_version' => 'v1.10.0',
          'version' => '1.10.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../react/dns',
          'aliases' => 
          array (
          ),
          'reference' => 'a5427e7dfa47713e438016905605819d101f238c',
          'dev_requirement' => false,
        ),
        'react/event-loop' => 
        array (
          'pretty_version' => 'v1.3.0',
          'version' => '1.3.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../react/event-loop',
          'aliases' => 
          array (
          ),
          'reference' => '187fb56f46d424afb6ec4ad089269c72eec2e137',
          'dev_requirement' => false,
        ),
        'react/promise' => 
        array (
          'pretty_version' => 'v2.9.0',
          'version' => '2.9.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../react/promise',
          'aliases' => 
          array (
          ),
          'reference' => '234f8fd1023c9158e2314fa9d7d0e6a83db42910',
          'dev_requirement' => false,
        ),
        'react/promise-timer' => 
        array (
          'pretty_version' => 'v1.9.0',
          'version' => '1.9.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../react/promise-timer',
          'aliases' => 
          array (
          ),
          'reference' => 'aa7a73c74b8d8c0f622f5982ff7b0351bc29e495',
          'dev_requirement' => false,
        ),
        'react/socket' => 
        array (
          'pretty_version' => 'v1.12.0',
          'version' => '1.12.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../react/socket',
          'aliases' => 
          array (
          ),
          'reference' => '81e1b4d7f5450ebd8d2e9a95bb008bb15ca95a7b',
          'dev_requirement' => false,
        ),
        'react/stream' => 
        array (
          'pretty_version' => 'v1.2.0',
          'version' => '1.2.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../react/stream',
          'aliases' => 
          array (
          ),
          'reference' => '7a423506ee1903e89f1e08ec5f0ed430ff784ae9',
          'dev_requirement' => false,
        ),
        'rhumsaa/uuid' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '4.5.1',
          ),
        ),
        'sanmai/later' => 
        array (
          'pretty_version' => '0.1.2',
          'version' => '0.1.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sanmai/later',
          'aliases' => 
          array (
          ),
          'reference' => '9b659fecef2030193fd02402955bc39629d5606f',
          'dev_requirement' => true,
        ),
        'sanmai/pipeline' => 
        array (
          'pretty_version' => 'v6.1',
          'version' => '6.1.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sanmai/pipeline',
          'aliases' => 
          array (
          ),
          'reference' => '3a88f2617237e18d5cd2aa38ca3d4b22770306c2',
          'dev_requirement' => true,
        ),
        'sebastian/cli-parser' => 
        array (
          'pretty_version' => '1.0.1',
          'version' => '1.0.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/cli-parser',
          'aliases' => 
          array (
          ),
          'reference' => '442e7c7e687e42adc03470c7b668bc4b2402c0b2',
          'dev_requirement' => true,
        ),
        'sebastian/code-unit' => 
        array (
          'pretty_version' => '1.0.8',
          'version' => '1.0.8.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/code-unit',
          'aliases' => 
          array (
          ),
          'reference' => '1fc9f64c0927627ef78ba436c9b17d967e68e120',
          'dev_requirement' => true,
        ),
        'sebastian/code-unit-reverse-lookup' => 
        array (
          'pretty_version' => '2.0.3',
          'version' => '2.0.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/code-unit-reverse-lookup',
          'aliases' => 
          array (
          ),
          'reference' => 'ac91f01ccec49fb77bdc6fd1e548bc70f7faa3e5',
          'dev_requirement' => true,
        ),
        'sebastian/comparator' => 
        array (
          'pretty_version' => '4.0.8',
          'version' => '4.0.8.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/comparator',
          'aliases' => 
          array (
          ),
          'reference' => 'fa0f136dd2334583309d32b62544682ee972b51a',
          'dev_requirement' => true,
        ),
        'sebastian/complexity' => 
        array (
          'pretty_version' => '2.0.2',
          'version' => '2.0.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/complexity',
          'aliases' => 
          array (
          ),
          'reference' => '739b35e53379900cc9ac327b2147867b8b6efd88',
          'dev_requirement' => true,
        ),
        'sebastian/diff' => 
        array (
          'pretty_version' => '4.0.4',
          'version' => '4.0.4.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/diff',
          'aliases' => 
          array (
          ),
          'reference' => '3461e3fccc7cfdfc2720be910d3bd73c69be590d',
          'dev_requirement' => true,
        ),
        'sebastian/environment' => 
        array (
          'pretty_version' => '5.1.4',
          'version' => '5.1.4.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/environment',
          'aliases' => 
          array (
          ),
          'reference' => '1b5dff7bb151a4db11d49d90e5408e4e938270f7',
          'dev_requirement' => true,
        ),
        'sebastian/exporter' => 
        array (
          'pretty_version' => '4.0.5',
          'version' => '4.0.5.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/exporter',
          'aliases' => 
          array (
          ),
          'reference' => 'ac230ed27f0f98f597c8a2b6eb7ac563af5e5b9d',
          'dev_requirement' => true,
        ),
        'sebastian/global-state' => 
        array (
          'pretty_version' => '5.0.5',
          'version' => '5.0.5.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/global-state',
          'aliases' => 
          array (
          ),
          'reference' => '0ca8db5a5fc9c8646244e629625ac486fa286bf2',
          'dev_requirement' => true,
        ),
        'sebastian/lines-of-code' => 
        array (
          'pretty_version' => '1.0.3',
          'version' => '1.0.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/lines-of-code',
          'aliases' => 
          array (
          ),
          'reference' => 'c1c2e997aa3146983ed888ad08b15470a2e22ecc',
          'dev_requirement' => true,
        ),
        'sebastian/object-enumerator' => 
        array (
          'pretty_version' => '4.0.4',
          'version' => '4.0.4.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/object-enumerator',
          'aliases' => 
          array (
          ),
          'reference' => '5c9eeac41b290a3712d88851518825ad78f45c71',
          'dev_requirement' => true,
        ),
        'sebastian/object-reflector' => 
        array (
          'pretty_version' => '2.0.4',
          'version' => '2.0.4.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/object-reflector',
          'aliases' => 
          array (
          ),
          'reference' => 'b4f479ebdbf63ac605d183ece17d8d7fe49c15c7',
          'dev_requirement' => true,
        ),
        'sebastian/recursion-context' => 
        array (
          'pretty_version' => '4.0.4',
          'version' => '4.0.4.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/recursion-context',
          'aliases' => 
          array (
          ),
          'reference' => 'cd9d8cf3c5804de4341c283ed787f099f5506172',
          'dev_requirement' => true,
        ),
        'sebastian/resource-operations' => 
        array (
          'pretty_version' => '3.0.3',
          'version' => '3.0.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/resource-operations',
          'aliases' => 
          array (
          ),
          'reference' => '0f4443cb3a1d92ce809899753bc0d5d5a8dd19a8',
          'dev_requirement' => true,
        ),
        'sebastian/type' => 
        array (
          'pretty_version' => '3.2.0',
          'version' => '3.2.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/type',
          'aliases' => 
          array (
          ),
          'reference' => 'fb3fe09c5f0bae6bc27ef3ce933a1e0ed9464b6e',
          'dev_requirement' => true,
        ),
        'sebastian/version' => 
        array (
          'pretty_version' => '3.0.2',
          'version' => '3.0.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../sebastian/version',
          'aliases' => 
          array (
          ),
          'reference' => 'c6c1022351a901512170118436c764e473f6de8c',
          'dev_requirement' => true,
        ),
        'slevomat/coding-standard' => 
        array (
          'pretty_version' => '8.5.2',
          'version' => '8.5.2.0',
          'type' => 'phpcodesniffer-standard',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../slevomat/coding-standard',
          'aliases' => 
          array (
          ),
          'reference' => 'f32937dc41b587f3500efed1dbca2f82aa519373',
          'dev_requirement' => true,
        ),
        'squizlabs/php_codesniffer' => 
        array (
          'pretty_version' => '3.7.1',
          'version' => '3.7.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../squizlabs/php_codesniffer',
          'aliases' => 
          array (
          ),
          'reference' => '1359e176e9307e906dc3d890bcc9603ff6d90619',
          'dev_requirement' => true,
        ),
        'staabm/annotate-pull-request-from-checkstyle' => 
        array (
          'pretty_version' => '1.8.3',
          'version' => '1.8.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../staabm/annotate-pull-request-from-checkstyle',
          'aliases' => 
          array (
          ),
          'reference' => '4d2b7cd5cd5fb8f172e16ba81d4e80a97aec383d',
          'dev_requirement' => true,
        ),
        'stella-maris/clock' => 
        array (
          'pretty_version' => '0.1.6',
          'version' => '0.1.6.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../stella-maris/clock',
          'aliases' => 
          array (
          ),
          'reference' => 'a94228dac03c9a8411198ce8c8dacbbe99c930c3',
          'dev_requirement' => false,
        ),
        'symfony/config' => 
        array (
          'pretty_version' => 'v5.4.11',
          'version' => '5.4.11.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/config',
          'aliases' => 
          array (
          ),
          'reference' => 'ec79e03125c1d2477e43dde8528535d90cc78379',
          'dev_requirement' => true,
        ),
        'symfony/console' => 
        array (
          'pretty_version' => 'v5.4.14',
          'version' => '5.4.14.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/console',
          'aliases' => 
          array (
          ),
          'reference' => '984ea2c0f45f42dfed01d2f3987b187467c4b16d',
          'dev_requirement' => false,
        ),
        'symfony/deprecation-contracts' => 
        array (
          'pretty_version' => 'v3.1.1',
          'version' => '3.1.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/deprecation-contracts',
          'aliases' => 
          array (
          ),
          'reference' => '07f1b9cc2ffee6aaafcf4b710fbc38ff736bd918',
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher' => 
        array (
          'pretty_version' => 'v5.4.9',
          'version' => '5.4.9.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/event-dispatcher',
          'aliases' => 
          array (
          ),
          'reference' => '8e6ce1cc0279e3ff3c8ff0f43813bc88d21ca1bc',
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-contracts' => 
        array (
          'pretty_version' => 'v3.1.1',
          'version' => '3.1.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/event-dispatcher-contracts',
          'aliases' => 
          array (
          ),
          'reference' => '02ff5eea2f453731cfbc6bc215e456b781480448',
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '2.0',
          ),
        ),
        'symfony/filesystem' => 
        array (
          'pretty_version' => 'v6.1.5',
          'version' => '6.1.5.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/filesystem',
          'aliases' => 
          array (
          ),
          'reference' => '4d216a2beef096edf040a070117c39ca2abce307',
          'dev_requirement' => true,
        ),
        'symfony/finder' => 
        array (
          'pretty_version' => 'v6.1.3',
          'version' => '6.1.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/finder',
          'aliases' => 
          array (
          ),
          'reference' => '39696bff2c2970b3779a5cac7bf9f0b88fc2b709',
          'dev_requirement' => true,
        ),
        'symfony/polyfill-ctype' => 
        array (
          'pretty_version' => 'v1.26.0',
          'version' => '1.26.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/polyfill-ctype',
          'aliases' => 
          array (
          ),
          'reference' => '6fd1b9a79f6e3cf65f9e679b23af304cd9e010d4',
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-grapheme' => 
        array (
          'pretty_version' => 'v1.26.0',
          'version' => '1.26.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/polyfill-intl-grapheme',
          'aliases' => 
          array (
          ),
          'reference' => '433d05519ce6990bf3530fba6957499d327395c2',
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-normalizer' => 
        array (
          'pretty_version' => 'v1.26.0',
          'version' => '1.26.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/polyfill-intl-normalizer',
          'aliases' => 
          array (
          ),
          'reference' => '219aa369ceff116e673852dce47c3a41794c14bd',
          'dev_requirement' => false,
        ),
        'symfony/polyfill-mbstring' => 
        array (
          'pretty_version' => 'v1.26.0',
          'version' => '1.26.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/polyfill-mbstring',
          'aliases' => 
          array (
          ),
          'reference' => '9344f9cb97f3b19424af1a21a3b0e75b0a7d8d7e',
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php72' => 
        array (
          'pretty_version' => 'v1.26.0',
          'version' => '1.26.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/polyfill-php72',
          'aliases' => 
          array (
          ),
          'reference' => 'bf44a9fd41feaac72b074de600314a93e2ae78e2',
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php73' => 
        array (
          'pretty_version' => 'v1.26.0',
          'version' => '1.26.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/polyfill-php73',
          'aliases' => 
          array (
          ),
          'reference' => 'e440d35fa0286f77fb45b79a03fedbeda9307e85',
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php80' => 
        array (
          'pretty_version' => 'v1.26.0',
          'version' => '1.26.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/polyfill-php80',
          'aliases' => 
          array (
          ),
          'reference' => 'cfa0ae98841b9e461207c13ab093d76b0fa7bace',
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php81' => 
        array (
          'pretty_version' => 'v1.26.0',
          'version' => '1.26.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/polyfill-php81',
          'aliases' => 
          array (
          ),
          'reference' => '13f6d1271c663dc5ae9fb843a8f16521db7687a1',
          'dev_requirement' => false,
        ),
        'symfony/process' => 
        array (
          'pretty_version' => 'v6.1.3',
          'version' => '6.1.3.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/process',
          'aliases' => 
          array (
          ),
          'reference' => 'a6506e99cfad7059b1ab5cab395854a0a0c21292',
          'dev_requirement' => true,
        ),
        'symfony/service-contracts' => 
        array (
          'pretty_version' => 'v3.1.1',
          'version' => '3.1.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/service-contracts',
          'aliases' => 
          array (
          ),
          'reference' => '925e713fe8fcacf6bc05e936edd8dd5441a21239',
          'dev_requirement' => false,
        ),
        'symfony/string' => 
        array (
          'pretty_version' => 'v6.1.6',
          'version' => '6.1.6.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/string',
          'aliases' => 
          array (
          ),
          'reference' => '7e7e0ff180d4c5a6636eaad57b65092014b61864',
          'dev_requirement' => false,
        ),
        'symfony/translation' => 
        array (
          'pretty_version' => 'v5.4.14',
          'version' => '5.4.14.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/translation',
          'aliases' => 
          array (
          ),
          'reference' => 'f0ed07675863aa6e3939df8b1bc879450b585cab',
          'dev_requirement' => true,
        ),
        'symfony/translation-contracts' => 
        array (
          'pretty_version' => 'v2.5.2',
          'version' => '2.5.2.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../symfony/translation-contracts',
          'aliases' => 
          array (
          ),
          'reference' => '136b19dd05cdf0709db6537d058bcab6dd6e2dbe',
          'dev_requirement' => true,
        ),
        'symfony/translation-implementation' => 
        array (
          'dev_requirement' => true,
          'provided' => 
          array (
            0 => '2.3',
          ),
        ),
        'thecodingmachine/safe' => 
        array (
          'pretty_version' => 'v2.4.0',
          'version' => '2.4.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../thecodingmachine/safe',
          'aliases' => 
          array (
          ),
          'reference' => 'e788f3d09dcd36f806350aedb77eac348fafadd3',
          'dev_requirement' => true,
        ),
        'theseer/tokenizer' => 
        array (
          'pretty_version' => '1.2.1',
          'version' => '1.2.1.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../theseer/tokenizer',
          'aliases' => 
          array (
          ),
          'reference' => '34a41e998c2183e22995f158c581e7b5e755ab9e',
          'dev_requirement' => true,
        ),
        'tracy/tracy' => 
        array (
          'pretty_version' => 'v2.9.4',
          'version' => '2.9.4.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../tracy/tracy',
          'aliases' => 
          array (
          ),
          'reference' => '0ed605329b095f5f5fe2db2adc3d1ee80c917294',
          'dev_requirement' => true,
        ),
        'webmozart/assert' => 
        array (
          'pretty_version' => '1.11.0',
          'version' => '1.11.0.0',
          'type' => 'library',
          'install_path' => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/composer/../webmozart/assert',
          'aliases' => 
          array (
          ),
          'reference' => '11cb2199493b2f8a3b53e7f19068fc6aac760991',
          'dev_requirement' => false,
        ),
      ),
    ),
  ),
  'executedFilesHashes' => 
  array (
    '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tools/phpstan-bootstrap.php' => '242e5faab10a4c3c10b266660e978ac8a897e584',
    'phar:///Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/phpstan/phpstan/phpstan.phar/stubs/runtime/Attribute.php' => 'eaf9127f074e9c7ebc65043ec4050f9fed60c2bb',
    'phar:///Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/phpstan/phpstan/phpstan.phar/stubs/runtime/ReflectionAttribute.php' => '0b4b78277eb6545955d2ce5e09bff28f1f8052c8',
    'phar:///Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/phpstan/phpstan/phpstan.phar/stubs/runtime/ReflectionIntersectionType.php' => 'a3e6299b87ee5d407dae7651758edfa11a74cb11',
    'phar:///Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/vendor/phpstan/phpstan/phpstan.phar/stubs/runtime/ReflectionUnionType.php' => '1b349aa997a834faeafe05fa21bc31cae22bf2e2',
  ),
  'phpExtensions' => 
  array (
    0 => 'Core',
    1 => 'FFI',
    2 => 'PDO',
    3 => 'PDO_ODBC',
    4 => 'Phar',
    5 => 'Reflection',
    6 => 'SPL',
    7 => 'SimpleXML',
    8 => 'Zend OPcache',
    9 => 'bcmath',
    10 => 'bz2',
    11 => 'calendar',
    12 => 'ctype',
    13 => 'curl',
    14 => 'date',
    15 => 'dba',
    16 => 'dio',
    17 => 'dom',
    18 => 'exif',
    19 => 'fileinfo',
    20 => 'filter',
    21 => 'ftp',
    22 => 'gd',
    23 => 'gettext',
    24 => 'gmp',
    25 => 'hash',
    26 => 'iconv',
    27 => 'intl',
    28 => 'json',
    29 => 'ldap',
    30 => 'libxml',
    31 => 'mbstring',
    32 => 'mysqli',
    33 => 'mysqlnd',
    34 => 'odbc',
    35 => 'openssl',
    36 => 'pcntl',
    37 => 'pcre',
    38 => 'pdo_dblib',
    39 => 'pdo_mysql',
    40 => 'pdo_pgsql',
    41 => 'pdo_sqlite',
    42 => 'pgsql',
    43 => 'posix',
    44 => 'pspell',
    45 => 'readline',
    46 => 'session',
    47 => 'shmop',
    48 => 'soap',
    49 => 'sockets',
    50 => 'sodium',
    51 => 'sqlite3',
    52 => 'standard',
    53 => 'sysvmsg',
    54 => 'sysvsem',
    55 => 'sysvshm',
    56 => 'tidy',
    57 => 'tokenizer',
    58 => 'xml',
    59 => 'xmlreader',
    60 => 'xmlwriter',
    61 => 'xsl',
    62 => 'zip',
    63 => 'zlib',
  ),
  'stubFiles' => 
  array (
  ),
  'level' => 'max',
),
	'projectExtensionFiles' => array (
),
	'errorsCallback' => static function (): array { return array (
); },
	'collectedDataCallback' => static function (): array { return array (
); },
	'dependencies' => array (
  '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/API/ApiV1ParserTest.php' => 
  array (
    'fileHash' => '42f2e9d5ae4c97f867a8dbeef24a3bb4342ac2f4',
    'dependentFiles' => 
    array (
    ),
  ),
  '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/API/ApiV1ValidatorTest.php' => 
  array (
    'fileHash' => 'c0497519fdbbd8b1dfd228dffdbafd99bb8ac19a',
    'dependentFiles' => 
    array (
    ),
  ),
  '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/BaseTestCase.php' => 
  array (
    'fileHash' => 'd0e7684b10c5cdd83e34522771f953570b797487',
    'dependentFiles' => 
    array (
      0 => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/API/ApiV1ParserTest.php',
      1 => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/API/ApiV1ValidatorTest.php',
      2 => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/DI/ServicesTest.php',
    ),
  ),
  '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/Connector/ConnectorFactoryTest.php' => 
  array (
    'fileHash' => '61634d3e00481555ae59f63b2f2b49debfa2701c',
    'dependentFiles' => 
    array (
    ),
  ),
  '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/DI/ServicesTest.php' => 
  array (
    'fileHash' => '2eb09f29edc7e854f59302115b9ef79afa02f84c',
    'dependentFiles' => 
    array (
    ),
  ),
  '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/DbTestCase.php' => 
  array (
    'fileHash' => 'dcf2d66a2ec400f1f364f147803253f8eeb59b2c',
    'dependentFiles' => 
    array (
      0 => '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/Connector/ConnectorFactoryTest.php',
    ),
  ),
),
	'exportedNodesCallback' => static function (): array { return array (
  '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/API/ApiV1ParserTest.php' => 
  array (
    0 => 
    PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Tests\\Cases\\Unit\\API\\ApiV1ParserTest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Tests\\Cases\\Unit\\BaseTestCase',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testParseDeviceAttribute',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @phpstan-param Array<string, bool|float|int|string|Array<string>> $expected
	 *
	 * @throws Exceptions\\InvalidArgument
	 * @throws Exceptions\\ParseMessage
	 * @throws Nette\\DI\\MissingServiceException
	 *
	 * @dataProvider parseDeviceAttributesProvider
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'topic',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payload',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'expected',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'parseDeviceAttributesProvider',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @return Array<string, Array<string|Array<string, bool|float|int|string|Array<string>>>>
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testParseDeviceHardwareInfo',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @phpstan-param Array<string, bool|float|int|string|Array<string>> $expected
	 *
	 * @throws Exceptions\\InvalidArgument
	 * @throws Exceptions\\ParseMessage
	 * @throws Nette\\DI\\MissingServiceException
	 *
	 * @dataProvider parseDeviceHardwareInfoProvider
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'topic',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payload',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'expected',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'parseDeviceHardwareInfoProvider',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @return Array<string, Array<string|Array<string, bool|float|int|string|Array<string>>>>
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testParseDeviceFirmwareInfo',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @phpstan-param Array<string, bool|float|int|string|Array<string>> $expected
	 *
	 * @throws Exceptions\\InvalidArgument
	 * @throws Exceptions\\ParseMessage
	 * @throws Nette\\DI\\MissingServiceException
	 *
	 * @dataProvider parseDeviceFirmwareInfoProvider
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'topic',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payload',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'expected',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'parseDeviceFirmwareInfoProvider',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @return Array<string, Array<string|Array<string, bool|float|int|string|Array<string>>>>
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testParseDeviceProperties',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @phpstan-param Array<string, bool|float|int|string|Array<string>> $expected
	 *
	 * @throws Exceptions\\InvalidArgument
	 * @throws Exceptions\\ParseMessage
	 * @throws Nette\\DI\\MissingServiceException
	 *
	 * @dataProvider parseDevicePropertiesProvider
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'topic',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payload',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'expected',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'parseDevicePropertiesProvider',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @return Array<string, Array<string|Array<string, bool|float|int|string|Array<string>>>>
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testParseDevicePropertiesAttributes',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @phpstan-param Array<string, bool|float|int|string|Array<string>> $expected
	 *
	 * @throws Exceptions\\InvalidArgument
	 * @throws Exceptions\\ParseMessage
	 * @throws Nette\\DI\\MissingServiceException
	 *
	 * @dataProvider parseDevicePropertiesAttributesProvider
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'topic',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payload',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'expected',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        9 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'parseDevicePropertiesAttributesProvider',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @return Array<string, Array<string|Array<string, bool|float|int|string|Array<string>>>>
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        10 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testParseDeviceAttributeNotValid',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @phpstan-param class-string<Throwable> $exception
	 *
	 * @throws Exceptions\\InvalidArgument
	 * @throws Exceptions\\ParseMessage
	 * @throws Nette\\DI\\MissingServiceException
	 *
	 * @dataProvider parseDeviceAttributesInvalidProvider
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'topic',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'exception',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'message',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        11 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'parseDeviceAttributesInvalidProvider',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @return Array<string, Array<string>>
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        12 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testParseDeviceHardwareInfoNotValid',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @phpstan-param class-string<Throwable> $exception
	 *
	 * @throws Exceptions\\InvalidArgument
	 * @throws Exceptions\\ParseMessage
	 * @throws Nette\\DI\\MissingServiceException
	 *
	 * @dataProvider parseDeviceHardwareInfoInvalidProvider
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'topic',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'exception',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'message',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        13 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'parseDeviceHardwareInfoInvalidProvider',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @return Array<string, Array<string>>
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        14 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testParseDeviceFirmwareInfoNotValid',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @phpstan-param class-string<Throwable> $exception
	 *
	 * @throws Exceptions\\InvalidArgument
	 * @throws Exceptions\\ParseMessage
	 * @throws Nette\\DI\\MissingServiceException
	 *
	 * @dataProvider parseDeviceFirmwareInfoInvalidProvider
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'topic',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'exception',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'message',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        15 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'parseDeviceFirmwareInfoInvalidProvider',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @return Array<string, Array<string>>
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        16 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testParseChannelAttributes',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @phpstan-param Array<string, bool|float|int|string|Array<string>> $expected
	 *
	 * @throws Exceptions\\InvalidArgument
	 * @throws Exceptions\\ParseMessage
	 * @throws Nette\\DI\\MissingServiceException
	 *
	 * @dataProvider parseChannelAttributesProvider
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'topic',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payload',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'expected',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        17 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'parseChannelAttributesProvider',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @return Array<string, Array<string|Array<string, bool|float|int|string|Array<string>>>>
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        18 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testParseChannelProperties',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @phpstan-param Array<string, bool|float|int|string|Array<string>> $expected
	 *
	 * @throws Exceptions\\InvalidArgument
	 * @throws Exceptions\\ParseMessage
	 * @throws Nette\\DI\\MissingServiceException
	 *
	 * @dataProvider parseChannelPropertiesProvider
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'topic',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payload',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'expected',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        19 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'parseChannelPropertiesProvider',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @return Array<string, Array<string|Array<string, bool|float|int|string|Array<string>>>>
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        20 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testParseChannelPropertiesAttributes',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @phpstan-param Array<string, bool|float|int|string|Array<string>> $expected
	 *
	 * @throws Exceptions\\InvalidArgument
	 * @throws Exceptions\\ParseMessage
	 * @throws Nette\\DI\\MissingServiceException
	 *
	 * @dataProvider parseChannelPropertiesAttributesProvider
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'topic',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'payload',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'expected',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        21 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'parseChannelPropertiesAttributesProvider',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @return Array<string, Array<string|Array<string, bool|float|int|string|Array<string>>>>
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        22 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testParseChannelAttributeNotValid',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @phpstan-param class-string<Throwable> $exception
	 *
	 * @throws Exceptions\\InvalidArgument
	 * @throws Exceptions\\ParseMessage
	 * @throws Nette\\DI\\MissingServiceException
	 *
	 * @dataProvider parseChannelAttributesInvalidProvider
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'topic',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'exception',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'message',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        23 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'parseChannelAttributesInvalidProvider',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @return Array<string, Array<string>>
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\API',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'entities' => 'FastyBird\\FbMqttConnector\\Entities',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/API/ApiV1ValidatorTest.php' => 
  array (
    0 => 
    PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Tests\\Cases\\Unit\\API\\ApiV1ValidatorTest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => 'Tests\\Cases\\Unit\\BaseTestCase',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testValidateDevices',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testValidateChannels',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/BaseTestCase.php' => 
  array (
    0 => 
    PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Tests\\Cases\\Unit\\BaseTestCase',
       'phpDoc' => NULL,
       'abstract' => true,
       'final' => false,
       'extends' => 'PHPUnit\\Framework\\TestCase',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'container',
          ),
           'phpDoc' => NULL,
           'type' => 'Nette\\DI\\Container',
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'attributes' => 
          array (
          ),
        )),
        1 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'setUp',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'createContainer',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Nette\\DI\\Container',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'additionalConfig',
               'type' => 'string|null|null',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'mockContainerService',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'serviceType',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'serviceMock',
               'type' => 'object',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/Connector/ConnectorFactoryTest.php' => 
  array (
    0 => 
    PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Tests\\Cases\\Unit\\Connector\\ConnectorFactoryTest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Tests\\Cases\\Unit\\DbTestCase',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'setUp',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @throws Exceptions\\InvalidArgument
	 * @throws Nette\\DI\\MissingServiceException
	 * @throws Nette\\Utils\\JsonException
	 * @throws Flysystem\\FilesystemException
	 * @throws RuntimeException
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\Connector',
             'uses' => 
            array (
              'devicesmodule' => 'FastyBird\\DevicesModule',
              'devicesmoduledatastorage' => 'FastyBird\\DevicesModule\\DataStorage',
              'connector' => 'FastyBird\\FbMqttConnector\\Connector',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'metadataentities' => 'FastyBird\\Metadata\\Entities',
              'metadataexceptions' => 'FastyBird\\Metadata\\Exceptions',
              'flysystem' => 'League\\Flysystem',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'runtimeexception' => 'RuntimeException',
              'dbtestcase' => 'Tests\\Cases\\Unit\\DbTestCase',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testCreateConnector',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @throws Exceptions\\InvalidArgument
	 * @throws MetadataExceptions\\FileNotFound
	 * @throws Nette\\DI\\MissingServiceException
	 * @throws RuntimeException
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\Connector',
             'uses' => 
            array (
              'devicesmodule' => 'FastyBird\\DevicesModule',
              'devicesmoduledatastorage' => 'FastyBird\\DevicesModule\\DataStorage',
              'connector' => 'FastyBird\\FbMqttConnector\\Connector',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'metadataentities' => 'FastyBird\\Metadata\\Entities',
              'metadataexceptions' => 'FastyBird\\Metadata\\Exceptions',
              'flysystem' => 'League\\Flysystem',
              'nette' => 'Nette',
              'uuid' => 'Ramsey\\Uuid',
              'runtimeexception' => 'RuntimeException',
              'dbtestcase' => 'Tests\\Cases\\Unit\\DbTestCase',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/DI/ServicesTest.php' => 
  array (
    0 => 
    PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Tests\\Cases\\Unit\\DI\\ServicesTest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Tests\\Cases\\Unit\\BaseTestCase',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'testServicesRegistration',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @throws Nette\\DI\\MissingServiceException
	 */',
             'namespace' => 'Tests\\Cases\\Unit\\DI',
             'uses' => 
            array (
              'api' => 'FastyBird\\FbMqttConnector\\API',
              'consumers' => 'FastyBird\\FbMqttConnector\\Consumers',
              'nette' => 'Nette',
              'basetestcase' => 'Tests\\Cases\\Unit\\BaseTestCase',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  '/Users/akadlec/Development/FastyBird/connectors/fb-mqtt-connector/tests/cases/unit/DbTestCase.php' => 
  array (
    0 => 
    PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Tests\\Cases\\Unit\\DbTestCase',
       'phpDoc' => NULL,
       'abstract' => true,
       'final' => false,
       'extends' => 'PHPUnit\\Framework\\TestCase',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'setUp',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @throws Exceptions\\InvalidArgument
	 * @throws Nette\\DI\\MissingServiceException
	 * @throws RuntimeException
	 */',
             'namespace' => 'Tests\\Cases\\Unit',
             'uses' => 
            array (
              'datetimeimmutable' => 'DateTimeImmutable',
              'dbal' => 'Doctrine\\DBAL',
              'orm' => 'Doctrine\\ORM',
              'datetimefactory' => 'FastyBird\\DateTimeFactory',
              'di' => 'FastyBird\\FbMqttConnector\\DI',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'nettrineorm' => 'Nettrine\\ORM',
              'testcase' => 'PHPUnit\\Framework\\TestCase',
              'runtimeexception' => 'RuntimeException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'registerDatabaseSchemaFile',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'file',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'mockContainerService',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @throws Exceptions\\InvalidArgument
	 * @throws Nette\\DI\\MissingServiceException
	 * @throws RuntimeException
	 */',
             'namespace' => 'Tests\\Cases\\Unit',
             'uses' => 
            array (
              'datetimeimmutable' => 'DateTimeImmutable',
              'dbal' => 'Doctrine\\DBAL',
              'orm' => 'Doctrine\\ORM',
              'datetimefactory' => 'FastyBird\\DateTimeFactory',
              'di' => 'FastyBird\\FbMqttConnector\\DI',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'nettrineorm' => 'Nettrine\\ORM',
              'testcase' => 'PHPUnit\\Framework\\TestCase',
              'runtimeexception' => 'RuntimeException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'serviceType',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'serviceMock',
               'type' => 'object',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'getContainer',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @throws Exceptions\\InvalidArgument
	 * @throws Nette\\DI\\MissingServiceException
	 * @throws RuntimeException
	 */',
             'namespace' => 'Tests\\Cases\\Unit',
             'uses' => 
            array (
              'datetimeimmutable' => 'DateTimeImmutable',
              'dbal' => 'Doctrine\\DBAL',
              'orm' => 'Doctrine\\ORM',
              'datetimefactory' => 'FastyBird\\DateTimeFactory',
              'di' => 'FastyBird\\FbMqttConnector\\DI',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'nettrineorm' => 'Nettrine\\ORM',
              'testcase' => 'PHPUnit\\Framework\\TestCase',
              'runtimeexception' => 'RuntimeException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Nette\\DI\\Container',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'getDb',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @throws Exceptions\\InvalidArgument
	 * @throws Nette\\DI\\MissingServiceException
	 * @throws RuntimeException
	 */',
             'namespace' => 'Tests\\Cases\\Unit',
             'uses' => 
            array (
              'datetimeimmutable' => 'DateTimeImmutable',
              'dbal' => 'Doctrine\\DBAL',
              'orm' => 'Doctrine\\ORM',
              'datetimefactory' => 'FastyBird\\DateTimeFactory',
              'di' => 'FastyBird\\FbMqttConnector\\DI',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'nettrineorm' => 'Nettrine\\ORM',
              'testcase' => 'PHPUnit\\Framework\\TestCase',
              'runtimeexception' => 'RuntimeException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Doctrine\\DBAL\\Connection',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'getEntityManager',
           'phpDoc' => 
          PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
	 * @throws Exceptions\\InvalidArgument
	 * @throws Nette\\DI\\MissingServiceException
	 * @throws RuntimeException
	 */',
             'namespace' => 'Tests\\Cases\\Unit',
             'uses' => 
            array (
              'datetimeimmutable' => 'DateTimeImmutable',
              'dbal' => 'Doctrine\\DBAL',
              'orm' => 'Doctrine\\ORM',
              'datetimefactory' => 'FastyBird\\DateTimeFactory',
              'di' => 'FastyBird\\FbMqttConnector\\DI',
              'exceptions' => 'FastyBird\\FbMqttConnector\\Exceptions',
              'nette' => 'Nette',
              'nettrineorm' => 'Nettrine\\ORM',
              'testcase' => 'PHPUnit\\Framework\\TestCase',
              'runtimeexception' => 'RuntimeException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Nettrine\\ORM\\EntityManagerDecorator',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'registerNeonConfigurationFile',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'file',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'tearDown',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
); },
];
