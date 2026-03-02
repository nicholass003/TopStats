<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_c3529f595dd55fae\SOFe\PmEvent;

use Closure;
use Nicholass003\TopStats\libs\_c3529f595dd55fae\SOFe\AwaitGenerator\Await;
use Nicholass003\TopStats\libs\_c3529f595dd55fae\SOFe\AwaitGenerator\Channel;
use Nicholass003\TopStats\libs\_c3529f595dd55fae\SOFe\AwaitGenerator\Traverser;

final class Util {
	/**
	 * @template T
	 * @param Channel<T>[] $channels
	 * @param ?Closure(): void $finalize
	 * @return Traverser<T>
	 */
	public static function traverseChannels(array $channels, ?Closure $finalize = null) : Traverser {
		return Traverser::fromClosure(function() use ($channels, $finalize) {
			try {
				while (true) {
					[, $value] = yield from Await::safeRace(array_map(fn(Channel $channel) => $channel->receive(), $channels));
					yield $value => Traverser::VALUE;
				}
			} finally {
				if($finalize !== null) {
					$finalize();
				}
			}
		});
	}
}