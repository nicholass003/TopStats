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





















final class PluginInitContext implements InitContext {
	public function __construct(private Plugin $plugin) {
	}

	public function watchEvent(array $events, string $key, Closure $interpreter) : Traverser {
		return Events::watch($this->plugin, $events, $key, $interpreter);
	}

	public function watchBlock(Position $position) : Traverser {
		return Traverser::fromClosure(function() use ($position) {
			$traverser = Blocks::watch($position);
			try {
				while ($traverser->next($_block)) {
					yield null => Traverser::VALUE;
				}
			} finally {
				yield from $traverser->interrupt();
			}
		});
	}

	public function sleep(int $ticks) : Generator {
		return Zleep::sleepTicks($this->plugin, $ticks);
	}
}