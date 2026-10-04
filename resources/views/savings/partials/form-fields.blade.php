@php
    /** @var \App\Models\Saving|null $saving */
    $saving = $saving ?? null;
    $extended = $extended ?? false;
    $selectedBank = old('bank_name', $saving?->bank_name);
@endphp

<div class="fields">
    <div>
        <label for="type">Type</label>
        <select id="type" name="type" required>
            @foreach (\App\SavingType::cases() as $type)
                <option value="{{ $type->value }}" @selected(old('type', $saving?->type?->value ?? 'savings_account') === $type->value)>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        @error('type') <div class="error">{{ $message }}</div> @enderror
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
        <label for="account_number">Account number</label>
        <input
            id="account_number"
            name="account_number"
            type="text"
            inputmode="numeric"
            pattern="[0-9]+"
            maxlength="40"
            value="{{ old('account_number', $saving?->account_number) }}"
            required
        >
        @error('account_number') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="interest_rate">Interest rate (%)</label>
        <input
            id="interest_rate"
            name="interest_rate"
            type="number"
            min="1"
            max="20"
            step="0.01"
            value="{{ old('interest_rate', $saving?->interest_rate) }}"
            required
        >
        @error('interest_rate') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="amount">Amount</label>
        <input
            id="amount"
            name="amount"
            type="number"
            inputmode="decimal"
            min="0.01"
            step="0.01"
            value="{{ old('amount', $saving?->amount) }}"
            required
        >
        @error('amount') <div class="error">{{ $message }}</div> @enderror
    </div>

    <div>
        <label for="start_date">Start date</label>
        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $saving?->start_date?->toDateString()) }}" required>
        @error('start_date') <div class="error">{{ $message }}</div> @enderror
    </div>

    @if ($extended)
        <div>
            <label for="maturity_date">Maturity date</label>
            <input id="maturity_date" name="maturity_date" type="date" value="{{ old('maturity_date', $saving?->maturity_date?->toDateString()) }}" required>
            @error('maturity_date') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="interest_earned">Interest earned at maturity</label>
            <input id="interest_earned" name="interest_earned" type="number" min="0" step="0.01" value="{{ old('interest_earned', $saving?->interest_earned) }}" required>
            @error('interest_earned') <div class="error">{{ $message }}</div> @enderror
        </div>
    @endif
</div>
