<?php

namespace Acmachado14\PactProvider;

use GuzzleHttp\Psr7\Uri;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfig;
use PhpPact\Standalone\ProviderVerifier\Verifier;
use PhpPact\Standalone\ProviderVerifier\Model\Config\PublishOptions;
use PhpPact\Standalone\ProviderVerifier\Model\Source\Broker;
use PhpPact\Standalone\ProviderVerifier\Model\ConsumerVersionSelectors;
use PhpPact\Standalone\ProviderVerifier\Model\Source\Url;

use PHPUnit\Framework\TestCase;

class ConsumerTest extends TestCase
{

    public function testPersonConsumer()
    {
        $config = new VerifierConfig();
        // $config->setLogLevel('DEBUG');
        $config
            ->getProviderInfo()
            ->setName("backend")
            ->setHost('localhost')
            ->setPort('8000')
            ->setScheme('http')
            ->setPath('/');

        // SETUP Pact Verifier Publishing options
        $publishOptions = new PublishOptions();
        $publishOptions
            ->setProviderVersion(exec('git rev-parse --short HEAD'))
            ->setProviderBranch(exec('git rev-parse --abbrev-ref HEAD'));

        // Once configured above, we need to apply to the verifier configuration
        // If any publish options are set, we will publish. You could configure
        // this option to only run if a CI env var is present, to allow for local
        // execution, without publishing results.
        $config->setPublishOptions($publishOptions);

        $verifier = new Verifier($config);
        // SETUP - Pact Broker
        // Note:- Not relevant if reading from a local directory or file


        $broker = new Broker();
        $broker->setUrl(new Uri('http://localhost:9292'));

        // SETUP - Pact Broker Authentication Mechanisms

        // $broker->setUsername('user')->setPassword('pass'); // Pact Broker with Auth
        // $broker->setToken('pass'); // PactFlow Broker

        $selectors = (new ConsumerVersionSelectors())
            ->addSelector('{"mainBranch":"true"}');

        // SETUP - Pact Broker Dynamic Retrieval Methods. 
        // Note: - Only use if not using Pact by Url, File or Directory
        // These should only be set, when running builds based on a provider change

        $broker->setConsumerVersionSelectors($selectors);

        // SETUP - Verify Pact By Url Methods.
        // If a Pact Url is provided, you should not add the dynamic retrieval methods
        // This would be run when a consumer contract that requires verification is published

        $url = new Url();
        $url->setUrl(new Uri('http://localhost:9292/pacts/provider/backend/consumer/frontend/latest'));

        // SETUP Pact Verification Sources
        // You should pick at least one of these.
        // Pacts verified by file or directory should not publish results to a broker

        // 1. Verify by Pact Url
        $verifier->addUrl($url);


        // 2. Verify by Pact Broker using Consumer Version Selectors
        // $verifier->addBroker($broker);

        // 3. Verify by file source
        // $verifier->addFile(dirname(__FILE__) . '/../../consumer/pacts/frontend-backend.json');

        // 4. Verify by directory
        // $verifier->addDirectory(dirname(__FILE__) . '/../../consumer/pacts');

        $verifyResult = $verifier->verify();

        $this->assertTrue($verifyResult);
    }
}
