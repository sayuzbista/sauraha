<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vendor Registration Rejected</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>Hello {{ $vendor->owner_name }},</h2>

    <p>
        We are sorry to inform you that your vendor registration for 
        <strong>{{ $vendor->company_name }}</strong> has been <strong>rejected</strong>.
    </p>

    <p>
        Please check the details you provided and try to register again.
        If you have questions, you can contact us at <strong>+977 9861792189</strong>.
    </p>

    <br>
    <p>Regards,<br>
    <strong>Ghumau Sauraha Team</strong></p>
</body>
</html>
