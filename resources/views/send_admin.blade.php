<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice ABG-25-45</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            color: #333;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #10b981;
            color: white;
            text-align: center;
            padding: 20px;
        }

        h2 {
            margin: 0;
            margin-bottom: 5px;
        }

        .invoice-box {
            background: #fff;
            padding: 30px;
            border-top: 1px solid #eee;
        }

        .invoice-box h1 {
            font-size: 24px;
            color: #10b981;
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 16px;
            color: #10b981;
            margin-top: 30px;
            margin-bottom: 10px;
            border-bottom: 1px solid #10b981;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            line-height: 1.6;
            border-collapse: collapse;
        }

        td {
            padding: 8px;
            vertical-align: top;
        }

        tr.item td {
            border-bottom: 1px solid #eee;
        }

        tr.total td {
            border-top: 2px solid #10b981;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h2>📋 New Quote & Billing Info</h2>
        </div>

        <div class="invoice-box">
            <div style="text-align:center;">
                <p><strong>Invoice ID:</strong> ABG-25-45</p>
                <p><strong>Date:</strong> {{ date('F j, Y') }}</p>
            </div>

            <!-- Customer Section -->
            <div class="section-title">👤 Customer Information</div>
            <table>
                <tr class="item">
                    <td>Full Name</td>
                    <td>{{ $billing->first_name }} {{ $billing->last_name }}</td>
                </tr>
                <tr class="item">
                    <td>Email</td>
                    <td>{{ $billing->email }}</td>
                </tr>
                <tr class="item">
                    <td>Phone</td>
                    <td>{{ $billing->phone }}</td>
                </tr>
                <tr class="item">
                    <td>Address</td>
                    <td>{{ $billing->address }}</td>
                </tr>
            </table>

            <!-- Service Section -->
            <div class="section-title">🧹 Service Details</div>
            <table>
                <tr class="item">
                    <td>Zip Code</td>
                    <td>{{ $quote->zip_code }}</td>
                </tr>
                <tr class="item">
                    <td>How Often</td>
                    <td>{{ $quote->how_often }}</td>
                </tr>
                <tr class="item">
                    <td>Number of Dogs</td>
                    <td>{{ $quote->amount_of_dogs }}</td>
                </tr>
                <tr class="item">
                    <td>Total Area</td>
                    <td>{{ $quote->total_area }} sq ft</td>
                </tr>
                <tr class="item">
                    <td>Area to Clean</td>
                    <td>{{ $quote->area_to_clean }}</td>
                </tr>
                <tr class="item">
                    <td>Dog's Name</td>
                    <td>{{ $billing->dog_name }}</td>
                </tr>
                <tr class="item">
                    <td>Additional Comments</td>
                    <td>{{ $billing->comments ?? 'None' }}</td>
                </tr>
            </table>

            <!-- Payment Section -->
            <div class="section-title">💳 Payment Information</div>
            <table>
                <tr class="item">
                    <td>Payment Intent ID</td>
                    <td>{{ $quote->payment_intent_id ?? 'N/A' }}</td>
                </tr>
                <tr class="item total">
                    <td>Total Cost</td>
                    <td>${{ number_format($quote->cost, 2) }}</td>
                </tr>
                <tr class="item">
                    <td>Status</td>
                    <td>{{ ucfirst($quote->status) }}</td>
                </tr>
            </table>

            <!-- Footer -->
            <div class="footer">
                This invoice was generated automatically from your system.
            </div>
        </div>
    </div>

</body>

</html>