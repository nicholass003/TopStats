<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\InfoAPI\Template;

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