<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_c03f4f8bb544f3e7\SOFe\InfoAPI;

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