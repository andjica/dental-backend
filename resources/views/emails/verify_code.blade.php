<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <title>Your Verification Code</title>
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- preheader -->
  <style>
    .preheader{display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;overflow:hidden;mso-hide:all;}
    @media (prefers-color-scheme: dark) {
      .card { background:#111827 !important; }
      .text { color:#e5e7eb !important; }
      .muted { color:#9ca3af !important; }
      .code-box { background:#0b1220 !important; border-color:#1f2937 !important; color:#f9fafb !important; }
      .button { background:#2563eb !important; border-color:#2563eb !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;">
  <div class="preheader">
    Your verification code is {{ $code }}. It expires in 3 minutes.
  </div>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fb;padding:24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:580px;margin:0 auto;">
          <!-- Header -->
          <tr>
            <td align="center" style="padding:12px 24px;">
              <img src="LOGO_URL" alt="Company" width="120" style="display:block;height:auto;border:0;outline:none;text-decoration:none;">
            </td>
          </tr>

          <!-- Card -->
          <tr>
            <td class="card" style="background:#ffffff;border-radius:14px;padding:28px 24px;border:1px solid #e5e7eb;">
              <h1 class="text" style="margin:0 0 8px;font-size:22px;line-height:1.3;color:#0f172a;font-weight:700;">
                Hi {{ $name ?? 'there' }},
              </h1>
              <p class="text" style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
                Use the code below to verify your email and continue.
              </p>

              <!-- Code -->
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:18px 0 8px;">
                <tr>
                  <td align="center">
                    <div class="code-box" style="display:inline-block;background:#0b5cff0d;border:1px solid #c7d2fe;border-radius:12px;padding:16px 22px;">
                      <span style="font-size:28px;letter-spacing:10px;font-weight:800;color:#0b5cff;font-family:SFMono-Regular,Consolas,'Liberation Mono',Menlo,monospace;display:inline-block;">
                        {{ implode(' ', str_split($code)) }}
                      </span>
                    </div>
                  </td>
                </tr>
              </table>

              <p class="muted" style="margin:0 0 16px;font-size:13px;line-height:1.6;color:#64748b;">
                This code expires in <strong>3 minutes</strong>. If it expires, you can request a new one from the app.
              </p>

              <!-- Help -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;">
                <tr>
                  <td style="padding-top:8px;border-top:1px solid #e5e7eb;">
                    <p class="muted" style="margin:12px 0 0;font-size:12px;line-height:1.6;color:#94a3b8;">
                      Didn’t request this? You can safely ignore this message.  
                      For help, contact us at <a href="mailto:SUPPORT_EMAIL" style="color:#2563eb;text-decoration:none;">SUPPORT_EMAIL</a>.
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td align="center" style="padding:18px 8px;">
              <p class="muted" style="margin:0;font-size:12px;line-height:1.6;color:#94a3b8;">
                © {{ date('Y') }} Company. All rights reserved.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
