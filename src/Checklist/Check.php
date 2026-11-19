<?php

declare(strict_types=1);

namespace Yu\WebToolsCore\Checklist;

/**
 * One check of a checklist report. Its title, description and advice are translations keyed by $code;
 * $note picks the message about what was found, $evidence lists the found things as they are (names, lines, URLs).
 */
final readonly class Check
{
    /**
     * @param  list<string>  $evidence
     * @param  array<string, string|int>  $replace  placeholders for the note
     */
    public function __construct(
        public string $code,
        public CheckStatus $status,
        public int $points,
        public int $max,
        public ?string $note = null,
        public array $replace = [],
        public array $evidence = [],
        public ?string $snippet = null,
    ) {}

    /**
     * @param  list<string>  $evidence
     * @param  array<string, string|int>  $replace
     */
    public static function pass(string $code, int $max, ?string $note = null, array $replace = [], array $evidence = []): self
    {
        return new self($code, CheckStatus::Pass, $max, $max, $note, $replace, $evidence);
    }

    /**
     * @param  list<string>  $evidence
     * @param  array<string, string|int>  $replace
     */
    public static function warn(string $code, int $points, int $max, ?string $note = null, array $replace = [], array $evidence = [], ?string $snippet = null): self
    {
        return new self($code, CheckStatus::Warn, $points, $max, $note, $replace, $evidence, $snippet);
    }

    /**
     * @param  list<string>  $evidence
     * @param  array<string, string|int>  $replace
     */
    public static function fail(string $code, int $max, ?string $note = null, array $replace = [], array $evidence = [], ?string $snippet = null): self
    {
        return new self($code, CheckStatus::Fail, 0, $max, $note, $replace, $evidence, $snippet);
    }

    /**
     * @param  array<string, string|int>  $replace
     */
    public static function skip(string $code, int $max, ?string $note = null, array $replace = []): self
    {
        return new self($code, CheckStatus::Skip, 0, $max, $note, $replace);
    }

    /**
     * @return array{code: string, status: string, points: int, max: int, note: ?string, replace: array<string, string|int>, evidence: list<string>, snippet: ?string}
     */
    public function toArray(): array
    {
        return [...get_object_vars($this), 'status' => $this->status->value];
    }
}
