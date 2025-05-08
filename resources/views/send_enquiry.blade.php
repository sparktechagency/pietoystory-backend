<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Enquiry Received</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            color: #333;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 10px;
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
            font-size: 22px;
        }

        .email-box {
            padding: 30px;
        }

        .section-title {
            font-size: 16px;
            color: #10b981;
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

        .footer {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin: 30px 0 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h2>📩 New Enquiry Received</h2>
        </div>

        <div class="email-box">
            <div class="section-title">👤 Enquiry Details</div>
            <table>
                <tr class="item">
                    <td><strong>Full Name:</strong></td>
                    <td>{{ $send_enquiry['full_name'] }}</td>
                </tr>
                <tr class="item">
                    <td><strong>Email:</strong></td>
                    <td>{{ $send_enquiry['email'] }}</td>
                </tr>
                <tr class="item">
                    <td><strong>Message:</strong></td>
                    <td>{{ $send_enquiry['enquiry'] }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            This is an automated message. Please do not reply.
        </div>
    </div>

</body>

</html>
