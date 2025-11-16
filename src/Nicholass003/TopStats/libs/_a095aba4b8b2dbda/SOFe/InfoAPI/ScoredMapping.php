<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_a095aba4b8b2dbda\SOFe\InfoAPI;

use Shared\SOFe\InfoAPI\Mapping;

use function array_filter;
use function array_unshift;
use function count;



























































final class ScoredMapping {
	public function __construct(
		public int $score,
		public Mapping $mapping,
	) {
	}
}