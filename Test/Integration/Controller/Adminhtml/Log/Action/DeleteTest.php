<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Test\Integration\Controller\Adminhtml\Log\Action;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Delete actions should redirect to listing on invalid inputs.
 *
 * @magentoAppArea adminhtml
 */
class DeleteTest extends AbstractBackendController
{
    protected $resource = 'RJDS_LogViewer::view_logs';

    protected $uri = 'backend/logviewer/log_action/delete/id/0';

    public function testSingleDeleteWithInvalidIdRedirectsToListing(): void
    {
        $this->dispatch($this->uri);
        $this->assertTrue($this->getResponse()->isRedirect());
    }

}


