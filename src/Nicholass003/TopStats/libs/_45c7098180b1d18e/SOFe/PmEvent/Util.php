<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_45c7098180b1d18e\SOFe\PmEvent;

use Closure;
use Nicholass003\TopStats\libs\_45c7098180b1d18e\SOFe\AwaitGenerator\Await;
use Nicholass003\TopStats\libs\_45c7098180b1d18e\SOFe\AwaitGenerator\Channel;
use Nicholass003\TopStats\libs\_45c7098180b1d18e\SOFe\AwaitGenerator\Traverser;

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