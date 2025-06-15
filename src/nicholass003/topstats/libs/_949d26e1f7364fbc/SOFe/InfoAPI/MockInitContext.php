<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_949d26e1f7364fbc\SOFe\InfoAPI;

use Closure;
use Generator;
use pocketmine\event\Event;
use pocketmine\plugin\Plugin;
use pocketmine\world\Position;
use nicholass003\topstats\libs\_949d26e1f7364fbc\SOFe\AwaitGenerator\GeneratorUtil;
use nicholass003\topstats\libs\_949d26e1f7364fbc\SOFe\AwaitGenerator\Traverser;
use nicholass003\topstats\libs\_949d26e1f7364fbc\SOFe\PmEvent\Blocks;
use nicholass003\topstats\libs\_949d26e1f7364fbc\SOFe\PmEvent\Events;
use nicholass003\topstats\libs\_949d26e1f7364fbc\SOFe\Zleep\Zleep;















































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