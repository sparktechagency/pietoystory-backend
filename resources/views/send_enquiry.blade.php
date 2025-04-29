<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Enquiry Received</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f9fc;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .email-header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333333;
        }
        .email-body {
            font-size: 16px;
            color: #555555;
        }
        .email-footer {
            margin-top: 30px;
            font-size: 14px;
            color: #999999;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">New Enquiry Received</div>
        <div class="email-body">
            <p><strong>Full Name:</strong> {{ $send_enquiry['full_name'] }}</p>
            <p><strong>Email:</strong> {{ $send_enquiry['email'] }}</p>
            <p><strong>Enquiry:</strong></p>
            <p>{{ $send_enquiry['enquiry'] }}</p>
        </div>
        <div class="email-footer">
            This is an automated message. Please do not reply.
        </div>
    </div>
</body>
</html>
