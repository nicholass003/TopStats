<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_f4ca9dcdd7ec60f5\SOFe\InfoAPI\Template;

use Closure;
use pocketmine\command\CommandSender;
use RuntimeException;
use Shared\SOFe\InfoAPI\Mapping;
use Shared\SOFe\InfoAPI\Parameter;
use Nicholass003\TopStats\libs\_f4ca9dcdd7ec60f5\SOFe\AwaitGenerator\Traverser;
use Nicholass003\TopStats\libs\_f4ca9dcdd7ec60f5\SOFe\InfoAPI\Ast;
use Nicholass003\TopStats\libs\_f4ca9dcdd7ec60f5\SOFe\InfoAPI\Ast\MappingCall;
use Nicholass003\TopStats\libs\_f4ca9dcdd7ec60f5\SOFe\InfoAPI\Pathfind;
use Nicholass003\TopStats\libs\_f4ca9dcdd7ec60f5\SOFe\InfoAPI\ReadIndices;

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