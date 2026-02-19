<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_2d130c0445b04077\SOFe\InfoAPI;

use Closure;
use Generator;
use pocketmine\event\Event;
use pocketmine\plugin\Plugin;
use pocketmine\world\Position;
use Nicholass003\TopStats\libs\_2d130c0445b04077\SOFe\AwaitGenerator\GeneratorUtil;
use Nicholass003\TopStats\libs\_2d130c0445b04077\SOFe\AwaitGenerator\Traverser;
use Nicholass003\TopStats\libs\_2d130c0445b04077\SOFe\PmEvent\Blocks;
use Nicholass003\TopStats\libs\_2d130c0445b04077\SOFe\PmEvent\Events;
use Nicholass003\TopStats\libs\_2d130c0445b04077\SOFe\Zleep\Zleep;















































final class MockInitContext implements InitContext {
	public function watchEvent(array $events, string $key, Closure $interpreter) : Traverser {
		return new Traverser(GeneratorUtil::empty());
	}

	public function watchBlock(Position $position) : Traverser {
		return new Traverser(GeneratorUtil::empty());
	}

	public function sleep(int $ticks) : Generator {
		return GeneratorUtil::pending();
	}
}