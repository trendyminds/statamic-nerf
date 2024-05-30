<?php

namespace Trendyminds\Nerf\Tests;

use Statamic\Testing\AddonTestCase;
use Trendyminds\Nerf\ServiceProvider;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}
