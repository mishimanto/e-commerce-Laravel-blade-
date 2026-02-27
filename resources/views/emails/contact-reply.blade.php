<!DOCTYPE html>
<html>
<head>
    <title>Reply to your message</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #007bff;">Hello {{ $name }},</h2>
        
        <p>Thank you for contacting us. Here is our response to your message:</p>
        
        <div style="background-color: #f9f9f9; padding: 20px; margin: 20px 0;">
            <!-- <p style="margin: 0;"><strong>Our Reply:</strong></p> -->
            <p style="margin-top: 10px;">{{ $replyMessage }}</p>
        </div>
        
        <p>If you have any further questions, please don't hesitate to contact us again.</p>
        
        <p>Best regards,<br>
        <strong>{{ config('app.name') }} Team</strong></p>
        
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        
        <p style="font-size: 12px; color: #999; text-align: center;">
            This is a reply to your message: "{{ $originalSubject }}"
        </p>
    </div>
</body>
</html>