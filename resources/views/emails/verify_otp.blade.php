<x-mail::message>
# Verify Your Email Address

Hello,

Thank you for registering with Hostoo. Please use the following One Time Password (OTP) to verify your email address and complete your registration.

<div style="text-align: center; margin: 30px 0;">
    <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #DF6951; background: #f8f9fa; padding: 10px 20px; border-radius: 8px; border: 1px dashed #DF6951;">
        {{ $otp }}
    </span>
</div>

This code will expire in 10 minutes. If you did not request this, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
