<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_45c7098180b1d18e\SOFe\InfoAPI;

use Closure;
use Generator;
use pocketmine\event\Event;
use pocketmine\plugin\Plugin;
use pocketmine\world\Position;
use Nicholass003\TopStats\libs\_45c7098180b1d18e\SOFe\AwaitGenerator\GeneratorUtil;
use Nicholass003\TopStats\libs\_45c7098180b1d18e\SOFe\AwaitGenerator\Traverser;
use Nicholass003\TopStats\libs\_45c7098180b1d18e\SOFe\PmEvent\Blocks;
use Nicholass003\TopStats\libs\_45c7098180b1d18e\SOFe\PmEvent\Events;
use Nicholass003\TopStats\libs\_45c7098180b1d18e\SOFe\Zleep\Zleep;















































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