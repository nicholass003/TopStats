<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\InfoAPI;

use Closure;
use Generator;
use pocketmine\event\Event;
use pocketmine\plugin\Plugin;
use pocketmine\world\Position;
use nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\AwaitGenerator\GeneratorUtil;
use nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\AwaitGenerator\Traverser;
use nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\PmEvent\Blocks;
use nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\PmEvent\Events;
use nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\Zleep\Zleep;















































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