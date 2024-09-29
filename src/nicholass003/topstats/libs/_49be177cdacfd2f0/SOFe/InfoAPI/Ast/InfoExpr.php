<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_49be177cdacfd2f0\SOFe\InfoAPI\Ast;

use JsonException;
use Shared\SOFe\InfoAPI\Mapping;
use nicholass003\topstats\libs\_49be177cdacfd2f0\SOFe\InfoAPI\QualifiedRef;
use nicholass003\topstats\libs\_49be177cdacfd2f0\SOFe\InfoAPI\StringParser;
use function is_numeric;
use function is_string;
use function json_decode;
use function strlen;
































/** An expression that resolves info. */
final class InfoExpr {
	public function __construct(
		/** The parent part, e.g. `a b` in `{{ a b c }}`. */
		public ?InfoExpr $parent,

		/** The part that calls a mapping on the parent, e.g. `c(...)` in `{{ a b c(...) }}`. */
		public MappingCall $call,
	) {
	}
}