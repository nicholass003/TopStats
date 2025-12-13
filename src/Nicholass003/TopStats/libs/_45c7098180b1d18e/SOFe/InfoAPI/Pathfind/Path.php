<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_45c7098180b1d18e\SOFe\InfoAPI\Pathfind;

use Closure;
use Shared\SOFe\InfoAPI\Mapping;
use Nicholass003\TopStats\libs\_45c7098180b1d18e\SOFe\InfoAPI\QualifiedRef;
use Nicholass003\TopStats\libs\_45c7098180b1d18e\SOFe\InfoAPI\ReadIndices;
use SplPriorityQueue;
use function array_merge;
use function array_shift;
use function count;








































































final class Path {
	/**
	 * @param QualifiedRef[] $unreadCalls
	 * @param Mapping[] $mappings
	 * @param array<string, true> $implicitLoopDetector
	 */
	public function __construct(
		public array $unreadCalls,
		public string $tailKind,
		public array $mappings,
		public array $implicitLoopDetector,
		public Cost $cost,
	) {
	}
}