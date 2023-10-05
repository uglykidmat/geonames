<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SentryTestController extends AbstractController
{
    public function __construct(private LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route(name: 'sentry_test', path: '/_sentry-test', env: 'dev')]
    public function testLog()
    {
        // the following code will test if monolog integration logs to sentry
        $this->logger->error('Monolog error.', ['Cause' => 'this is a test']);

        // the following code will test if an uncaught exception logs to sentry
        throw new RuntimeException('Sentry test error.');
    }
}
