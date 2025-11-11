<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_c6f4970f9d0e02e4\SOFe\InfoAPI;

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