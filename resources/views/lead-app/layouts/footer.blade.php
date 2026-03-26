 <footer class="adminuiux-mobile-footer style-1">
     <div class="container">
         <ul class="nav nav-pills nav-justified">
             <li class="nav-item"><a class="nav-link" href="/lead-app/dashboard"><span><i class="nav-icon"
                             data-feather="home"></i> <span class="nav-text">Home</span></span></a></li>

             <li class="nav-item"><a class="nav-link" href="/lead-app/leads"><span>
                         <i class="fa fa-file" aria-hidden="true"></i> <span class="nav-text">Leads</span></span></a>
             </li>
             <li class="nav-item"><a href="/lead-app/add-lead" class="nav-link center-menu-btn"><span
                         class="bg-theme-r-gradient rounded-circle text-white"><i
                             class="nav-icon bi bi-plus-lg text-white"></i></span></a></li>
             <li class="nav-item"><a class="nav-link" href="/lead-app/meetings"><span>
                         <i class="fa fa-handshake" aria-hidden="true"></i>
                         <span class="nav-text">Meetings</span></span></a></li>
             <li class="nav-item"><a class="nav-link" href="/lead-app/orders?status=pending"><span><i class="nav-icon"
                             data-feather="file-text"></i> <span class="nav-text">Orders</span></span></a>
             </li>
         </ul>
     </div>
 </footer>


 </main>
 </div>
 </div>


 <script src="/salesapp/js/clinic-dashboard.js"></script>
 </body>

 </html>


 <form action=" " method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
     @csrf
     <div class="modal fade" id="expenseModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
         role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
         <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="modalTitleId">
                         Expense
                     </h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <div class="row">
                         <div class="col-md-12">
                             <label for="">Attach File</label>
                             <input type="file" name="file" class="form-control" required>

                         </div>
                         <div class="col-md-12 mt-2">
                             <label for="">Category</label>
                             <select name="category" id="category" class="form-control" required>
                                 <option value="">Select</option>
                                 <option value="TA">TA</option>
                                 <option value="DA">DA</option>
                                 <option value="HRA">HRA</option>
                                 <option value="Other">Other</option>
                             </select>
                         </div>
                         <div class="col-md-12 mt-2">
                             <label for="">Amount</label>
                             <input type="decimal" name="amount" class="form-control" placeholder="Enter Amount"
                                 required>

                         </div>
                         <div class="col-md-12 mt-2">
                             <label for="">Name</label>
                             <input type="text" name="name" class="form-control" placeholder="Enter Name/Title"
                                 required>

                         </div>
                         <div class="col-md-12 mt-2">
                             <label for="">Description</label>
                             <textarea name="description" id="description" class="form-control" placeholder="Enter Description"></textarea>
                         </div>

                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                         Close
                     </button>
                     <button type="submit" class="btn btn-primary">Save</button>
                 </div>
             </div>
         </div>
     </div>
 </form>

 <div class="fab-wrapper">
     <button class="fab-main" id="fabToggle">
         <i class="fas fa-plus" id="fabIcon"></i>
     </button>

     <div class="fab-menu" id="fabMenu">
         <button class="fab-item" data-bs-toggle="modal" data-bs-target="#meetingModal">
             <i class="fas fa-calendar-alt"></i>
             <span>Add Meeting</span>
         </button>

         <a class="fab-item" style="text-decoration: none; color:black" href="/lead-app/create-order">
             <i class="fas fa-shopping-cart"></i>
             <span>Add Order</span>
         </a>
     </div>
 </div>



 <form action="{{ route('/lead-app/SaveMeeting') }}" method="post" class="needs-validation" novalidate>
     @csrf
     <div class="modal fade" id="meetingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
         role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
         <div class="modal-dialog  " role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="modalTitleId">
                         Meetings
                     </h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <div class="row">
                         <div class="col-12">
                             <label for="">Meeting Title</label>
                             <input type="text" name="title" id="title" class="form-control" required>

                         </div>

                         <div class="col-12 mt-3">
                             <label for="">Description</label>
                             <textarea type="text" name="description" id="description" class="form-control"></textarea>

                         </div>
                         <div class="col-6 mt-3">
                             <label for="">Start Time</label>
                             <input type="datetime-local" name="start_time" id="title" class="form-control"
                                 required>

                         </div>

                         <div class="col-6 mt-3">
                             <label class="form-label">Meeting Type</label>
                             <select name="meeting_type" class="form-control" required>
                                 <option value="">All Types</option>
                                 <option value="internal">Internal
                                 </option>
                                 <option value="client">Client</option>
                                 <option value="team">Team</option>
                                 <option value="general">General
                                 </option>
                             </select>
                         </div>

                         <div class="col-6 mt-3">
                             <label for="">Location</label>
                             <input type="" name="location" id="location" class="form-control">

                         </div>

                         <div class="col-6 mt-3">
                             <label for="">Virtual Meeting Link</label>
                             <input type="url" name="meeting_link" id="meeting_link" class="form-control">

                         </div>


                         <div class="col-12 mt-3">
                             <label for="">Customer</label>
                             <select name="customer_id" id="customer_id" required class="form-control">
                                 <option value="">Select</option>
                                 @foreach ($headerCustomer as $item)
                                     <option value="{{ $item->id }}"> {{ $item->name }} </option>
                                 @endforeach
                             </select>

                         </div>

                     </div>

                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                         Close
                     </button>
                     <button type="submit" class="btn btn-primary">Save</button>
                 </div>
             </div>
         </div>
     </div>
 </form>



 <style>
     .fab-wrapper {
         position: fixed;
         bottom: 80px;
         right: 20px;
         z-index: 1050;
     }

     /* Main FAB */
     .fab-main {
         width: 40px;
         height: 40px;
         border-radius: 50%;
         background: linear-gradient(135deg, #4f46e5, #6366f1);
         color: #fff;
         border: none;
         font-size: 14px;
         box-shadow: 0 8px 20px rgba(0, 0, 0, .3);
         transition: transform .35s cubic-bezier(.4, 0, .2, 1),
             box-shadow .35s ease;
     }

     .fab-main:hover {
         transform: scale(1.05);
     }

     /* Rotate + morph */
     .fab-main.active {
         transform: rotate(45deg);
         box-shadow: 0 10px 25px rgba(0, 0, 0, .4);
     }

     /* Menu */
     .fab-menu {
         position: absolute;
         bottom: 50px;
         right: 0;
         display: flex;
         flex-direction: column;
         gap: 10px;
         pointer-events: none;
     }

     /* Menu item animation */
     .fab-item {
         opacity: 0;
         transform: translateY(15px) scale(.9);
         transition: all .35s cubic-bezier(.4, 0, .2, 1);
     }

     /* Staggered animation */
     .fab-menu.show .fab-item {
         opacity: 1;
         transform: translateY(0) scale(1);
         pointer-events: auto;
     }

     .fab-menu.show .fab-item:nth-child(1) {
         transition-delay: .05s;
     }

     .fab-menu.show .fab-item:nth-child(2) {
         transition-delay: .1s;
     }

     /* Button look */
     .fab-item {
         display: flex;
         align-items: center;
         gap: 10px;
         background: #fff;
         border-radius: 15px;
         padding: 10px 16px;
         border: none;
         font-size: 14px;
         font-weight: 600;
         box-shadow: 0 4px 14px rgba(0, 0, 0, .15);
         width: 151px;
     }

     .fab-item i {
         color: #4f46e5;
     }

     #loader {
         display: none;
         /* Initially hidden */
         position: fixed;
         /* Stays in the same position while scrolling */
         top: 0;
         left: 0;
         width: 100vw;
         /* Full width */
         height: 100vh;
         /* Full height */
         display: flex;
         /* Flexbox to center */
         justify-content: center;
         /* Horizontal centering */
         align-items: center;
         /* Vertical centering */
         background-color: rgba(255, 255, 255, 0.7);
         /* Optional: translucent background */
         z-index: 9999;
         /* Ensure it appears on top of other elements */
     }

     .floating-btn {
         position: fixed;
         bottom: 70px;
         right: 20px;
         width: 40px;
         height: 40px;
         background-color: #007bff;
         /* change color if needed */
         color: white;
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 18px;
         text-decoration: none;
         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
         transition: all 0.3s ease;
         z-index: 9999;
     }

     .floating-btn:hover {
         background-color: #0056b3;
         transform: scale(1.1);
     }
 </style>
 <!-- /Main Wrapper -->
 {{-- <div id="loader" style="display:none;">
     <img src="loader.gif">
 </div> --}}







 <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
 <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
 <script>
     const fabToggle = document.getElementById("fabToggle");
     const fabMenu = document.getElementById("fabMenu");

     fabToggle.addEventListener("click", () => {
         fabMenu.classList.toggle("show");
         fabToggle.classList.toggle("active");
     });

     // Auto close on outside click
     document.addEventListener("click", (e) => {
         if (!e.target.closest(".fab-wrapper")) {
             fabMenu.classList.remove("show");
             fabToggle.classList.remove("active");
         }
     });
 </script>

 <script>
     (() => {
         'use strict'

         // Fetch all the forms we want to apply custom Bootstrap validation styles to
         const forms = document.querySelectorAll('.needs-validation')

         // Loop over them and prevent submission
         Array.from(forms).forEach(form => {
             form.addEventListener('submit', event => {
                 if (!form.checkValidity()) {
                     event.preventDefault()
                     event.stopPropagation()
                 }

                 form.classList.add('was-validated')
             }, false)
         })
     })()
     //  $(".dataTable").DataTable({
     //      "responsive": true,
     //      "lengthChange": true,
     //      "autoWidth": false,
     //      "ordering": true,
     //      "buttons": ["excel", 'csv'],
     //      "pageLength": 10,
     //      "lengthMenu": [
     //          [10, 25, 50, -1],
     //          [10, 25, 50, "All"]
     //      ],
     //  }).buttons().container().appendTo('.col-md-6:eq()');


     //  $("#dataTable").DataTable({
     //      "responsive": true,
     //      "lengthChange": true,
     //      "autoWidth": false,
     //      "ordering": true,
     //      "buttons": ["excel", 'csv'],
     //      "pageLength": 10,
     //      "lengthMenu": [
     //          [10, 25, 50, -1],
     //          [10, 25, 50, "All"]
     //      ],
     //      "paging": false,
     //      "searching": false,
     //  }).buttons().container().appendTo('.col-md-6:eq()');

     toastr.options = {
         "closeButton": true,
         "debug": false,
         "newestOnTop": false,
         "progressBar": true,
         "positionClass": "toast-top-right",
         "preventDuplicates": false,
         "onclick": null,
         "showDuration": "300",
         "hideDuration": "1000",
         "timeOut": "5000",
         "extendedTimeOut": "1000",
         "showEasing": "swing",
         "hideEasing": "linear",
         "showMethod": "fadeIn",
         "hideMethod": "fadeOut"
     }
     @if (Session::has('error'))
         toastr.error('{{ Session::get('error') }}');
     @elseif (Session::has('success'))
         toastr.success('{{ Session::get('success') }}');
     @elseif (Session::has('warning'))
         toastr.warning('{{ Session::get('warning') }}');
     @endif

     $("#state").on("change", function() {
         $.ajax({
             url: "/GetCity",
             type: "POST",
             data: {
                 state: $(this).val(),
             },
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             success: function(result) {
                 var options = [{
                     value: '',
                     label: '----Select City----',
                     selected: true
                 }];

                 result.forEach(function(element) {
                     options.push({
                         value: element.city,
                         label: element.city
                     });
                 });

                 // Replace options in Choices instance without re-initializing
                 if (selectInstances['city']) {
                     selectInstances['city'].clearStore();
                     selectInstances['city'].setChoices(options, 'value', 'label', true);
                 }
             },
             error: function(result) {
                 console.log(result);
             }
         });

     })


     $("#stateSimple").on("change", function() {
         $.ajax({
             url: "/GetCity",
             type: "POST",
             data: {
                 state: $(this).val(),
             },
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             success: function(result) {
                 var options = "Select";

                 result.forEach(function(element) {
                     options += `<option value="${element.city}">${element.city}</option>`;
                 });
                 $("#citySimple").html(options);

             },
             error: function(result) {
                 console.log(result);
             }
         });

     })
 </script>


 <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

 <script>
     async function generatePDF() {
         const element = document.getElementById('printorder');
         const canvas = await html2canvas(element);
         const imgData = canvas.toDataURL('image/png');
         const {
             jsPDF
         } = window.jspdf;
         const pdf = new jsPDF();
         const imgProps = pdf.getImageProperties(imgData);
         const pdfWidth = pdf.internal.pageSize.getWidth();
         const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
         pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
         pdf.save('generated-po-list.pdf');
     }
 </script>

 <style>
     @media print {
         .noPrint {
             display: none;
             margin-top: 0px;
         }
     }

     body {
         margin-top: 10px;
         color: #484b51;
     }

     .text-secondary-d1 {
         color: #728299 !important;
     }

     .page-header {
         margin: 0 0 1rem;
         padding-bottom: 1rem;
         padding-top: .5rem;
         border-bottom: 1px dotted #e2e2e2;
         display: -ms-flexbox;
         display: flex;
         -ms-flex-pack: justify;
         justify-content: space-between;
         -ms-flex-align: center;
         align-items: center;
     }

     .page-title {
         padding: 0;
         margin: 0;
         font-size: 1.75rem;
         font-weight: 300;
     }

     .brc-default-l1 {
         border-color: #dce9f0 !important;
     }

     .ml-n1,
     .mx-n1 {
         margin-left: -.25rem !important;
     }

     .mr-n1,
     .mx-n1 {
         margin-right: -.25rem !important;
     }

     .mb-4,
     .my-4 {
         margin-bottom: 1.5rem !important;
     }

     hr {
         margin-top: 1rem;
         margin-bottom: 1rem;
         border: 0;
         border-top: 1px solid rgba(0, 0, 0, .1);
     }

     .text-grey-m2 {
         color: #888a8d !important;
     }

     .text-success-m2 {
         color: #86bd68 !important;
     }

     .font-bolder,
     .text-600 {
         font-weight: 600 !important;
     }

     .text-110 {
         font-size: 100% !important;
     }

     .text-blue {
         color: #478fcc !important;
     }

     .pb-25,
     .py-25 {
         padding-bottom: .75rem !important;
     }

     .pt-25,
     .py-25 {
         padding-top: .75rem !important;
     }

     .bgc-default-tp1 {
         background-color: rgba(121, 169, 197, .92) !important;
     }

     .bgc-default-l4,
     .bgc-h-default-l4:hover {
         background-color: #f3f8fa !important;
     }

     .page-header .page-tools {
         -ms-flex-item-align: end;
         align-self: flex-end;
     }

     .btn-light {
         color: #757984;
         background-color: #f5f6f9;
         border-color: #dddfe4;
     }

     .w-2 {
         width: 1rem;
     }

     .text-120 {
         font-size: 110% !important;
     }

     .text-primary-m1 {
         color: #4087d4 !important;
     }

     .text-danger-m1 {
         color: #dd4949 !important;
     }

     .text-blue-m2 {
         color: #68a3d5 !important;
     }

     .text-150 {
         font-size: 150% !important;
     }

     .text-60 {
         font-size: 60% !important;
     }

     .text-grey-m1 {
         color: #7b7d81 !important;
     }

     .align-bottom {
         vertical-align: bottom !important;
     }

     #itemlist td {
         padding: 6px 15px;
     }
 </style>
 <script>
     function printcontent() {
         $(".remove").hide()
         $(".buttons").hide()
         var printContents = document.getElementById('PrintOrder').innerHTML;
         var originalContents = document.body.innerHTML;

         document.body.innerHTML = printContents;

         window.print();

         document.body.innerHTML = originalContents;
     }
 </script>
