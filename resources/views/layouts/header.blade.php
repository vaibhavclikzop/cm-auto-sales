 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
     @stack('title')

     <meta name="csrf-token" content="{{ csrf_token() }}">

     <!-- Favicon -->
     <link rel="shortcut icon" type="image/x-icon" href="/favicon.png">

     <!-- Bootstrap CSS -->
     <link rel="stylesheet" href="/css/bootstrap.min.css">

     <!-- Datetimepicker CSS -->
     <link rel="stylesheet" href="/css/bootstrap-datetimepicker.min.css">

     <!-- animation CSS -->
     <link rel="stylesheet" href="/css/animate.css">

     <!-- Select2 CSS -->
     <link rel="stylesheet" href="/css/select2.min.css">

     <!-- Fontawesome CSS -->
     <link rel="stylesheet" href="/css/fontawesome.min.css">
     <link rel="stylesheet" href="/css/all.min.css">

     <!-- Main CSS -->
     <link rel="stylesheet" href="/css/style.css">
     <link rel="stylesheet" href="/dataTables/datatables.min.css">
     <link rel="stylesheet" href="/richtexteditor/rte_theme_default.css" />
     <script type="text/javascript" src="/richtexteditor/rte.js"></script>
     <script type="text/javascript" src='/richtexteditor/plugins/all_plugins.js'></script>
     <script src="https://code.jquery.com/jquery-2.2.4.min.js"
         integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
 </head>

 <body>
     <!-- <div id="global-loader">
        <div class="whirly-loader"> </div>
    </div> -->
     <!-- Main Wrapper -->
     <div class="main-wrapper">

         <!-- Header -->
         <div class="header">

             <!-- Logo -->
             <div class="header-left active">
                 <a href="/" class="logo logo-normal">
                     <img src="/logo/{{ $setting->img }}" alt="" style="width:100%">
                 </a>
                 <a href="/" class="logo logo-white">
                     <img src="/logo/{{ $setting->img }}" alt="" width="100%">
                 </a>
                 <a href="/" class="logo-small">
                     <img src="/logo/{{ $setting->img }}" alt="" width="100%">
                 </a>
                 <a id="toggle_btn" href="javascript:void(0);">
                     <i data-feather="chevrons-left" class="feather-16"></i>
                 </a>
             </div>
             <!-- /Logo -->

             <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                 <span class="bar-icon">
                     <span></span>
                     <span></span>
                     <span></span>
                 </span>
             </a>

             <!-- Header Menu -->
             <ul class="nav user-menu">

                 <li class="nav-item nav-searchinputs">
                     <form action="{{ route('updateActivePanel') }}" method="POST" class="needs-validation" novalidate>
                         @csrf
                         @php
                             $panel_permission = explode(', ', $panel_permission);
                         @endphp
                         @foreach ($panel_permission as $item)
                             <button type="submit"
                                 class="btn btn-sm {{ $active_panel == $item ? 'btn-success' : 'btn-dark' }}"
                                 name="active_panel" value="{{ $item }}"> {{ $item }} </button>
                         @endforeach
                         <button type="button" class="btn btn-sm  btn-dark" name="active_panel"
                             value="">Attendance & Tracking </button>
                     </form>
                 </li>
                 <li class="nav-item nav-searchinputs">

                 </li>
                 <!-- /Search -->
                 <li class="nav-item">
                     <form action="{{ route('updateActiveInventory') }}" method="POST">
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
                 </li>
                 @php
                     $masters = [
                         'company',
                         'location',
                         'warehouse',
                         'party_type',
                         'customers',
                         'vendor',
                         'gst',
                         'status',
                         'mode_of_transport',
                         'special_offer',
                     ];
                 @endphp
                 @if (
                     ($active_panel === 'Lead Management' ||
                         $active_panel === 'Purchase' ||
                         $active_panel === 'Order' ||
                         $active_panel === 'Dispatch') &&
                         $rolePermissions->whereIn('permission_name', $masters)->where('view', 1)->isNotEmpty())
                     <li class="nav-item dropdown has-arrow main-drop">
                         <a href="javascript:void(0);" class="dropdown-toggle nav-link userset "
                             data-bs-toggle="dropdown">

                             <span class="btn btn-success btn-sm"> Masters</span>
                         </a>
                         <div class="dropdown-menu menu-drop-user">
                             <div class="profilename">

                                 @if ($rolePermissions->where('permission_name', 'company')->where('view', 1)->isNotEmpty())
                                     <a class="dropdown-item pb-0" href="/company">Company</a>
                                 @endif
                                 @if ($rolePermissions->where('permission_name', 'warehouse')->where('view', 1)->isNotEmpty())
                                     <a class="dropdown-item pb-0" href="/warehouse">Warehouse</a>
                                 @endif
                                 @if ($rolePermissions->where('permission_name', 'location')->where('view', 1)->isNotEmpty())
                                     <a class="dropdown-item pb-0" href="/store-location">Location</a>
                                 @endif
                                 @if ($rolePermissions->where('permission_name', 'party_type')->where('view', 1)->isNotEmpty())
                                     <a class="dropdown-item pb-0" href="/customer-type">Party Type</a>
                                 @endif
                                 @if ($rolePermissions->where('permission_name', 'customers')->where('view', 1)->isNotEmpty())
                                     <a class="dropdown-item pb-0" href="/customers">Customers</a>
                                 @endif
                                 @if ($rolePermissions->where('permission_name', 'vendor')->where('view', 1)->isNotEmpty())
                                     <a class="dropdown-item pb-0" href="/vendor">Vendor</a>
                                 @endif
                                 @if ($rolePermissions->where('permission_name', 'gst')->where('view', 1)->isNotEmpty())
                                     <a class="dropdown-item pb-0" href="/gst">GST</a>
                                 @endif

                                 @if ($rolePermissions->where('permission_name', 'status')->where('view', 1)->isNotEmpty())
                                     <a class="dropdown-item pb-0" href="/status">Status</a>
                                 @endif

                                 @if ($rolePermissions->where('permission_name', 'mode_of_transport')->where('view', 1)->isNotEmpty())
                                     <a class="dropdown-item pb-0" href="/mode-of-transport">Mode of Transport</a>
                                 @endif

                                 @if ($rolePermissions->where('permission_name', 'special_offer')->where('view', 1)->isNotEmpty())
                                     <a class="dropdown-item pb-0" href="/special-offer">Special Offer</a>
                                 @endif


                             </div>
                         </div>
                     </li>
                 @endif

                 @php
                     $staff_management = ['users', 'user_role'];
                 @endphp
                 @if (
                     ($active_panel === 'Lead Management' ||
                         $active_panel === 'Purchase' ||
                         $active_panel === 'Order' ||
                         $active_panel === 'Dispatch') &&
                         $rolePermissions->whereIn('permission_name', $staff_management)->where('view', 1)->isNotEmpty())
                     <li class="nav-item dropdown has-arrow main-drop">
                         <a href="javascript:void(0);" class="dropdown-toggle nav-link userset "
                             data-bs-toggle="dropdown">

                             <span class="btn btn-secondary btn-sm"> Staff Management</span>
                         </a>
                         <div class="dropdown-menu menu-drop-user">
                             <div class="profilename">

                                 @if ($rolePermissions->where('permission_name', 'user_role')->where('view', 1)->isNotEmpty())
                                     <a class="dropdown-item pb-0" href="/user-role">User Role</a>
                                 @endif
                                 @if ($rolePermissions->where('permission_name', 'users')->where('view', 1)->isNotEmpty())
                                     <a class="dropdown-item pb-0" href="/users">Users</a>
                                 @endif


                             </div>
                         </div>
                     </li>
                 @endif

                 <li class="nav-item dropdown has-arrow main-drop">

                     <a href="javascript:void(0);" class="dropdown-toggle nav-link userset"
                         data-bs-toggle="dropdown">
                         <span class="user-info">

                             <i class="fa fa-user-circle" style="font-size: 40px; margin-right: 10px;"></i>
                             <span class="user-detail">
                                 <span class="user-name">User</span>
                                 <span class="user-role"></span>
                             </span>
                         </span>
                     </a>
                     <div class="dropdown-menu menu-drop-user">
                         <div class="profilename">
                             <div class="profileset">
                                 <!-- <span class="user-img"><img src="/images/avator1.jpg" alt=""> -->
                                 <i class="fa fa-user-circle" style="font-size: 28px;"></i>
                                 <span class="status online"></span></span>
                                 <div class="profilesets">
                                     <h6></h6>
                                     <h5></h5>
                                 </div>
                                 <a href="/profile">Profile</a>

                             </div>
                             <hr class="m-0">
                             @if (session('user')->role_id == 1)
                                 <a class="dropdown-item  pb-0" href="/settings">
                                     <i class="fa fa-gear me-2" aria-hidden="true"></i> Setting</a>
                             @endif

                               <a class="dropdown-item pb-0" href="/team-hierarchy"  ><i data-feather="users"></i><span>Team hierarchy</span></a>
                             <a class="dropdown-item pb-0" href="#" data-bs-toggle="modal"
                                 data-bs-target="#installGuideModal"><i data-feather="download"></i><span>Download
                                     App</span></a>
                             <a class="dropdown-item logout pb-0" href="../logout"><img src="/images/log-out.svg"
                                     class="me-2" alt="img">Logout</a>
                         </div>
                     </div>
                 </li>
             </ul>
             <!-- /Header Menu -->

             <!-- Mobile Menu -->
             <div class="dropdown mobile-user-menu">
                 <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                     aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                 <div class="dropdown-menu dropdown-menu-right">
                     <a class="dropdown-item" href="#">My Profile</a>
                     <a class="dropdown-item" href="#">Settings</a>
                     <a class="dropdown-item" href="#">Logout</a>
                 </div>
             </div>
             <!-- /Mobile Menu -->
         </div>
         <!-- /Header -->

         <!-- Sidebar -->
         <div class="sidebar" id="sidebar">
             <div class="sidebar-inner slimscroll">
                 <div id="sidebar-menu" class="sidebar-menu">
                     <ul>

                         <li class="submenu-open">
                             <ul>
                                 <li><a href="/"><i data-feather="home"></i><span>Dashboard</span></a></li>
                             </ul>
                         </li>
                         <li class="submenu-open">

                             <ul>
                                 @php
                                     $product_master = ['unit_type', 'brand', 'category', 'sub_category', 'product'];
                                 @endphp
                                 @if (
                                     ($active_panel === 'Lead Management' ||
                                         $active_panel === 'Purchase' ||
                                         $active_panel === 'Order' ||
                                         $active_panel === 'Dispatch') &&
                                         $rolePermissions->whereIn('permission_name', $product_master)->where('view', 1)->isNotEmpty())
                                     <li class="submenu">
                                         <a href="javascript:void(0);">
                                             <i data-feather="layers"></i><span>Product Master</span><span
                                                 class="menu-arrow"></span>
                                         </a>
                                         <ul>
                                             @if ($rolePermissions->where('permission_name', 'unit_type')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/unit-type">Unit Type</a></li>
                                             @endif

                                             @if ($rolePermissions->where('permission_name', 'brand')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/brand">Brand</a></li>
                                             @endif

                                             @if ($rolePermissions->where('permission_name', 'category')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/category">Category</a></li>
                                             @endif

                                             @if ($rolePermissions->where('permission_name', 'sub_category')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/sub-category">Sub Category</a></li>
                                             @endif

                                             @if ($rolePermissions->where('permission_name', 'product')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/products">Product</a></li>
                                             @endif

                                         </ul>
                                     </li>
                                 @endif

                                 @php
                                     $product_master = [
                                         'generate_po',
                                         'generating_po',
                                         'generated_po',
                                         'purchases',
                                         'mrn',
                                         'partial_approved',
                                         'full_approved',
                                         'purchase_return',
                                         'mrn_product_wise',
                                     ];
                                 @endphp

                                 @if (
                                     $active_panel === 'Purchase' &&
                                         $rolePermissions->whereIn('permission_name', $product_master)->where('view', 1)->isNotEmpty())
                                     <li class="submenu">
                                         <a href="javascript:void(0);">
                                             <i data-feather="layers"></i><span>Purchase Order</span><span
                                                 class="menu-arrow"></span>
                                         </a>
                                         <ul>
                                             @if ($rolePermissions->where('permission_name', 'generate_po')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/generate-po">Generate PO</a></li>
                                             @endif


                                             @if ($rolePermissions->where('permission_name', 'generating_po')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/purchase-order/pending">Generating PO</a></li>
                                             @endif

                                             @if ($rolePermissions->where('permission_name', 'generated_po')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/purchase-order/generated">Generated PO</a></li>
                                             @endif

                                             @if ($rolePermissions->where('permission_name', 'purchases')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/inward-stock">Purchases</a></li>
                                             @endif

                                             @if ($rolePermissions->where('permission_name', 'mrn')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/inward-report">MRN</a></li>
                                             @endif

                                             @if ($rolePermissions->where('permission_name', 'partial_approved')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/purchase-order/partial">Partial Approved</a></li>
                                             @endif

                                             @if ($rolePermissions->where('permission_name', 'full_approved')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/purchase-order/complete">Full Approved</a></li>
                                             @endif

                                             @if ($rolePermissions->where('permission_name', 'purchase_return')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/purchase-return">Purchase Return</a></li>
                                             @endif

                                             @if ($rolePermissions->where('permission_name', 'mrn_product_wise')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/inward-product-wise">MRN Product Wise</a></li>
                                             @endif

                                         </ul>
                                     </li>
                                 @endif

                                 @if (
                                     $active_panel === 'Lead Management' &&
                                         $rolePermissions->where('permission_name', 'lead_management')->where('view', 1)->isNotEmpty())

                                     <li class="submenu">
                                         <a href="javascript:void(0);">
                                             <i data-feather="layers"></i><span>Lead Management</span><span
                                                 class="menu-arrow"></span>
                                         </a>
                                         <ul>

                                             @foreach ($Leadstatus as $i)
                                                 <li><a href="/lead/{{ $i->id }}">{{ $i->name }}</a>
                                                 </li>
                                             @endforeach


                                         </ul>
                                     </li>

                                 @endif


                                 @if (
                                     $active_panel === 'Lead Management' &&
                                         $rolePermissions->where('permission_name', 'meetings')->where('view', 1)->isNotEmpty())
                                     <li><a href="/meetings"><i data-feather="home"></i><span>Meetings</span></a>
                                     </li>
                                 @endif


                                 @php
                                     $order_management = [
                                         'create_order',
                                         'pending_order',
                                         'completed_order',
                                         'view_pick_tickets',
                                         'cancelled_order',
                                         'sale_return',
                                         'invoices',
                                     ];
                                 @endphp

                                 @if (
                                     $active_panel === 'Order' &&
                                         $rolePermissions->whereIn('permission_name', $order_management)->where('view', 1)->isNotEmpty())
                                     <li class="submenu">
                                         <a href="javascript:void(0);">
                                             <i data-feather="layers"></i><span>Order Management</span><span
                                                 class="menu-arrow"></span>
                                         </a>
                                         <ul>


                                             @if ($rolePermissions->where('permission_name', 'create_order')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/new-order">Create order </a></li>
                                             @endif


                                             @if ($rolePermissions->where('permission_name', 'new_order')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/orders?status=pending">New Order </a></li>
                                             @endif


                                             @if ($rolePermissions->where('permission_name', 'pending_order')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/orders?status=processing">Pending Order </a></li>
                                             @endif


                                             @if ($rolePermissions->where('permission_name', 'completed_order')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/orders?status=complete">Completed Order </a></li>
                                             @endif

                                             @if ($rolePermissions->where('permission_name', 'view_pick_tickets')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/outward-order-list?status=pending">View Pick Tickets
                                                     </a>
                                                 </li>
                                             @endif


                                             @if ($rolePermissions->where('permission_name', 'cancelled_order')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/orders?status=cancel">Cancelled Order </a></li>
                                             @endif


                                         </ul>
                                     </li>

                                     @if ($rolePermissions->where('permission_name', 'sale_return')->where('view', 1)->isNotEmpty())
                                         <li><a href="/sale-return"><i data-feather="home"></i><span>Sale
                                                     Return</span></a>
                                         </li>
                                     @endif

                                     @if ($rolePermissions->where('permission_name', 'invoices')->where('view', 1)->isNotEmpty())
                                         <li><a href="/invoices"><i data-feather="home"></i><span>Invoices</span></a>
                                         </li>
                                     @endif
                                 @endif

                                 @php
                                     $dispatch_management = ['dispatch_plan', 'ready_to_deliver', 'delivered'];
                                 @endphp
                                 @if (
                                     $active_panel === 'Dispatch' &&
                                         $rolePermissions->whereIn('permission_name', $dispatch_management)->where('view', 1)->isNotEmpty())
                                     <li class="submenu">
                                         <a href="javascript:void(0);">
                                             <i data-feather="layers"></i><span>Dispatch Management</span><span
                                                 class="menu-arrow"></span>
                                         </a>
                                         <ul>
                                             @if ($rolePermissions->where('permission_name', 'dispatch_plan')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/dispatch-plan">Dispatch Plan </a>
                                                 </li>
                                             @endif
                                             @if ($rolePermissions->where('permission_name', 'ready_to_deliver')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/dispatch?status=pending">Ready to Deliver</a>
                                             @endif


                                             @if ($rolePermissions->where('permission_name', 'delivered')->where('view', 1)->isNotEmpty())
                                                 <li><a href="/dispatch?status=delivered">Delivered</a>
                                                 </li>
                                             @endif
                                         </ul>
                                     </li>
                                 @endif



                             </ul>





                         </li>

                         @if (
                             ($active_panel === 'Purchase' || $active_panel === 'Order' || $active_panel === 'Dispatch') &&
                                 $rolePermissions->where('permission_name', 'store')->where('view', 1)->isNotEmpty())
                             <li class="submenu-open">

                                 <ul>
                                     <li class="submenu">
                                         <a href="javascript:void(0);">
                                             <i data-feather="layers"></i><span>Store</span><span
                                                 class="menu-arrow"></span>
                                         </a>
                                         <ul>
                                             <li><a href="/current-stock">Current Stock</a></li>
                                             <li><a href="/near-by-minimum-stock">Near by Minimum Stock</a></li>
                                             <li><a href="/defective-stock">Defective Stock</a></li>
                                             <li><a href="/product-ledger">Product Ledger</a></li>
                                             <li><a href="/in-out-report">In Out Report</a></li>




                                         </ul>
                                     </li>



                                 </ul>

                             </li>
                         @endif

                         @if (
                             ($active_panel === 'Purchase' || $active_panel === 'Order' || $active_panel === 'Dispatch') &&
                                 $rolePermissions->where('permission_name', 'reports')->where('view', 1)->isNotEmpty())
                             <li class="submenu-open">

                                 <ul>
                                     <li class="submenu">
                                         <a href="javascript:void(0);">
                                             <i data-feather="layers"></i><span>Reports</span><span
                                                 class="menu-arrow"></span>
                                         </a>
                                         <ul>
                                             <li><a href="/po-report">PO Report</a></li>

                                             <li><a href="/sale-report-tally">Sale Report Tally</a></li>
                                             <li><a href="/sale-report">Sale Report</a></li>
                                             <li><a href="/customer-wise-sale-report">Customer Wise Sale Report</a>
                                             </li>
                                             <li><a href="/product-wise-sale-report">Product Wise Sale Report</a></li>
                                             <li><a href="/customer-product-report">Customer/DSR/Product Report</a>
                                             </li>
                                             <li><a href="/dsr-report">DSR Wise Report</a></li>
                                             <li><a href="/order-vs-stock">Order Vs Stock</a></li>
                                             <li><a href="/order-vs-invoice">Order Vs invoice</a></li>
                                             <li><a href="/slow-fast-moving-products">Slow/Fast Moving Products</a>
                                             </li>
                                             <li><a href="/category-wise-report">Category Wise Report</a></li>
                                             <li><a href="/purchase-report">Purchase Report</a></li>
                                             <li><a href="/purchase-report-product-wise">Purchase Report Product Wise</a></li>
                                             <li><a href="/purchase-return-report">Purchase Return Report</a></li>
                                             <li><a href="/sale-return-report">Sale Return Report</a></li>

                                         </ul>
                                     </li>



                                 </ul>



                                 {{-- <ul>
                                         <li class="submenu">
                                             <a href="javascript:void(0);">
                                                 <i data-feather="layers"></i><span>Audit Report</span><span
                                                     class="menu-arrow"></span>
                                             </a>
                                             <ul>
                                                 <li><a href="/audit-setting">Audit Setting</a></li>
                                                 <li><a href="/audit-report">Audit Report</a></li>




                                             </ul>
                                         </li>



                                     </ul>
                         --}}



                             </li>
                         @endif
                         @foreach ($rolePermissions as $item)
                             @if ($item->permission_id == 14 && $item->view == 1)
                                 <li><a href="/settings"><i data-feather="settings"></i><span>Settings</span></a></li>
                             @endif
                         @endforeach

                         <li><a href="../logout"><i data-feather="log-out"></i><span>Logout</span></a></li>
                     </ul>
                 </div>
             </div>
         </div>
         <!-- /Sidebar -->



         <div class="page-wrapper">
             <div class="content">
