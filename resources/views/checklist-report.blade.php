{{--
    A checklist report (ChecklistReport::toArray()).
    @include('web-tools-core::checklist-report', ['report' => $report, 'tool' => 'webmcp-analyzer', 'locale' => $locale])
    Texts come from the tool: {tool}::messages.verdicts.{verdict}, groups.{code}.title|text,
    checks.{code}.title|text|fix and checks.{code}.notes.{note}. The score bar and group meters are green from 70% (almost ready and up), amber from 50%, red below. Groups are numbered 1, 2… and checks 01, 02… in the order
    of the report; the internal codes (A, A1) are never shown.
    "Check again" dispatches the browser event "wt-again"; the tool's view scrolls back to its form.
--}}
@php
    $own = fn (string $key, array $replace = []) => __($tool.'::messages.'.$key, $replace, $locale);
    $core = fn (string $key, array $replace = []) => __('web-tools-core::messages.'.$key, $replace, $locale);
    $percent = fn (array $group): int => $group['max'] > 0 ? (int) round($group['points'] / $group['max'] * 100) : 0;
    $tone = fn (array $group): string => match (true) {
        $group['max'] === 0 => 'skip',
        $percent($group) >= 70 => 'pass',
        $percent($group) >= 50 => 'warn',
        default => 'fail',
    };
    $verdictTone = ['ready' => 'pass', 'almost' => 'pass', 'partly' => 'warn', 'not_ready' => 'fail'];
    $counts = array_count_values(array_column(array_merge(...array_column($report['groups'], 'checks')), 'status'));
    $number = 0;
@endphp

<div class="wt-report" x-data="{
        copied: null,
        copy(text, key) {
            const done = () => { this.copied = key; setTimeout(() => this.copied = null, 1500) };
            if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(text).then(done); return }
            const area = Object.assign(document.createElement('textarea'), { value: text, readOnly: true });
            area.style.cssText = 'position:fixed;opacity:0';
            document.body.append(area); area.select(); document.execCommand('copy'); area.remove(); done();
        },
    }">
    <div class="wt-head">
        <p class="wt-verdict"><b>{{ $own('verdicts.'.$report['verdict']) }}</b><span>{{ $report['score'] }} / 100</span></p>

        {{-- Filled up to the score in the colour of the verdict; ticks and labels sit at the verdict thresholds --}}
        <div class="wt-scale wt-{{ $verdictTone[$report['verdict']] }}" style="--v: {{ $report['score'] }}%" aria-hidden="true">
            <i></i>
            @foreach (array_filter(\Yu\WebToolsCore\Checklist\ChecklistReport::VERDICTS) as $minimum)
                <b style="--at: {{ $minimum }}%"></b>
            @endforeach
        </div>
        <div class="wt-marks" aria-hidden="true">
            @foreach (array_reverse(\Yu\WebToolsCore\Checklist\ChecklistReport::VERDICTS) as $mark => $minimum)
                <span style="--at: {{ $minimum }}%">{{ $core('scale.'.$mark) }}</span>
            @endforeach
        </div>

        <div class="wt-meters">
            @foreach ($report['groups'] as $i => $group)
                <a class="wt-m" href="#wt-group-{{ $i + 1 }}">
                    <span class="wt-m-title"><span class="wt-gn">{{ $i + 1 }}</span>{{ $own('groups.'.$group['code'].'.title') }}</span>
                    <span class="wt-m-points">{{ $group['max'] > 0 ? $group['points'].'/'.$group['max'] : '—' }}</span>
                    <span class="wt-meter wt-{{ $tone($group) }}" style="--v: {{ $percent($group) }}%"><i></i></span>
                </a>
            @endforeach
        </div>

        <p class="wt-summary">
            @foreach (['pass', 'warn', 'fail', 'skip'] as $status)
                <span class="wt-dot wt-{{ $status }}">{{ $core('status.'.$status) }}: {{ $counts[$status] ?? 0 }}</span>
            @endforeach
        </p>

        <div class="wt-foot">
            <span class="wt-url">{{ $report['url'] }}</span>
            <small>{{ \Illuminate\Support\Carbon::parse($report['checkedAt'])->locale($locale)->isoFormat('LLL') }} · {{ $core('report.ruleset', ['ruleset' => $report['ruleset']]) }}</small>
            <button type="button" class="wt-btn" @click="$dispatch('wt-again')">{{ $core('report.again') }}</button>
        </div>
        <p class="wt-disclaimer">{{ $core('report.disclaimer') }}</p>
    </div>

    @foreach ($report['groups'] as $i => $group)
        <section class="wt-group" id="wt-group-{{ $i + 1 }}" aria-labelledby="wt-group-title-{{ $i + 1 }}">
            <header class="wt-group-head">
                <h3 id="wt-group-title-{{ $i + 1 }}"><span class="wt-gn">{{ $i + 1 }}</span>{{ $own('groups.'.$group['code'].'.title') }}</h3>
                <p>{{ $own('groups.'.$group['code'].'.text') }}</p>
                <b>{{ $group['max'] > 0 ? $group['points'].'/'.$group['max'] : $core('status.skip') }}</b>
            </header>

            @foreach ($group['checks'] as $check)
                @php($key = 'checks.'.$check['code'])
                @php($number++)
                <details class="wt-check wt-{{ $check['status'] }}">
                    <summary>
                        <span class="wt-num">{{ sprintf('%02d', $number) }}</span>
                        <span class="wt-check-title">
                            <b>{{ $own($key.'.title') }}</b>
                            <small>{{ $own($key.'.text') }}</small>
                        </span>
                        <span class="wt-dot">{{ $core('status.'.$check['status']) }}</span>
                        <span class="wt-points">{{ $check['status'] === 'skip' ? '—' : $check['points'].'/'.$check['max'] }}</span>
                    </summary>

                    <div class="wt-check-body">
                        @if ($check['note'])
                            <p>{{ $own($key.'.notes.'.$check['note'], $check['replace']) }}</p>
                        @endif

                        @if ($check['evidence'])
                            <p class="wt-label">{{ $core('report.found') }}</p>
                            <ul class="wt-evidence">
                                @foreach ($check['evidence'] as $item)
                                    <li><code>{{ $item }}</code></li>
                                @endforeach
                            </ul>
                        @endif

                        @if (in_array($check['status'], ['warn', 'fail'], true))
                            <p class="wt-label">{{ $core('report.fix') }}</p>
                            <p>{{ $own($key.'.fix') }}</p>

                            @if ($check['snippet'])
                                <div class="wt-snippet">
                                    <button type="button" class="wt-btn wt-copy" @click="copy(@js($check['snippet']), @js($check['code']))"
                                            x-text="copied === @js($check['code']) ? @js($core('report.copied')) : @js($core('report.copy'))">{{ $core('report.copy') }}</button>
                                    <pre><code>{{ $check['snippet'] }}</code></pre>
                                </div>
                            @endif
                        @endif
                    </div>
                </details>
            @endforeach
        </section>
    @endforeach
</div>
