<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Epic Movie Quotes</title>
</head>
<body style="background: #191725; font-family: Helvetica, sans-serif">
    <div style="width:6.5rem ; margin: auto; padding-top: 6rem; margin-bottom: 6rem">
        <div style="width: 2rem; margin:auto">
            <img style="width: 2rem" src="{{ $message->embed(public_path() . '/images/quoteLogo.png')}}" alt="quote">
        </div>
        <h2 style="color:white; font-size: 0.8rem; color: #DDCCAA">MOVIE QUOTES</h2>
    </div>
   <div style="margin-left: 12rem">
     <p style="color: white; font-size: 1rem; ">Hola {{$username}}!</p>
     <p style="color: white; font-size: 1rem; margin-bottom: 2rem; margin-top: 2rem " >Thanks for joining Movie quotes! We really appreciate it. Please click the button below to verify your account:</p>
     <a href="{{$url}}" style="padding: 0.6rem;  background: #E31221; text-decoration: none; color: white; border-radius: 0.5rem">Verify account</a>
     <p style="color: white; font-size: 1rem; margin-top: 2rem; margin-bottom: 2rem">If clicking doesn't work, you can try copying and pasting it to your browser:</p>
     <a href="" style="color:#DDCCAA; text-decoration: none">{{$url}}</a>
     <p style="color: white; font-size: 1rem; margin-top: 2rem; margin-bottom: 2rem">If you have any problems, please contact us: support@moviequotes.ge</p>
     <p style="color: white; font-size: 1rem; padding-bottom: 7rem">MovieQuotes Crew</p>
   </div>
</body>
</html>
