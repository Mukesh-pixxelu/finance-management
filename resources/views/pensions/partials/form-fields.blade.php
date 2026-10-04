@php
    /** @var \App\Models\Pension|null $pension */
    $pension = $pension ?? null;
    $selectedBank = old('bank_name', $pension?->bank_name);
@endphp

<div class="fields">
    <div>
        <label for="scheme">Scheme</label>
        <select id="scheme" name="scheme" required>
            @foreach (\App\PensionScheme::cases() as $scheme)
                <option value="{{ $scheme->value }}" @selected(old('scheme', $pension?->scheme?->value ?? 'apy') === $scheme->value)>
                    {{ $scheme->label() }}
                </option>
            @endforeach
        </select>
        @error('scheme') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="bank_name">Bank / Post office</label>
        <select id="bank_name" name="bank_name" required>
            <option value="" disabled @selected($selectedBank === null || $selectedBank === '')>Select bank</option>
            @foreach (\App\Bank::options() as $bank)
                <option value="{{ $bank }}" @selected($selectedBank === $bank)>{{ $bank }}</option>
            @endforeach
            @if ($selectedBank && ! in_array($selectedBank, \App\Bank::options(), true))
                <option value="{{ $selectedBank }}" selected>{{ $selectedBank }}</option>
            @endif
        </select>
        @error('bank_name') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="pran">PRAN / Account number</label>
        <input
            id="pran"
            name="pran"
            type="text"
            inputmode="numeric"
            pattern="[0-9]+"
            maxlength="40"
            value="{{ old('pran', $pension?->pran) }}"
            required
        >
        @error('pran') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="contribution_amount">Contribution amount</label>
        <input
            id="contribution_amount"
            name="contribution_amount"
            type="number"
            inputmode="decimal"
            min="0.01"
            step="0.01"
            value="{{ old('contribution_amount', $pension?->contribution_amount) }}"
            required
        >
        @error('contribution_amount') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="contribution_frequency">Frequency</label>
        <select id="contribution_frequency" name="contribution_frequency" required>
            @foreach (\App\ContributionFrequency::cases() as $frequency)
                <option value="{{ $frequency->value }}" @selected(old('contribution_frequency', $pension?->contribution_frequency?->value ?? 'monthly') === $frequency->value)>
                    {{ $frequency->label() }}
                </option>
            @endforeach
        </select>
        @error('contribution_frequency') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="start_date">Contribution start date</label>
        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $pension?->start_date?->toDateString()) }}" required>
        @error('start_date') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="pension_start_date">Pension start date <span class="muted">(when payout begins)</span></label>
        <input
            id="pension_start_date"
            name="pension_start_date"
            type="date"
            value="{{ old('pension_start_date', $pension?->pension_start_date?->toDateString()) }}"
        >
        @error('pension_start_date') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="expected_pension">Expected pension <span class="muted">(monthly, optional)</span></label>
        <input
            id="expected_pension"
            name="expected_pension"
            type="number"
            inputmode="decimal"
            min="0"
            step="0.01"
            value="{{ old('expected_pension', $pension?->expected_pension) }}"
        >
        @error('expected_pension') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="expected_corpus">Expected corpus <span class="muted">(optional)</span></label>
        <input
            id="expected_corpus"
            name="expected_corpus"
            type="number"
            inputmode="decimal"
            min="0"
            step="0.01"
            value="{{ old('expected_corpus', $pension?->expected_corpus) }}"
        >
        @error('expected_corpus') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="wide">
        <label for="notes">Notes <span class="muted">(optional)</span></label>
        <input
            id="notes"
            name="notes"
            type="text"
            maxlength="1000"
            value="{{ old('notes', $pension?->notes) }}"
            placeholder="Nominee, branch, or reminder"
        >
        @error('notes') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>
