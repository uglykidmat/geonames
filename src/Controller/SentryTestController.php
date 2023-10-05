<?php

namespace App\Controller;

use RuntimeException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SentryTestController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route(name="sentry_test", path="/_sentry-test")
     */
    #[Route(name: 'sentry_test', path: '/_sentry-test')]
    public function testLog()
    {
        // the following code will test if monolog integration logs to sentry
        $this->logger->error('My custom monolog error.');

        // the following code will test if an uncaught exception logs to sentry
        throw new RuntimeException('Sentry test error.');
    }
}
