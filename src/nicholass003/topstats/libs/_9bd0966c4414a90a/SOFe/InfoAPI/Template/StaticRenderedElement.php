<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\InfoAPI\Template;

use pocketmine\command\CommandSender;
use Shared\SOFe\InfoAPI\Display;
use nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\AwaitGenerator\Traverser;

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