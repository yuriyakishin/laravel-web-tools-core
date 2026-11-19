<?php

namespace Yu\WebToolsCore\Checklist;

/**
 * A checklist report: groups of checks, a score out of 100 and a verdict in words.
 * The score is the points earned out of the points available; skipped checks are left out of both.
 */
final readonly class ChecklistReport
{
    /** Lowest score for each verdict, best first; the texts are the tool's messages.verdicts.{key}. */
    public const array VERDICTS = ['ready' => 90, 'almost' => 70, 'partly' => 50, 'not_ready' => 0];

    /**
     * @param  array<string, list<Check>>  $groups  checks by group code, e.g. ['A' => [...], 'B' => [...]]
     */
    public function __construct(
        public string $url,
        public string $locale,
        public string $checkedAt,
        public string $ruleset,
        public array $groups,
    ) {}

    public function score(): int
    {
        [$points, $max] = $this->totals(array_merge(...array_values($this->groups)));

        return $max === 0 ? 0 : (int) round($points / $max * 100);
    }

    public static function verdict(int $score): string
    {
        foreach (self::VERDICTS as $verdict => $minimum) {
            if ($score >= $minimum) {
                return $verdict;
            }
        }

        return 'not_ready';
    }

    /**
     * The form kept in the Livewire component and in the cache.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $groups = [];
        foreach ($this->groups as $code => $checks) {
            [$points, $max] = $this->totals($checks);
            $groups[] = [
                'code' => $code,
                'points' => $points,
                'max' => $max,
                'checks' => array_map(fn (Check $check): array => $check->toArray(), $checks),
            ];
        }

        return [
            'url' => $this->url,
            'locale' => $this->locale,
            'checkedAt' => $this->checkedAt,
            'ruleset' => $this->ruleset,
            'score' => $score = $this->score(),
            'verdict' => self::verdict($score),
            'groups' => $groups,
        ];
    }

    /**
     * @param  list<Check>  $checks
     * @return array{0: int, 1: int}
     */
    private function totals(array $checks): array
    {
        $counted = array_filter($checks, fn (Check $check): bool => $check->status !== CheckStatus::Skip);

        return [
            array_sum(array_map(fn (Check $check): int => $check->points, $counted)),
            array_sum(array_map(fn (Check $check): int => $check->max, $counted)),
        ];
    }
}
