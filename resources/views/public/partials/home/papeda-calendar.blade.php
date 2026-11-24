@php
    $month = $calendar['month'];
    $activityDates = [];
    foreach ($calendar['events'] ?? [] as $event) {
        for ($day = (int) substr($event['date'], -2); $day <= (int) substr($event['end_date'], -2); $day++) {
            $activityDates[$month->day($day)->toDateString()] = true;
        }
    }
    $monthDays = array_merge(array_fill(0, $month->dayOfWeekIso - 1, null), range(1, $month->daysInMonth));
    $weeks = array_chunk(array_pad($monthDays, (int) (ceil(count($monthDays) / 7) * 7), null), 7);
@endphp

<nav class="home-calendar-nav" aria-label="Pilih bulan kalender">
    @if($calendar['previous'])
        <a href="{{ route('home', ['bulan' => $calendar['previous']]) }}#kalender-kegiatan" aria-label="Bulan sebelumnya">
            <i class="fas fa-chevron-left" aria-hidden="true"></i>
        </a>
    @else
        <span></span>
    @endif
    <h3>{{ $month->locale('id')->translatedFormat('F Y') }}</h3>
    @if($calendar['next'])
        <a href="{{ route('home', ['bulan' => $calendar['next']]) }}#kalender-kegiatan" aria-label="Bulan berikutnya">
            <i class="fas fa-chevron-right" aria-hidden="true"></i>
        </a>
    @else
        <span></span>
    @endif
</nav>

<table class="home-calendar-grid" aria-label="Kalender kegiatan {{ $month->locale('id')->translatedFormat('F Y') }}">
    <thead><tr>
        @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $weekday)
            <th scope="col">{{ $weekday }}</th>
        @endforeach
    </tr></thead>
    <tbody>
        @foreach($weeks as $week)
            <tr>
                @foreach($week as $day)
                    <td>
                        @if($day !== null)
                            @php($date = $month->day($day)->toDateString())
                            <time datetime="{{ $date }}" @class(['has-activity' => isset($activityDates[$date])])
                                @if(isset($activityDates[$date])) aria-label="{{ $day }}, ada kegiatan" @endif
                                @if($date === $calendar['today']->toDateString()) aria-current="date" @endif>
                                {{ $day }}
                            </time>
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

@if($calendar['events'] === null)
    <p class="home-calendar-note">Jadwal dari Papeda sedang tidak tersedia.</p>
@elseif($calendar['events'] === [])
    <p class="home-calendar-note">Belum ada kegiatan publik pada bulan ini.</p>
@elseif($calendar['events'])
    <ul class="home-calendar-events" tabindex="0" aria-label="Daftar kegiatan publik">
        @foreach($calendar['events'] as $event)
            <li>
                <time datetime="{{ $event['date'] }}">{{ $month->day((int) substr($event['date'], -2))->locale('id')->translatedFormat('d M') }}</time>
                <div>
                    <strong>{{ $event['title'] }}</strong>
                    @if($event['end_date'] !== $event['date'])
                        <small>Sampai {{ $month->day((int) substr($event['end_date'], -2))->locale('id')->translatedFormat('d M') }}</small>
                    @endif
                    @if($event['time'])<small>{{ $event['time'] }} WIT</small>@endif
                </div>
            </li>
        @endforeach
    </ul>
@endif
@if($calendar['events'] !== null)
    <p class="home-calendar-note">Tersingkronisasi dengan aplikasi inovasi PAPEDA PTA Papua Barat.</p>
@endif
