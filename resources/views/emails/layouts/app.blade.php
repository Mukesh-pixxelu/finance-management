<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $title ?? config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fb;color:#0f172a;font-family:'Manrope','Segoe UI',Arial,sans-serif;-webkit-text-size-adjust:100%;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f4f7fb;padding:28px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background:#ffffff;border:1px solid #e6edf5;border-radius:16px;overflow:hidden;box-shadow:0 10px 28px rgba(15,23,42,0.05);">
                    <tr>
                        <td style="background:#1d70e7;padding:18px 24px;">
                            <p style="margin:0;font-size:18px;font-weight:800;letter-spacing:-0.02em;color:#ffffff;">
                                Ledger
                            </p>
                            <p style="margin:4px 0 0;font-size:13px;color:#dceaff;">
                                Financial Management
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 24px 8px;">
                            {{ $slot }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 24px 24px;">
                            <p style="margin:0;font-size:12px;line-height:1.5;color:#64748b;">
                                &copy; {{ now()->year }}
                                <a href="https://craftoweb.com" style="color:#1d70e7;text-decoration:none;font-weight:700;">craftoweb.com</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
