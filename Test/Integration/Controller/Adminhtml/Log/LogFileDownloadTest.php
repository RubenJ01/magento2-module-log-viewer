<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Test\Integration\Controller\Adminhtml\Log;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Single-file download action (invalid id should redirect back to listing).
 *
 * @magentoAppArea adminhtml
 */
class LogFileDownloadTest extends AbstractBackendController
{
    public function testDownloadWithInvalidIdRedirectsToListing(): void
    {
        $this->dispatch('backend/logviewer/log_action/download/id/0');
        $this->assertTrue($this->getResponse()->isRedirect());
    }
}
