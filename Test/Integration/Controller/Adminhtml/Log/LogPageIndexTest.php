<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Test\Integration\Controller\Adminhtml\Log;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Admin log listing page (HTTP integration).
 *
 * @magentoAppArea adminhtml
 */
class LogPageIndexTest extends AbstractBackendController
{
    protected $resource = 'RJDS_LogViewer::view_logs';

    protected $uri = 'backend/logviewer/log_page/index';

    public function testIndexPageRendersViewLogs(): void
    {
        $this->dispatch($this->uri);
        $this->assertNotSame(404, $this->getResponse()->getHttpResponseCode());
        $body = $this->getResponse()->getBody();
        $this->assertStringContainsString('View Logs', $body);
    }
}
