<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_2f2e26b52fbe84a5\SOFe\InfoAPI\Ast;

use JsonException;
use Shared\SOFe\InfoAPI\Mapping;
use nicholass003\topstats\libs\_2f2e26b52fbe84a5\SOFe\InfoAPI\QualifiedRef;
use nicholass003\topstats\libs\_2f2e26b52fbe84a5\SOFe\InfoAPI\StringParser;
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