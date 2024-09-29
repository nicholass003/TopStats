<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_819d6c159e0d8f04\SOFe\InfoAPI\Template;

use Closure;
use pocketmine\command\CommandSender;
use RuntimeException;
use Shared\SOFe\InfoAPI\Mapping;
use Shared\SOFe\InfoAPI\Parameter;
use nicholass003\topstats\libs\_819d6c159e0d8f04\SOFe\AwaitGenerator\Traverser;
use nicholass003\topstats\libs\_819d6c159e0d8f04\SOFe\InfoAPI\Ast;
use nicholass003\topstats\libs\_819d6c159e0d8f04\SOFe\InfoAPI\Ast\MappingCall;
use nicholass003\topstats\libs\_819d6c159e0d8f04\SOFe\InfoAPI\Pathfind;
use nicholass003\topstats\libs\_819d6c159e0d8f04\SOFe\InfoAPI\ReadIndices;

use function array_keys;
use function array_map;
use function count;
use function implode;
use function json_decode;
use function range;
use function sprintf;





















































































































































































final class ResolvedPathSegment {
	/**
	 * @param list<ResolvedPathArg> $args
	 */
	public function __construct(
		public Mapping $mapping,
		public array $args,
	) {
	}
}