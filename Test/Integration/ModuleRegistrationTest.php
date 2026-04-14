<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Test\Integration;

use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Ensures the module is registered when integration bootstrap runs.
 */
class ModuleRegistrationTest extends TestCase
{
    public function testModuleIsRegistered(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $moduleList = $objectManager->get(ModuleList::class);
        $this->assertTrue($moduleList->has('RJDS_LogViewer'));
    }
}
