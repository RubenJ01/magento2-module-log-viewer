<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Ui\Component\Listing;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\MassAction as BaseMassAction;
use RJDS\LogViewer\Model\Config\Config;

/**
 * Mass action component that hides the delete action when the feature is
 * disabled in store configuration.
 */
class MassAction extends BaseMassAction
{
    /**
     * @param UiComponentInterface[] $components
     * @param array<string, mixed>   $data
     */
    public function __construct(
        ContextInterface $context,
        private readonly Config $config,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
    }

    public function prepare(): void
    {
        if (!$this->config->isDeleteEnabled()) {
            foreach ($this->getChildComponents() as $name => $actionComponent) {
                if ($name !== 'delete') {
                    continue;
                }

                $componentConfig = $actionComponent->getConfiguration();
                $componentConfig['actionDisable'] = true;
                $actionComponent->setData('config', $componentConfig);
            }
        }

        parent::prepare();
    }
}
