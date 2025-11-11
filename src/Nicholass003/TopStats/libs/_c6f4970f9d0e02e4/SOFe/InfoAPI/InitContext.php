<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_c6f4970f9d0e02e4\SOFe\InfoAPI;

use Closure;
use Generator;
use pocketmine\event\Event;
use pocketmine\plugin\Plugin;
use pocketmine\world\Position;
use Nicholass003\TopStats\libs\_c6f4970f9d0e02e4\SOFe\AwaitGenerator\GeneratorUtil;
use Nicholass003\TopStats\libs\_c6f4970f9d0e02e4\SOFe\AwaitGenerator\Traverser;
use Nicholass003\TopStats\libs\_c6f4970f9d0e02e4\SOFe\PmEvent\Blocks;
use Nicholass003\TopStats\libs\_c6f4970f9d0e02e4\SOFe\PmEvent\Events;
use Nicholass003\TopStats\libs\_c6f4970f9d0e02e4\SOFe\Zleep\Zleep;

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