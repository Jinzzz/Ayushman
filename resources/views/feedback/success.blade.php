<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback || Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap"
        rel="stylesheet">
</head>
<style>
    .feedback-successful {
        background-color: #f5f3f3;
        height: 100vh;
    }

    .feedback-message-inner {
        background: #fff;
        border-radius: 40px;
        box-shadow: 4px 4px 10px 5px rgba(0, 0, 0, .05);
        padding: 20px;
        width: 50%;
        margin: auto;
    }

    .logo-feedback img {
        height: auto;
        margin: 0 auto;
        width: 190px;
        display: block;
    }

    .feedback-message-inner img {
        margin: auto;
        padding: 10px 0;
        display: block;
    }

    .feedback-message-inner h2 {
        text-align: center;
        font-family: 'Titillium Web', sans-serif;
        font-weight: 700;
        font-size: 36px;
    }

    .feedback-message-inner p {
        color: #666666;
        font-family: 'Titillium Web', sans-serif;
        text-align: center;
        font-weight: 400;
        font-size: 16px;
    }
    /* responsive */
    @media (max-width: 991px) {
        .feedback-message-inner {
        width: 100%;
    } 
    }
    /* responsive : end*/
</style>

<body>
    <div class="feedback-successful">
        <div class="container">
            <div class="logo-feedback" style="padding: 100px 0 30px;">
                <img src="{{asset('assets/images/ayushman-logo.jpeg')}}">
            </div>
            <div class="feedback-message-inner">
                <img src="{{asset('assets/images/download.png')}}">
                <h2>Feedback Submitted!</h2>
                <p>Get the Ayushman patient app on</p>
                <div class="row" style="margin: 20px 0;">
                    <div class="col-6"><img src="{{asset('assets/images/app-ios-01.png')}}"
                        style="float: right;width: 100%;height: auto;max-width: 150px;cursor: pointer;"></div>
                    <div class="col-6"><img src="{{asset('assets/images/app-android-01.png')}}"
                            style="float: left;width: 100%;height: auto;max-width: 150px;cursor: pointer;"></div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>