<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_f4ca9dcdd7ec60f5\SOFe\InfoAPI\Ast;

use JsonException;
use Shared\SOFe\InfoAPI\Mapping;
use Nicholass003\TopStats\libs\_f4ca9dcdd7ec60f5\SOFe\InfoAPI\QualifiedRef;
use Nicholass003\TopStats\libs\_f4ca9dcdd7ec60f5\SOFe\InfoAPI\StringParser;
use function is_numeric;
use function is_string;
use function json_decode;
use function strlen;

/** The entire template string. */
final class Template {
	public function __construct(
		/** @var (RawText|Expr)[] */
		public array $elements,
	) {
	}
}