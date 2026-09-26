<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>سجل العوائل</title>
        <style>
            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #EBEDE3;
                color: #0B1849;
                font-family: Tahoma, "Segoe UI", sans-serif;
            }
            .card {
                max-width: 32rem;
                margin: 1.5rem;
                padding: 2rem;
                border: 2px solid #E4B028;
                background: #fff;
                text-align: center;
            }
            h1 {
                margin: 0 0 1rem;
                font-size: 1.4rem;
            }
            p {
                margin: 0;
                line-height: 1.8;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <h1>تعذر تشغيل البرنامج</h1>
            <p>{{ $message }}</p>
        </div>
    </body>
</html>
