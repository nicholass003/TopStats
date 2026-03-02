<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_86cd70581b547d1c\SOFe\InfoAPI;

use Closure;
use Generator;
use pocketmine\event\Event;
use pocketmine\plugin\Plugin;
use pocketmine\world\Position;
use Nicholass003\TopStats\libs\_86cd70581b547d1c\SOFe\AwaitGenerator\GeneratorUtil;
use Nicholass003\TopStats\libs\_86cd70581b547d1c\SOFe\AwaitGenerator\Traverser;
use Nicholass003\TopStats\libs\_86cd70581b547d1c\SOFe\PmEvent\Blocks;
use Nicholass003\TopStats\libs\_86cd70581b547d1c\SOFe\PmEvent\Events;
use Nicholass003\TopStats\libs\_86cd70581b547d1c\SOFe\Zleep\Zleep;





















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