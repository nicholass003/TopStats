<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_5d0cf7a60b86570e\SOFe\InfoAPI;

use Closure;
use Generator;
use pocketmine\event\Event;
use pocketmine\plugin\Plugin;
use pocketmine\world\Position;
use nicholass003\topstats\libs\_5d0cf7a60b86570e\SOFe\AwaitGenerator\GeneratorUtil;
use nicholass003\topstats\libs\_5d0cf7a60b86570e\SOFe\AwaitGenerator\Traverser;
use nicholass003\topstats\libs\_5d0cf7a60b86570e\SOFe\PmEvent\Blocks;
use nicholass003\topstats\libs\_5d0cf7a60b86570e\SOFe\PmEvent\Events;
use nicholass003\topstats\libs\_5d0cf7a60b86570e\SOFe\Zleep\Zleep;





















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