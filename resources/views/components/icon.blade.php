@props(['name'])

<svg {{ $attributes->merge(['class' => 'icon']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('wallet')
            <path d="M3 7.5A2.5 2.5 0 0 1 5.5 5h13A2.5 2.5 0 0 1 21 7.5V18a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
            <path d="M3 10h18" />
            <path d="M16.5 14.5h.01" />
            @break
        @case('income')
            <path d="M12 19V5" />
            <path d="m6 11 6-6 6 6" />
            @break
        @case('expense')
            <path d="M12 5v14" />
            <path d="m6 13 6 6 6-6" />
            @break
        @case('receipt')
            <path d="M6 3h12v18l-3-1.5L12 21l-3-1.5L6 21z" />
            <path d="M9 8h6" />
            <path d="M9 12h6" />
            @break
        @case('piggy')
            <path d="M19 5c-1.5 0-2.8 1.4-3 2-3.5-1.5-11-.3-11 5 0 1.8 0 3 2 4.5V20h4v-2h3v2h4v-4c1-.5 1.7-1 2-2h2v-4h-2c0-1-.5-1.5-1-2" />
            <path d="M2 9v1c0 1.1.9 2 2 2h1" />
            <path d="M16 11h.01" />
            @break
        @case('landmark')
            <path d="M4 10h16" />
            <path d="M12 3 4 10h16z" />
            <path d="M6 10v7" />
            <path d="M10 10v7" />
            <path d="M14 10v7" />
            <path d="M18 10v7" />
            <path d="M3 17h18" />
            @break
        @case('repeat')
            <path d="M17 3v4h4" />
            <path d="M3 12a8 8 0 0 1 14-5l4 0" />
            <path d="M7 21v-4H3" />
            <path d="M21 12a8 8 0 0 1-14 5H3" />
            @break
        @case('logout')
            <path d="M10 7V5a2 2 0 0 1 2-2h7v18h-7a2 2 0 0 1-2-2v-2" />
            <path d="M15 12H3" />
            <path d="m6 9-3 3 3 3" />
            @break
        @case('trash')
            <path d="M4 7h16" />
            <path d="M9 7V4h6v3" />
            <path d="M7 7l1 13h8l1-13" />
            @break
        @case('plus')
            <path d="M12 5v14" />
            <path d="M5 12h14" />
            @break
        @case('calendar')
            <rect x="4" y="5" width="16" height="15" rx="2" />
            <path d="M4 10h16" />
            <path d="M8 3v4" />
            <path d="M16 3v4" />
            @break
        @case('percent')
            <circle cx="7.5" cy="7.5" r="2.25" />
            <circle cx="16.5" cy="16.5" r="2.25" />
            <path d="m18 6-12 12" />
            @break
        @case('pie')
            <path d="M12 3a9 9 0 1 0 9 9h-9z" />
            <path d="M12 3v9h9" />
            <path d="M20.5 8.5A9 9 0 0 1 15 20.2" />
            @break
        @case('coins')
            <ellipse cx="12" cy="6" rx="7" ry="2.5" />
            <path d="M5 6v5c0 1.4 3.1 2.5 7 2.5s7-1.1 7-2.5V6" />
            <path d="M5 11v5c0 1.4 3.1 2.5 7 2.5s7-1.1 7-2.5v-5" />
            @break
        @case('user')
            <circle cx="12" cy="8" r="3.25" />
            <path d="M5 19.5a7 7 0 0 1 14 0" />
            @break
        @default
    @endswitch
</svg>
