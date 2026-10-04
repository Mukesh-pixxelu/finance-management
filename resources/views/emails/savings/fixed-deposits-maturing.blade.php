@component('emails.layouts.app', ['title' => 'FD maturity reminder'])
    <h1 style="margin:0 0 8px;font-size:24px;line-height:1.25;letter-spacing:-0.03em;font-weight:800;color:#0f172a;">
        FD maturity reminder
    </h1>
    <p style="margin:0 0 20px;font-size:15px;line-height:1.55;color:#64748b;">
        Hello {{ $user->name }}, you have
        <strong style="color:#0f172a;">{{ $savings->count() }}</strong>
        fixed {{ Str::plural('deposit', $savings->count()) }} maturing in
        <strong style="color:#1d70e7;">{{ $monthLabel }}</strong>.
    </p>

    @foreach ($savings as $saving)
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 12px;background:#f8fbff;border:1px solid #e6edf5;border-radius:14px;">
            <tr>
                <td style="padding:14px 16px;">
                    <p style="margin:0 0 4px;font-size:11px;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;color:#1d70e7;">
                        Fixed deposit
                    </p>
                    <p style="margin:0 0 2px;font-size:17px;font-weight:800;letter-spacing:-0.02em;color:#0f172a;">
                        {{ $saving->bank_name ?: 'Bank' }}
                    </p>
                    <p style="margin:0 0 12px;font-size:13px;color:#64748b;">
                        A/C {{ $saving->account_number }}
                    </p>

                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="50%" style="padding:0 8px 0 0;vertical-align:top;">
                                <p style="margin:0 0 2px;font-size:12px;color:#64748b;">Matures on</p>
                                <p style="margin:0;font-size:14px;font-weight:700;color:#0f172a;">
                                    {{ $saving->maturity_date?->format('d M Y') ?? '—' }}
                                </p>
                            </td>
                            <td width="50%" style="padding:0;vertical-align:top;">
                                <p style="margin:0 0 2px;font-size:12px;color:#64748b;">Principal</p>
                                <p style="margin:0;font-size:14px;font-weight:700;color:#0f172a;">
                                    {{ \App\Support\Money::indian($saving->amount) }}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding:12px 0 0;">
                                <p style="margin:0 0 2px;font-size:12px;color:#64748b;">Receivable at maturity</p>
                                <p style="margin:0;font-size:20px;font-weight:800;letter-spacing:-0.03em;color:#1d70e7;">
                                    {{ \App\Support\Money::indian($saving->receivableAtMaturity()) }}
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    @endforeach

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:22px 0 18px;">
        <tr>
            <td style="border-radius:12px;background:#1d70e7;">
                <a href="{{ route('savings.index') }}" style="display:inline-block;padding:12px 20px;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none;">
                    View savings →
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:13px;line-height:1.5;color:#64748b;">
        Thanks,<br>
        <strong style="color:#0f172a;">{{ config('app.name', 'Ledger') }}</strong>
    </p>
@endcomponent
