<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_c3529f595dd55fae\SOFe\InfoAPI\Template;

use Closure;
use Generator;
use RuntimeException;
use Nicholass003\TopStats\libs\_c3529f595dd55fae\SOFe\AwaitGenerator\Await;
use Nicholass003\TopStats\libs\_c3529f595dd55fae\SOFe\AwaitGenerator\Traverser;

use function count;
use function implode;
use function is_string;



























































































































interface RenderedWatchElement extends RenderedElement {
	/**
	 * @return Traverser<string>
	 */
	public function watch() : Traverser;
}