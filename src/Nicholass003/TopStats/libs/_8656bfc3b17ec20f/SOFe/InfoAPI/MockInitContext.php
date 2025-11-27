<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_8656bfc3b17ec20f\SOFe\InfoAPI;

use Closure;
use Generator;
use pocketmine\event\Event;
use pocketmine\plugin\Plugin;
use pocketmine\world\Position;
use Nicholass003\TopStats\libs\_8656bfc3b17ec20f\SOFe\AwaitGenerator\GeneratorUtil;
use Nicholass003\TopStats\libs\_8656bfc3b17ec20f\SOFe\AwaitGenerator\Traverser;
use Nicholass003\TopStats\libs\_8656bfc3b17ec20f\SOFe\PmEvent\Blocks;
use Nicholass003\TopStats\libs\_8656bfc3b17ec20f\SOFe\PmEvent\Events;
use Nicholass003\TopStats\libs\_8656bfc3b17ec20f\SOFe\Zleep\Zleep;















































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