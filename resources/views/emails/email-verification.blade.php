<!DOCTYPE html>
<html>
<head>
	<title>How To Send Mail Using Queue In Laravel 10 - Techsolutionstuff</title>
</head>
<body>
   
  
<p>Hey {{$mailData['user']}}</p>

<p>Welcome to Great Crystal! We’re excited to have you on board. To complete your account setup, please use the verification code below:</p>  

<p>Verification Code: <b>{{$mailData['otp']}}</b></p>

<p>Please enter this code in the verification section of your account settings to activate your account. If you didn’t sign up for Great Crystal, you can ignore this email.
</p>

<p>Thank you for choosing Great Crystal!</p>

<p>Best Regards, Admin Great Crystal</p>

</body>
</html>