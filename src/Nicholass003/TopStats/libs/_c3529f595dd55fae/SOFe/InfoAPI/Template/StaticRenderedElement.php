<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_c3529f595dd55fae\SOFe\InfoAPI\Template;

use pocketmine\command\CommandSender;
use Shared\SOFe\InfoAPI\Display;
use Nicholass003\TopStats\libs\_c3529f595dd55fae\SOFe\AwaitGenerator\Traverser;

use function count;
use function sprintf;




















final class StaticRenderedElement implements RenderedGetElement, RenderedWatchElement {
	public function __construct(private string $raw) {
	}

	public function get() : string {
		return $this->raw;
	}

	public function watch() : Traverser {
		return Traverser::fromClosure(function() {
			yield $this->raw => Traverser::VALUE;
		});
	}
}