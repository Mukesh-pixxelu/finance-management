@php
    /** @var \App\Models\Insurance|null $insurance */
    $insurance = $insurance ?? null;
    $selectedProvider = old('provider', $insurance?->provider);
    $selectedBank = old('bank_name', $insurance?->bank_name);
@endphp

<div class="fields">
    <div>
        <label for="type">Type</label>
        <select id="type" name="type" required>
            @foreach (\App\InsuranceType::cases() as $type)
                <option value="{{ $type->value }}" @selected(old('type', $insurance?->type?->value ?? 'health') === $type->value)>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        @error('type') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="provider">Provider / scheme</label>
        <select id="provider" name="provider" required>
            <option value="" disabled @selected($selectedProvider === null || $selectedProvider === '')>Select provider</option>
            @foreach (\App\Insurer::options() as $provider)
                <option value="{{ $provider }}" @selected($selectedProvider === $provider)>{{ $provider }}</option>
            @endforeach
            @if ($selectedProvider && ! in_array($selectedProvider, \App\Insurer::options(), true))
                <option value="{{ $selectedProvider }}" selected>{{ $selectedProvider }}</option>
            @endif
        </select>
        @error('provider') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="bank_name">Bank name</label>
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
        <label for="policy_number">Policy number</label>
        <input
            id="policy_number"
            name="policy_number"
            type="text"
            maxlength="60"
            value="{{ old('policy_number', $insurance?->policy_number) }}"
            required
        >
        @error('policy_number') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="insured_person">Insured person</label>
        <input
            id="insured_person"
            name="insured_person"
            type="text"
            maxlength="120"
            value="{{ old('insured_person', $insurance?->insured_person) }}"
            required
        >
        @error('insured_person') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="sum_assured">Sum assured</label>
        <input
            id="sum_assured"
            name="sum_assured"
            type="number"
            inputmode="decimal"
            min="0.01"
            step="0.01"
            value="{{ old('sum_assured', $insurance?->sum_assured) }}"
            required
        >
        @error('sum_assured') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="premium_amount">Premium amount</label>
        <input
            id="premium_amount"
            name="premium_amount"
            type="number"
            inputmode="decimal"
            min="0.01"
            step="0.01"
            value="{{ old('premium_amount', $insurance?->premium_amount) }}"
            required
        >
        @error('premium_amount') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="premium_frequency">Premium frequency</label>
        <select id="premium_frequency" name="premium_frequency" required>
            @foreach (\App\PremiumFrequency::cases() as $frequency)
                <option value="{{ $frequency->value }}" @selected(old('premium_frequency', $insurance?->premium_frequency?->value ?? 'yearly') === $frequency->value)>
                    {{ $frequency->label() }}
                </option>
            @endforeach
        </select>
        @error('premium_frequency') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="start_date">Start date</label>
        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $insurance?->start_date?->toDateString()) }}" required>
        @error('start_date') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="expiry_date">Expiry / maturity</label>
        <input id="expiry_date" name="expiry_date" type="date" value="{{ old('expiry_date', $insurance?->expiry_date?->toDateString()) }}" required>
        @error('expiry_date') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div class="wide">
        <label for="notes">Notes <span class="muted">(optional)</span></label>
        <input
            id="notes"
            name="notes"
            type="text"
            maxlength="1000"
            value="{{ old('notes', $insurance?->notes) }}"
            placeholder="Nominee, agent, or reminder"
        >
        @error('notes') <div class="error">{{ $message }}</div> @enderror
    </div>
</div>
