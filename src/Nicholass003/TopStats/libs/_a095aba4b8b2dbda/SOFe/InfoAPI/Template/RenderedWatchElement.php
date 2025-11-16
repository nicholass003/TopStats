<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_a095aba4b8b2dbda\SOFe\InfoAPI\Template;

use Closure;
use Generator;
use RuntimeException;
use Nicholass003\TopStats\libs\_a095aba4b8b2dbda\SOFe\AwaitGenerator\Await;
use Nicholass003\TopStats\libs\_a095aba4b8b2dbda\SOFe\AwaitGenerator\Traverser;

use function count;
use function implode;
use function is_string;



























































































































interface RenderedWatchElement extends RenderedElement {
	/**
	 * @return Traverser<string>
	 */
	public function watch() : Traverser;
}