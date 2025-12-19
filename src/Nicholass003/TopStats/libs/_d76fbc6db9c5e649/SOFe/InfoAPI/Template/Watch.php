<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_d76fbc6db9c5e649\SOFe\InfoAPI\Template;

use Closure;
use Generator;
use RuntimeException;
use Nicholass003\TopStats\libs\_d76fbc6db9c5e649\SOFe\AwaitGenerator\Await;
use Nicholass003\TopStats\libs\_d76fbc6db9c5e649\SOFe\AwaitGenerator\Traverser;

use function count;
use function implode;
use function is_string;

/**
 * @implements GetOrWatch<RenderedWatchElement, RenderedWatchGroup>
 */
final class Watch implements GetOrWatch {
	public function buildResult(array $elements) : RenderedGroup {
		return new RenderedWatchGroup($elements);
	}

	public function startEvalChain() : EvalChain {
		return new WatchEvalChain;
	}

	public function staticElement(string $raw) : RenderedElement {
		return new StaticRenderedElement($raw);
	}
}