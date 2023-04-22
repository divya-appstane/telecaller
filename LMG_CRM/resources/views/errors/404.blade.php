<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $title ?? '404 - Page not found' }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{env('USER_ASSETS')}}images/logo/favicon.ico">

    <!-- page css -->

    <!-- Core css -->
    <link href="{{env('USER_ASSETS')}}css/app.min.css" rel="stylesheet">

</head>

<body>
    <div class="auth-full-height d-flex flex-row align-items-center">
        <div class="container">
           <div class="d-flex justify-content-center mx-auto" style="max-width: 360px;">
               <img class="img-fluid" src="{{env('USER_ASSETS')}}images/others/img-2.png" alt="" />
           </div> 
           <div class="text-center mt-5">
               <h1 class=" fw-bolder mb-3">Opss... PAGE NOT FOUND</h1>
               <p class="mb-4 font-size-lg">The page you're looking for isn't exist. We suggest you back to home.</p>
               <a href="javascript:window.history.back();" class="btn btn-info">Back to home</a>
           </div>
        </div>
    </div>   
    
    <!-- Core Vendors JS -->
    <script src="{{env('USER_ASSETS')}}js/vendors.min.js"></script>

    <!-- page js -->

    <!-- Core JS -->
    <script src="{{env('USER_ASSETS')}}js/app.min.js"></script>

</body>

</html>