<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_2f2e26b52fbe84a5\SOFe\InfoAPI\Template;

use Closure;
use Generator;
use RuntimeException;
use nicholass003\topstats\libs\_2f2e26b52fbe84a5\SOFe\AwaitGenerator\Await;
use nicholass003\topstats\libs\_2f2e26b52fbe84a5\SOFe\AwaitGenerator\Traverser;

use function count;
use function implode;
use function is_string;



























































































































interface RenderedWatchElement extends RenderedElement {
	/**
	 * @return Traverser<string>
	 */
	public function watch() : Traverser;
}