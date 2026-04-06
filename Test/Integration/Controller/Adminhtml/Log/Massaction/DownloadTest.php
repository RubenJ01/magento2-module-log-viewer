<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Test\Integration\Controller\Adminhtml\Log\Massaction;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Download mass action should redirect to listing on invalid input.
 *
 * @magentoAppArea adminhtml
 */
class DownloadTest extends AbstractBackendController
{
    protected $resource = 'RJDS_LogViewer::view_logs';

    public function testMassDownloadWithoutSelectionRedirectsToListing(): void
    {
        $this->dispatch('backend/logviewer/log_massaction/download');
        $this->assertTrue($this->getResponse()->isRedirect());
    }
}

