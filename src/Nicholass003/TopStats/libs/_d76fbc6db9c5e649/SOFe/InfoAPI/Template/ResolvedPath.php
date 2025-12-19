<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_d76fbc6db9c5e649\SOFe\InfoAPI\Template;

use Closure;
use pocketmine\command\CommandSender;
use RuntimeException;
use Shared\SOFe\InfoAPI\Mapping;
use Shared\SOFe\InfoAPI\Parameter;
use Nicholass003\TopStats\libs\_d76fbc6db9c5e649\SOFe\AwaitGenerator\Traverser;
use Nicholass003\TopStats\libs\_d76fbc6db9c5e649\SOFe\InfoAPI\Ast;
use Nicholass003\TopStats\libs\_d76fbc6db9c5e649\SOFe\InfoAPI\Ast\MappingCall;
use Nicholass003\TopStats\libs\_d76fbc6db9c5e649\SOFe\InfoAPI\Pathfind;
use Nicholass003\TopStats\libs\_d76fbc6db9c5e649\SOFe\InfoAPI\ReadIndices;

use function array_keys;
use function array_map;
use function count;
use function implode;
use function json_decode;
use function range;
use function sprintf;







































































































































































final class ResolvedPath {
	/**
	 * @param non-empty-array<int, ResolvedPathSegment> $segments
	 */
	public function __construct(
		public array $segments,
	) {
	}

	public function getTargetKind() : string {
		return $this->segments[count($this->segments) - 1]->mapping->targetKind;
	}
}