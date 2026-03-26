 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
     @stack('title')


     <meta name="csrf-token" content="{{ csrf_token() }}">

     <!-- Favicon -->
     <link rel="shortcut icon" type="image/x-icon" href="/logo/{{ $setting->img }}">
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
     <link
         href="https://fonts.googleapis.com/css2?family=Fira+Sans+Condensed:wght@100;400;500;600&family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,400&display=swap"
         rel="stylesheet">
     <style>
         :root {
             --adminuiux-content-font: 'Roboto';
             --adminuiux-content-font-weight: 400;
             --adminuiux-title-font: "Fira Sans Condensed";
             --adminuiux-title-font-weight: 500
         }
     </style>

     <link href="/salesapp/css/app.css" rel="stylesheet">

     <link rel="stylesheet" href="/dataTables/datatables.min.css">
     <link rel="stylesheet" href="/richtexteditor/rte_theme_default.css" />
     <script type="text/javascript" src="/richtexteditor/rte.js"></script>
     <script type="text/javascript" src='/richtexteditor/plugins/all_plugins.js'></script>


     <script defer src="/salesapp/js/app.js"></script>

     <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
     <script defer src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
         integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
         crossorigin="anonymous" referrerpolicy="no-referrer" />
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/brands.min.css"
         integrity="sha512-mXqwRsOznG7CS37KA7CLR1Fc72gfOgp7r8xaVdBKoBKhitcKI/mK+IamtZUf+FAkufXOvVTESu9lPsoQc+kFxg=="
         crossorigin="anonymous" referrerpolicy="no-referrer" />

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
         integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK" crossorigin="anonymous">
     </script>
     <script src="https://code.jquery.com/jquery-2.2.4.min.js"
         integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>

 </head>

 <body class="main-bg main-bg-opac main-bg-blur overflow-hidden">
     <div class="pageloader">
         <div class="container h-100">
             <div class="row justify-content-center align-items-center text-center h-100">
                 <div class="col-12 mb-auto pt-4"></div>
                 <div class="col-auto"><img src="/logo/{{ $setting->img }}" alt="" class="height-60 mb-3">
                     {{-- <p class="h6 mb-0"></p>
                    <p class="h3 mb-4">{{$setting->company_name}}</p> --}}
                     <div class="loaderplus mb-2"></div>
                 </div>
                 <div class="col-12 mt-auto pb-4">
                     <p class="text-secondary">Please wait we are preparing awesome things to preview...</p>
                 </div>
             </div>
         </div>
     </div>
     <header class="adminuiux-header">
         <nav class="navbar navbar-expand-lg fixed-top">

             <button class="btn btn-theme btn-sm"> <i class="fa-solid fa-arrow-left"></i> </button>

             </a>
             <div class="ms-auto">

             </div>
             <div class="ms-auto">

             </div>
             <div class="ms-auto">
                <form action="{{ route('leadApp/updateActiveInventory') }}" method="POST">
                         @csrf
                         <select name="active_inventory" id="active_inventory" onchange="this.form.submit()"
                             class="form-control">
                             <option value="">Select</option>
                             @foreach ($headerStore as $item)
                                 <option value="{{ $item->id }}"
                                     {{ $active_inventory == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                 </option>
                             @endforeach
                         </select>
                     </form>

             </div>
             <div class="ms-auto">
                 <div class="dropdown d-inline-block">
                     <a class="dropdown-toggle btn btn-link btn-link-header style-none" id="userprofiledd"
                         data-bs-toggle="dropdown" aria-expanded="false" role="button">
                         <div class="row gx-0 d-inline-flex">
                             <div class="col-auto align-self-center">
                                 <figure class="avatar avatar-28 rounded-circle coverimg align-middle"><img
                                         src="/salesapp/images/user-6.jpg" alt="" id="userphotoonboarding2">
                                 </figure>
                             </div>

                         </div>
                     </a>
                     <div class="dropdown-menu dropdown-menu-end width-300 pt-0 px-0" aria-labelledby="userprofiledd">
                         <div class="bg-theme-1-space rounded py-3 mb-3 dropdown-dontclose">
                             <div class="row gx-0">
                                 <div class="col-auto px-3">
                                     <figure class="avatar avatar-50 rounded-circle coverimg align-middle"><img
                                             src="/salesapp/images/user-6.jpg" alt=""></figure>
                                 </div>
                                 <div class="col align-self-center">
                                     <p class="mb-1"><span>{{ Session('user')->name }}</span></p>

                                 </div>
                             </div>
                         </div>
                         <div class="px-2">
                             {{-- <div><a class="dropdown-item" href="/sales-app/my-profile"><i data-feather="user"
                                             class="avatar avatar-18 me-1"></i> My Profile</a></div>
                                 <div> --}}
                             <a class="dropdown-item" href="/lead-app/dashboard">
                                 <div class="row g-0">
                                     <div class="col align-self-center"><i data-feather="layout"
                                             class="avatar avatar-18 me-1"></i> My Dashboard</div>

                                 </div>
                             </a>
                         </div>


                         <div><a class="dropdown-item theme-red" href="/lead-app/logout"><i data-feather="power"
                                     class="avatar avatar-18 me-1"></i> Logout</a></div>
                     </div>
                 </div>
             </div>
             </div>
             </div>
         </nav>

     </header>


     <div class="adminuiux-wrap">
         <div class="adminuiux-sidebar">
             <div class="adminuiux-sidebar-inner">
                 <div class="px-3 not-iconic mt-3">
                     <div class="row">
                         <div class="col align-self-center">
                             <p class="h6">Main Menu</p>
                         </div>
                         <div class="col-auto"><a class="btn btn-link btn-square" data-bs-toggle="collapse"
                                 data-bs-target="#usersidebarprofile" aria-expanded="false" role="button"
                                 aria-controls="usersidebarprofile"><i data-feather="user"></i></a></div>
                     </div>
                     <div class="text-center collapse" id="usersidebarprofile">
                         <figure class="avatar avatar-100 rounded-circle coverimg my-3"><img
                                 src="/salesapp/images/user-6.jpg" alt=""></figure>
                         <p class="mb-1 h5">{{ Session('user')->name }}</p>

                     </div>
                 </div>
                 <ul class="nav flex-column menu-active-line my-3">
                     <li class="nav-item"><a href="/sales-app/dashboard" class="nav-link"><i class="menu-icon"
                                 data-feather="grid"></i> <span class="menu-name">Dashboard</span></a></li>
                     <li class="nav-item dropdown"><a href="javascrit:void(0)" class="nav-link dropdown-toggle"
                             data-bs-toggle="dropdown"><i class="menu-icon" data-feather="calendar"></i> <span
                                 class="menu-name">Master's</span></a>
                         <div class="dropdown-menu">
                             <div class="nav-item"><a href="/sales-app/client" class="nav-link"><i class="menu-icon"
                                         data-feather="calendar"></i> <span class="menu-name">Client</span></a></div>
                             <div class="nav-item"><a href="/sales-app/architect" class="nav-link"><i
                                         class="menu-icon" data-feather="table"></i> <span
                                         class="menu-name">Architect</span></a></div>
                             <div class="nav-item"><a href="/sales-app/electrician" class="nav-link"><i
                                         class="menu-icon" data-feather="layers"></i> <span
                                         class="menu-name">Electrician
                                     </span></a></div>

                         </div>
                     </li>

                     <li class="nav-item"><a href="/service-app/expenses" class="nav-link"><i class="menu-icon"
                                 data-feather="grid"></i> <span class="menu-name">Expenses</span></a></li>
                 </ul>
                 <div class="mt-auto"></div>

             </div>
         </div>
         <main class="adminuiux-content has-sidebar" onclick="contentClick()">
             <div class="container mt-3" id="main-content">
