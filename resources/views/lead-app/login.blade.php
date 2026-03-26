<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
 
 

    <script defer="defer" src="/salesapp/js/app.js"></script>
    <link href="/salesapp/css/app.css" rel="stylesheet">
</head>

<body class="main-bg main-bg-opac main-bg-blur overflow-hidden">
    <div class="pageloader">
        <div class="container h-100">
            <div class="row justify-content-center align-items-center text-center h-100">
                <div class="col-12 mb-auto pt-4"></div>
                <div class="col-auto"> 
                    <p class="h6 mb-0">{{ $setting->company_name }}</p>
                    <p class="h3 mb-4">{{ $setting->company_name }}</p>
                    <div class="loaderplus mb-2"></div>
                </div>
                <div class="col-12 mt-auto pb-4">
                    <p class="text-secondary">Please wait we are preparing awesome things to preview...</p>
                </div>
            </div>
        </div>
    </div>
    <main class="flex-shrink-0 pt-0 h-100">
        <div class="container-fluid">
            <div class="auth-wrapper">
                <div class="d-flex flex-column vh-100 pt-ios">
                    <header class="adminuiux-header">
                        <nav class="navbar"><a class="navbar-brand" href="#">
                                {{-- <div class="d-block"><span
                                        class="company-name text-uppercase h4"><b></b>{{ $setting->company_name }}</span>
                                    <p class="company-tagline">{{ $setting->company_name }}</p>
                                </div> --}}
                            </a>
                            <div class="ms-auto"></div>
                            <div class="ms-auto"></div>
                        </nav>
                    </header>
                    <div class="row justify-content-center h-100">
                        <div class="col-12 col-md-6 col-lg-6">

                            <div class="row h-100 align-items-center justify-content-center my-md-3">

                                <div class="col-12 col-md-10 col-lg-8 col-xxl-6 login-box">
                                    <form action="{{ route('saveLeadLogin') }}" method="POST" class="needs-validation">
                                        @csrf
                                        <p class="h1 fw-bold text-center mb-4">
                                            <img src="/logo/{{ $setting->img }}" alt="" width="190" class="me-3"> <br><br>
                                            Login
                                        </p>
                                        <div class="form-floating mb-3">
                                            <input type="" class="form-control"
                                                 name="username" placeholder="Enter username "  
                                                autofocus="" required> <label for="emailadd">Username</label></div>
                                        <div class="position-relative">
                                            <div class="form-floating mb-3">
                                                <input type="password" class="form-control"
                                                    name="password" placeholder="Enter your password" required> <label
                                                    for="passwd">Password</label></div><button
                                                class="btn btn-square btn-link text-theme-1 position-absolute end-0 top-0 mt-2 me-2"><i
                                                    class="bi bi-eye"></i></button>
                                        </div>
                                        <div class="row align-items-center mb-3">


                                        </div>
                                        <button class="btn  btn-theme w-100" type="submit">Login</button>
                                    </form>


                                </div>
                            </div>
                        </div>
                    </div>
                    <footer class="adminuiux-footer mt-auto">
                        <div class="container-fluid text-center"><span class="small">Copyright @ {{ date('Y') }},
                                Creatively
                                designed by {{ $setting->company_name }} </span></div>
                    </footer>
                    <link rel="stylesheet" href="/salesapp/css/circus.min.css">
                    <script src="/salesapp/js/highlight.min.js"></script>
                    <script>
                        document.querySelectorAll(".code").forEach((e => {
                            hljs.highlightElement(e)
                        }))
                    </script>
                </div>
            </div>
        </div>
    </main>
</body>

</html>

<!-- CSS -->
<script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/arboshiki/lobibox/dist/css/lobibox.min.css">
<script src="https://cdn.jsdelivr.net/gh/arboshiki/lobibox/dist/js/lobibox.min.js"></script>

 
<script>
    function lobalerts(type, msg) {
        Lobibox.notify(type, {
            pauseDelayOnHover: true,
            size: 'mini',
            rounded: true,
            delayIndicator: true,
            icon: '',
            continueDelayOnInactiveTab: false,
            position: 'top right',
            msg: msg
        });
    }
     
     @if (Session::has('error'))
         lobalerts("error", "{{ Session::get('error') }}");
     @elseif (Session::has('success'))
         lobalerts("success", "{{ Session::get('success') }}");
     @elseif (Session::has('warning'))
         lobalerts("warning", "{{ Session::get('warning') }}");
     @endif
</script>

