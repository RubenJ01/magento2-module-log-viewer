<?php

use Magento\Framework\Component\ComponentRegistrar;

/*
 * Register the rjds/php-humanize library as a Magento "library" component so
 * that its classes are picked up by `bin/magento setup:di:compile`. Without
 * this, the <preference> in etc/di.xml for Rjds\PhpHumanize\HumanizerInterface
 * fails compilation with "the latter has not been included in dependency
 * injection compilation."
 */
$humanizerPath = \dirname(__DIR__) . '/php-humanize';
$registrar = new ComponentRegistrar();
if (\is_dir($humanizerPath)
    && $registrar->getPath(ComponentRegistrar::LIBRARY, 'rjds/php-humanize') === null
) {
    ComponentRegistrar::register(ComponentRegistrar::LIBRARY, 'rjds/php-humanize', $humanizerPath);
}

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'RJDS_LogViewer',
    __DIR__
);
