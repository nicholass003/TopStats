<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_84622ff2cf681b8b\SOFe\InfoAPI\Template;

use pocketmine\command\CommandSender;
use Shared\SOFe\InfoAPI\Display;
use nicholass003\topstats\libs\_84622ff2cf681b8b\SOFe\AwaitGenerator\Traverser;

use function count;
use function sprintf;











final class RawText implements TemplateElement {
	public function __construct(public string $raw) {
	}

	public function render(mixed $context, ?CommandSender $sender, GetOrWatch $getOrWatch) : RenderedElement {
		return $getOrWatch->staticElement($this->raw);
	}
}