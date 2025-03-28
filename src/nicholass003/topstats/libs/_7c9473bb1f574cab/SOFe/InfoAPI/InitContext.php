<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_7c9473bb1f574cab\SOFe\InfoAPI;

use Closure;
use Generator;
use pocketmine\event\Event;
use pocketmine\plugin\Plugin;
use pocketmine\world\Position;
use nicholass003\topstats\libs\_7c9473bb1f574cab\SOFe\AwaitGenerator\GeneratorUtil;
use nicholass003\topstats\libs\_7c9473bb1f574cab\SOFe\AwaitGenerator\Traverser;
use nicholass003\topstats\libs\_7c9473bb1f574cab\SOFe\PmEvent\Blocks;
use nicholass003\topstats\libs\_7c9473bb1f574cab\SOFe\PmEvent\Events;
use nicholass003\topstats\libs\_7c9473bb1f574cab\SOFe\Zleep\Zleep;

interface InitContext {
	/**
	 * @template E of Event
	 * @param class-string<E>[] $events
	 * @param Closure(E): string $interpreter
	 * @return Traverser<E>
	 */
	public function watchEvent(array $events, string $key, Closure $interpreter) : Traverser;

	/**
	 * @return Traverser<null>
	 */
	public function watchBlock(Position $position) : Traverser;

	/**
	 * @return Generator<mixed, mixed, mixed, void>
	 */
	public function sleep(int $ticks) : Generator;
}