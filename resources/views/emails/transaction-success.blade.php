<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Receipt - Wildtrack Adventure</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background-color: #f4f4f0;
      color: #2d2d2d;
      -webkit-font-smoothing: antialiased;
    }

    .wrapper {
      max-width: 640px;
      margin: 32px auto;
      background: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    }

    /* ── HEADER ── */
    .header {
      background: linear-gradient(135deg, #1a3a2a 0%, #2d5a3d 100%);
      padding: 36px 40px 32px;
      position: relative;
      overflow: hidden;
    }
    .header::after {
      content: '';
      position: absolute;
      bottom: -30px; right: -30px;
      width: 140px; height: 140px;
      border-radius: 50%;
      background: rgba(255,255,255,0.04);
    }

    .logo-area {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 24px;
    }
    .logo-icon {
      width: 52px; height: 52px;
      background: #4a9e6b;
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .logo-icon svg { display: block; }
    .logo-text-wrap { line-height: 1.2; }
    .logo-name {
      font-size: 20px;
      font-weight: 700;
      color: #ffffff;
      letter-spacing: 0.5px;
    }
    .logo-tagline {
      font-size: 11px;
      color: #86c89a;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      margin-top: 2px;
    }

    .header-title {
      font-size: 13px;
      color: #86c89a;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      margin-bottom: 6px;
    }
    .header-invoice-no {
      font-size: 28px;
      font-weight: 700;
      color: #ffffff;
    }

    /* ── STATUS BADGE ── */
    .status-bar {
      background: #f0fdf4;
      border-left: 4px solid #4a9e6b;
      padding: 12px 40px;
      font-size: 13px;
      color: #166534;
      font-weight: 500;
    }

    /* ── BODY ── */
    .body { padding: 36px 40px; }

    /* Info grid */
    .info-grid {
      display: table;
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 28px;
    }
    .info-row { display: table-row; }
    .info-label, .info-value {
      display: table-cell;
      padding: 7px 0;
      font-size: 14px;
      border-bottom: 1px solid #f0f0ec;
      vertical-align: middle;
    }
    .info-label {
      color: #8a8a7a;
      width: 45%;
      font-weight: 500;
    }
    .info-value {
      color: #1f1f1f;
      font-weight: 600;
    }
    .info-value.amount {
      color: #1a3a2a;
      font-size: 16px;
    }
    .info-value.due-date { color: #c0392b; }

    /* Section title */
    .section-title {
      font-size: 11px;
      font-weight: 700;
      color: #8a8a7a;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      margin-bottom: 12px;
    }

    /* Invoice table */
    .invoice-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 0;
      font-size: 14px;
    }
    .invoice-table thead tr {
      background: #1a3a2a;
    }
    .invoice-table thead th {
      color: #86c89a;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      padding: 11px 16px;
      text-align: left;
    }
    .invoice-table thead th:last-child { text-align: right; }

    .invoice-table tbody tr { border-bottom: 1px solid #f0f0ec; }
    .invoice-table tbody tr:last-child { border-bottom: none; }
    .invoice-table tbody td {
      padding: 13px 16px;
      color: #2d2d2d;
      vertical-align: top;
    }
    .invoice-table tbody td:last-child {
      text-align: right;
      font-weight: 600;
      color: #1a3a2a;
    }
    .item-name { font-weight: 600; color: #1a1a1a; }
    .item-sub  { font-size: 12px; color: #8a8a7a; margin-top: 2px; }

    /* Totals block */
    .totals-block {
      background: #f9f9f6;
      border-radius: 10px;
      overflow: hidden;
      margin-top: 0;
    }
    .totals-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 11px 16px;
      font-size: 14px;
      border-bottom: 1px solid #eeeee8;
    }
    .totals-row:last-child { border-bottom: none; }
    .totals-row.total-final {
      background: #1a3a2a;
      padding: 14px 16px;
    }
    .totals-row.total-final span { color: #ffffff; font-weight: 700; font-size: 15px; }
    .totals-label { color: #6b6b5a; }
    .totals-value { font-weight: 600; color: #1a1a1a; }

    /* Payment note */
    .payment-note {
      background: #fffbeb;
      border: 1px solid #fde68a;
      border-radius: 10px;
      padding: 14px 18px;
      margin-top: 24px;
      font-size: 13px;
      color: #92400e;
      line-height: 1.6;
    }
    .payment-note strong { display: block; margin-bottom: 4px; color: #78350f; }

    /* CTA Button */
    .cta-wrap { text-align: center; margin-top: 28px; }
    .cta-btn {
      display: inline-block;
      background: #2d5a3d;
      color: #ffffff !important;
      text-decoration: none;
      font-size: 15px;
      font-weight: 700;
      padding: 14px 40px;
      border-radius: 10px;
      letter-spacing: 0.3px;
    }

    /* ── FOOTER ── */
    .footer {
      background: #1a3a2a;
      padding: 28px 40px;
      text-align: center;
    }
    .footer-brand {
      font-size: 16px;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 4px;
    }
    .footer-tagline { font-size: 12px; color: #86c89a; margin-bottom: 16px; }
    .footer-divider {
      width: 40px; height: 1px;
      background: #2d5a3d;
      margin: 14px auto;
    }
    .footer-copy { font-size: 11px; color: #4a7a5a; line-height: 1.7; }
    .footer-copy a { color: #86c89a; text-decoration: none; }

    @media (max-width: 480px) {
      .wrapper { margin: 0; border-radius: 0; }
      .header, .body, .footer { padding-left: 20px; padding-right: 20px; }
      .status-bar { padding-left: 20px; padding-right: 20px; }
    }
  </style>
</head>
<body>
<div class="wrapper">

  <!-- HEADER -->
  <div class="header">
    <div class="logo-area">
      <div class="logo-text-wrap">
        <div class="logo-name">Wildtrack Adventure</div>
        <div class="logo-tagline">Explore · Discover · Conquer</div>
      </div>
    </div>
    <div class="header-title">Receipt</div>
    <div class="header-invoice-no">#{{ $orderId }}</div>
  </div>

  <!-- STATUS BAR -->
  <div class="status-bar">
    ✅ &nbsp;Pembayaran dikonfirmasi — Terima kasih atas kepercayaan Anda!
  </div>

  <!-- BODY -->
  <div class="body">

    <!-- Info Grid -->
    <p class="section-title">Receipt Information</p>
    <div class="info-grid">
      <div class="info-row">
        <div class="info-label">Name</div>
        <div class="info-value">{{ $name }}</div>
      </div>
      <div class="info-row">
        <div class="info-label">Payment Method</div>
        <div class="info-value">{{ $paymentMethod ?? '-' }}</div>
      </div>
      <div class="info-row">
        <div class="info-label">Booking Code</div>
        <div class="info-value">#{{ $orderId }}</div>
      </div>
      <div class="info-row">
        <div class="info-label">Amount Due</div>
        <div class="info-value amount">Rp {{ number_format($total, 0, ',', '.') }}</div>
      </div>
      <div class="info-row">
        <div class="info-label">Due Date</div>
        <div class="info-value due-date">{{ $dueDate ?? \Carbon\Carbon::now()->addDay()->format('d M Y') }}</div>
      </div>
    </div>

    <!-- Invoice Items Table -->
    <p class="section-title" style="margin-top:28px;">Receipt Items</p>
    <table class="invoice-table">
      <thead>
        <tr>
          <th>Description</th>
          <th>Qty</th>
          <th>Price</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <div class="item-name">{{ $paket ?? 'Paket Petualangan' }}</div>
            <div class="item-sub">Program Wildtrack Adventure</div>
          </td>
          <td>1</td>
          <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-block">
      <div class="totals-row">
        <span class="totals-label">Package Type: </span>
        <span class="totals-value">{{ $paket ?? '-' }}</span>
      </div>
      <div class="totals-row">
        <span class="totals-label">Camp Members: </span>
        <span class="totals-value">{{ $campMembers ?? 1 }} orang</span>
      </div>
      <div class="totals-row">
        <span class="totals-label">Sub Total: </span>
        <span class="totals-value">Rp {{ number_format($subTotal ?? $total, 0, ',', '.') }}</span>
      </div>
      <div class="totals-row total-final">
        <span>Total</span>
        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
      </div>
    </div>

    <!-- Payment Note -->
    <div class="payment-note" style="background:#f0fdf4; border-color:#86efac; color:#166534;">
      <strong>✅ Pembayaran Berhasil</strong>
      Pembayaran Anda telah diterima. Terima kasih atas kepercayaan Anda!
    </div>

  </div>

  <!-- FOOTER -->
  <div class="footer">
    <div class="footer-brand">Wildtrack Adventure</div>
    <div class="footer-tagline">Explore · Discover · Conquer</div>
    <div class="footer-divider"></div>
    <div class="footer-copy">
      Email ini dikirim secara otomatis, mohon tidak membalas email ini.<br>
      Butuh bantuan? Hubungi kami di <a href="mailto:wildtrackadventure@gmail.com">wildtrackadventure@gmail.com</a><br><br>
      &copy; {{ date('Y') }} Wildtrack Adventure. All rights reserved.
    </div>
  </div>

</div>
</body>
</html>
