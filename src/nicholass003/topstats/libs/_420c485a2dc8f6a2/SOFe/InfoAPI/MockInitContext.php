<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_420c485a2dc8f6a2\SOFe\InfoAPI;

use Closure;
use Generator;
use pocketmine\event\Event;
use pocketmine\plugin\Plugin;
use pocketmine\world\Position;
use nicholass003\topstats\libs\_420c485a2dc8f6a2\SOFe\AwaitGenerator\GeneratorUtil;
use nicholass003\topstats\libs\_420c485a2dc8f6a2\SOFe\AwaitGenerator\Traverser;
use nicholass003\topstats\libs\_420c485a2dc8f6a2\SOFe\PmEvent\Blocks;
use nicholass003\topstats\libs\_420c485a2dc8f6a2\SOFe\PmEvent\Events;
use nicholass003\topstats\libs\_420c485a2dc8f6a2\SOFe\Zleep\Zleep;















































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