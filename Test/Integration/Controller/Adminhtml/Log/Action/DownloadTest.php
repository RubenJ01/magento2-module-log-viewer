<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Test\Integration\Controller\Adminhtml\Log\Action;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Single-file download action (invalid id should redirect back to listing).
 *
 * @magentoAppArea adminhtml
 */
class DownloadTest extends AbstractBackendController
{
    protected $resource = 'RJDS_LogViewer::view_logs';

    protected $uri = 'backend/logviewer/log_action/download/id/0';

    public function testDownloadWithInvalidIdRedirectsToListing(): void
    {
        $this->dispatch($this->uri);
        $this->assertTrue($this->getResponse()->isRedirect());
    }
}

