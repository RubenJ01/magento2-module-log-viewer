<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Test\Integration\Controller\Adminhtml\Log\Massaction;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Delete mass action should redirect to listing on invalid input.
 *
 * @magentoAppArea adminhtml
 */
class DeleteTest extends AbstractBackendController
{
    protected $resource = 'RJDS_LogViewer::view_logs';

    public function testMassDeleteWithoutSelectionRedirectsToListing(): void
    {
        $this->dispatch('backend/logviewer/log_massaction/delete');
        $this->assertTrue($this->getResponse()->isRedirect());
    }
}

