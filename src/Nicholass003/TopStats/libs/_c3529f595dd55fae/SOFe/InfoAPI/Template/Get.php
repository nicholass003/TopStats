<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_c3529f595dd55fae\SOFe\InfoAPI\Template;

use Closure;
use RuntimeException;
use function is_string;

/**
 * @implements GetOrWatch<RenderedGetElement, RenderedGetGroup>
 */
final class Get implements GetOrWatch {
	public function buildResult(array $elements) : RenderedGroup {
		$rendered = [];
		foreach ($elements as $element) {
			$rendered[] = $element;
		}
		return new RenderedGetGroup($rendered);
	}

	public function startEvalChain() : EvalChain {
		return new GetEvalChain;
	}

	public function staticElement(string $raw) : RenderedElement {
		return new StaticRenderedElement($raw);
	}
}