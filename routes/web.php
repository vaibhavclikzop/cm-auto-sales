<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AjaxCall;
use App\Http\Controllers\Authentication;
use App\Http\Controllers\BulkImport;
use App\Http\Controllers\eInvoice\EInvoiceController;
use App\Http\Controllers\InwardStock;
use App\Http\Controllers\Masters;
use App\Http\Controllers\OrderManagement;
use App\Http\Controllers\OutwardStock;
use App\Http\Controllers\PurchaseOrder;
use App\Http\Controllers\PurchaseReturn;
use App\Http\Controllers\Reports;
use App\Http\Controllers\SaleReturn;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;
use App\Http\Controllers\LeadManagement;
use App\Http\Controllers\meetingController;
use App\Http\Controllers\meetingManagement;
use App\Http\Controllers\saleReport;
use App\Http\Controllers\sales\leadAppController;
use App\Http\Controllers\sales\saleAppController;
use App\Http\Controllers\sendEmail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/




Route::get('/', [Authentication::class, 'SuperAdmin'])->name('/');
Route::post('/', [Authentication::class, 'SuperAdminLogin'])->name('SuperAdminLogin');

Route::group(['middleware' => ['SuperAdmin']], function () {
  Route::post('generateEInvoice', [EInvoiceController::class, 'generateEInvoice'])->name('generateEInvoice');



  Route::get('team-hierarchy', [Masters::class, 'TeamHierarchy'])->name('team-hierarchy');

  Route::post('updateActivePanel', [Admin::class, 'updateActivePanel'])->name('updateActivePanel');
  Route::get('settings', [Masters::class, 'settings'])->name('settings');
  Route::post('SaveSettings', [Masters::class, 'SaveSettings'])->name('SaveSettings');
  Route::get('logout', [Authentication::class, 'logout'])->name('Logout');
  Route::get('dashboard', [Admin::class, 'Dashboard'])->name('dashboard');
  Route::get('profile', [Admin::class, 'Profile'])->name('profile');
  Route::post('SaveProfile', [Admin::class, 'SaveProfile'])->name('SaveProfile');
  // ajax call
  Route::post('GetCity', [Masters::class, 'GetCity'])->name('GetCity');
  Route::post('GetCategory', [Masters::class, 'GetCategory'])->name('GetCategory');
  Route::post('GetSubCategory', [Masters::class, 'GetSubCategory'])->name('GetSubCategory');
  Route::post('GetProducts', [Masters::class, 'GetProducts'])->name('GetProducts');
  Route::post('GetProducts1', [Masters::class, 'GetProducts1'])->name('GetProducts1');
  Route::post('GetVendorProducts', [AjaxCall::class, 'GetVendorProducts'])->name('GetVendorProducts');
  Route::post('GetPO', [AjaxCall::class, 'GetPO'])->name('GetPO');
  Route::post('GetPODet', [AjaxCall::class, 'GetPODet'])->name('GetPODet');
  Route::post('GetCustomerOrder', [AjaxCall::class, 'GetCustomerOrder'])->name('GetCustomerOrder');
  Route::post('GetOrderDet', [AjaxCall::class, 'GetOrderDet'])->name('GetOrderDet');
  Route::post('getSpecialOffer', [AjaxCall::class, 'getSpecialOffer'])->name('getSpecialOffer');
  Route::post('getLocation', [AjaxCall::class, 'getLocation'])->name('getLocation');






  // master route
  Route::get('company', [Masters::class, 'Company'])->name('company');
  Route::post('SaveCompany', [Masters::class, 'SaveCompany'])->name('SaveCompany');

  Route::get('customers', [Masters::class, 'Customers'])->name('customers');
  Route::post('SaveCustomer', [Masters::class, 'SaveCustomer'])->name('SaveCustomer');

  Route::get('vendor-type', [Masters::class, 'VendorType'])->name('vendor-type');
  Route::post('SaveVendorType', [Masters::class, 'SaveVendorType'])->name('SaveVendorType');


  Route::get('vendor', [Masters::class, 'Vendor'])->name('vendor');
  Route::post('SaveVendor', [Masters::class, 'SaveVendor'])->name('SaveVendor');

  Route::get('vendor-product/{id}', [Masters::class, 'VendorProduct'])->name('vendor-product');
  Route::post('AllocateProduct', [Masters::class, 'AllocateProduct'])->name('AllocateProduct');

  Route::get('store-location', [Masters::class, 'StoreLocation'])->name('store-location');
  Route::post('SaveStoreLocation', [Masters::class, 'SaveStoreLocation'])->name('SaveStoreLocation');

  Route::get('unit-type', [Masters::class, 'UnitType'])->name('unit-type');
  Route::post('SaveUnitType', [Masters::class, 'SaveUnitType'])->name('SaveUnitType');


  Route::get('brand', [Masters::class, 'Brand'])->name('brand');
  Route::post('SaveBrand', [Masters::class, 'SaveBrand'])->name('SaveBrand');



  Route::get('category', [Masters::class, 'Category'])->name('category');
  Route::post('SaveCategory', [Masters::class, 'SaveCategory'])->name('SaveCategory');



  Route::get('sub-category', [Masters::class, 'SubCategory'])->name('sub-category');
  Route::post('SaveSubCategory', [Masters::class, 'SaveSubCategory'])->name('SaveSubCategory');


  Route::get('products', [Masters::class, 'Product'])->name('products');
  Route::post('SaveProduct', [Masters::class, 'SaveProduct'])->name('SaveProduct');
  Route::get('gst', [Masters::class, 'Gst'])->name('gst');
  Route::post('SaveGst', [Masters::class, 'SaveGst'])->name('SaveGst');
  Route::get('mode-of-transport', [Masters::class, 'modeOfTransport'])->name('mode-of-transport');
  Route::post('saveModeOfTransport', [Masters::class, 'saveModeOfTransport'])->name('saveModeOfTransport');


  Route::get('warehouse', [Masters::class, 'warehouse'])->name('warehouse');
  Route::post('saveWarehouse', [Masters::class, 'saveWarehouse'])->name('saveWarehouse');
  Route::post('getWarehouse', [Masters::class, 'getWarehouse'])->name('getWarehouse');


  Route::get('customer-type', [Masters::class, 'customerType'])->name('customer-type');
  Route::post('saveCustomerType', [Masters::class, 'saveCustomerType'])->name('saveCustomerType');
  Route::get('customer-type-price-list/{id}', [Masters::class, 'customerTypePriceList'])->name('customer-type-price-list');
  Route::post('updateCustomerPrice', [Masters::class, 'updateCustomerPrice'])->name('updateCustomerPrice');
  Route::post('updateCustomerPrice0', [Masters::class, 'updateCustomerPrice0'])->name('updateCustomerPrice0');

  Route::get('special-offer', [Masters::class, 'specialOffer'])->name('special-offer');
  Route::post('saveSpecialOffer', [Masters::class, 'saveSpecialOffer'])->name('saveSpecialOffer');
  Route::post('deleteSpecialOffer', [Masters::class, 'deleteSpecialOffer'])->name('deleteSpecialOffer');
  Route::post('updateProductLocation', [Masters::class, 'updateProductLocation'])->name('updateProductLocation');





  //order routes
  Route::get('new-order', [OrderManagement::class, 'NewOrder'])->name('new-order');
  Route::post('UploadRequirementList', [OrderManagement::class, 'UploadRequirementList'])->name('UploadRequirementList');
  Route::post('UploadPORequirementList', [OrderManagement::class, 'UploadPORequirementList'])->name('UploadPORequirementList');



  Route::post('SaveNewOrder', [OrderManagement::class, 'SaveNewOrder'])->name('SaveNewOrder');

  Route::get('orders', [OrderManagement::class, 'Orders'])->name('orders');
  Route::post('InitiateOrder', [OrderManagement::class, 'InitiateOrder'])->name('InitiateOrder');
  Route::get('order-view/{id}', [OrderManagement::class, 'OrderView'])->name('order-view');
  Route::get('pi-order-view/{id}', [OrderManagement::class, 'piOrderView'])->name('pi-order-view');
  Route::post('getCustomer', [OrderManagement::class, 'getCustomer'])->name('getCustomer');
  Route::post('cancelOrder', [OrderManagement::class, 'cancelOrder'])->name('cancelOrder');
  Route::post('scrapOrder', [OrderManagement::class, 'scrapOrder'])->name('scrapOrder');

  // PO routes
  Route::get('generate-po', [PurchaseOrder::class, 'GeneratePo'])->name('generate-po');
  Route::post('SavePO', [PurchaseOrder::class, 'SavePO'])->name('SavePO');
  Route::get('purchase-order/{status}', [PurchaseOrder::class, 'PurchaseOrder'])->name('purchase-order');
  Route::post('SaveGeneratePO', [PurchaseOrder::class, 'SaveGeneratePO'])->name('SaveGeneratePO');
  Route::get('purchase-order-view/{id}', [PurchaseOrder::class, 'PurchaseOrderView'])->name('purchase-order-view');
  Route::post('UpdateCharges', [PurchaseOrder::class, 'UpdateCharges'])->name('UpdateCharges');
  Route::post('DeletePOProduct', [PurchaseOrder::class, 'DeletePOProduct'])->name('DeletePOProduct');
  Route::post('SavePOProduct', [PurchaseOrder::class, 'SavePOProduct'])->name('SavePOProduct');
  Route::post('deletePO', [PurchaseOrder::class, 'deletePO'])->name('deletePO');


  // inward stock routes
  Route::get('inward-stock', [InwardStock::class, 'InwardStock'])->name('inward-stock');
  Route::post('SaveInwardStock', [InwardStock::class, 'SaveInwardStock'])->name('SaveInwardStock');

  Route::get('inward-report', [InwardStock::class, 'InwardReport'])->name('inward-report');
  Route::get('inward-report-view/{id}', [InwardStock::class, 'InwardReportView'])->name('inward-report-view');
  Route::post('deleteStockInward', [InwardStock::class, 'deleteStockInward'])->name('deleteStockInward');
Route::post('/update-stock-inward',[InwardStock::class,'updateStockInward'])->name('updateStockInward');

  Route::get('inward-product-wise', [InwardStock::class, 'inwardProductWise'])->name('inward-product-wise');
  // outward stock routes

  Route::get('outward-stock', [OutwardStock::class, 'OutwardStock'])->name('outward-stock');
  Route::post('SaveOutward', [OutwardStock::class, 'SaveOutward'])->name('SaveOutward');
  Route::get('outward-order-list', [OutwardStock::class, 'OutwardOrderList'])->name('outward-order-list');
  Route::post('DispatchChallan', [OutwardStock::class, 'DispatchChallan'])->name('DispatchChallan');
  Route::post('DeliveredChallan', [OutwardStock::class, 'DeliveredChallan'])->name('DeliveredChallan');

  Route::get('outward-challan-view/{id}', [OutwardStock::class, 'OutwardChallanView'])->name('outward-challan-view');
  Route::post('convertToInvoice', [OutwardStock::class, 'convertToInvoice'])->name('convertToInvoice');
  Route::get('invoices', [OutwardStock::class, 'invoices'])->name('invoices');
  Route::get('download-invoice/{id}', [OutwardStock::class, 'downloadInvoice'])->name('downloadInvoice');



  Route::get('invoice-view/{id}', [OutwardStock::class, 'invoiceView'])->name('invoice-view');
  Route::post('cancelOutwardChallan', [OutwardStock::class, 'cancelOutwardChallan'])->name('cancelOutwardChallan');


  Route::get('dispatch-plan', [OutwardStock::class, 'dispatchPlan'])->name('dispatch-plan');
  Route::post('updateDispatchPlan', [OutwardStock::class, 'updateDispatchPlan'])->name('updateDispatchPlan');
  Route::post('finalDispatchPlan', [OutwardStock::class, 'finalDispatchPlan'])->name('finalDispatchPlan');
  Route::get('dispatch', [OutwardStock::class, 'dispatchMgmt'])->name('dispatch');
  Route::post('updateOutwardDetValue', [OutwardStock::class, 'updateOutwardDetValue'])->name('updateOutwardDetValue');

  Route::post('sendOtpSMS', [OutwardStock::class, 'sendOtpSMS'])->name('sendOtpSMS');
  Route::post('deliveredChallans', [OutwardStock::class, 'deliveredChallans'])->name('deliveredChallans');
  Route::post('uploadDispatchFile', [OutwardStock::class, 'uploadDispatchFile'])->name('uploadDispatchFile');
  Route::post('updateWithPassword', [OutwardStock::class, 'updateWithPassword'])->name('updateWithPassword');



  //report 
  Route::get('current-stock', [Reports::class, 'CurrentStock'])->name('current-stock');
  Route::post('SaveStock', [Reports::class, 'SaveStock'])->name('SaveStock');
  Route::post('GetStockAdjustmentHistory', [Reports::class, 'GetStockAdjustmentHistory'])->name('GetStockAdjustmentHistory');
  Route::get('near-by-minimum-stock', [Reports::class, 'NearMinimumStock'])->name('near-by-minimum-stock');



  Route::get('audit-setting', [Reports::class, 'AuditSetting'])->name('audit-setting');
  Route::post('SaveAuditReport', [Reports::class, 'SaveAuditReport'])->name('SaveAuditReport');

  Route::get('audit-report', [Reports::class, 'AuditReport'])->name('audit-report');
  Route::get('audit-report-view/{id}', [Reports::class, 'AuditReportView'])->name('audit-report-view');
  Route::post('SaveAudit', [Reports::class, 'SaveAudit'])->name('SaveAudit');
  Route::post('GetCSProducts', [Reports::class, 'GetCSProducts'])->name('GetCSProducts');

  Route::get('defective-stock', [Reports::class, 'DefectiveStock'])->name('defective-stock');
  Route::get('add-defective-stock', [Reports::class, 'AddDefectiveStock'])->name('add-defective-stock');
  Route::post('SaveDefectiveStock', [Reports::class, 'SaveDefectiveStock'])->name('SaveDefectiveStock');
  Route::post('GetCurrentStock', [Reports::class, 'GetCurrentStock'])->name('GetCurrentStock');


  Route::get('scrap-stock', [Reports::class, 'ScrapStock'])->name('scrap-stock');
  Route::get('add-scrap-stock', [Reports::class, 'AddScrapStock'])->name('add-scrap-stock');
  Route::post('GetGenSet', [Reports::class, 'GetGenSet'])->name('GetGenSet');
  Route::post('GetGenSetProducts', [Reports::class, 'GetGenSetProducts'])->name('GetGenSetProducts');
  Route::post('SaveScrapProducts', [Reports::class, 'SaveScrapProducts'])->name('SaveScrapProducts');
  Route::post('BulkImportCS', [Reports::class, 'BulkImportCS'])->name('BulkImportCS');

  //users mgmt
  Route::get('users', [Admin::class, 'Users'])->name('users');
  Route::get('user-role', [Admin::class, 'UserRole'])->name('user-role');
  Route::post('SaveUser', [Admin::class, 'SaveUser'])->name('SaveUser');
  Route::post('GetUserDetails', [AjaxCall::class, 'GetUserDetails'])->name('GetUserDetails');
  Route::post('SaveRole', [Admin::class, 'SaveRole'])->name('SaveRole');
  Route::get('user-permission/{id}', [Admin::class, 'UserPermission'])->name('user-permission');
  Route::post('SaveUserPermission', [Admin::class, 'SaveUserPermission'])->name('SaveUserPermission');
  Route::post('RemovePermission', [Admin::class, 'RemovePermission'])->name('RemovePermission');
  Route::post('updateActiveInventory', [Admin::class, 'updateActiveInventory'])->name('updateActiveInventory');


  //purchase return

  Route::get('purchase-return', [PurchaseReturn::class, 'PurchaseReturnList'])->name('purchase-return');
  Route::post('GetInwardChallan', [PurchaseReturn::class, 'GetInwardChallan'])->name('GetInwardChallan');
  Route::post('GetInwardChallanProducts', [PurchaseReturn::class, 'GetInwardChallanProducts'])->name('GetInwardChallanProducts');
  Route::post('SavePurchaseReturn', [PurchaseReturn::class, 'SavePurchaseReturn'])->name('SavePurchaseReturn');

  Route::get('purchase-return-challan-view/{id}', [PurchaseReturn::class, 'PurchaseReturnChallanView'])->name('purchase-return-challan-view');

  //sale return

  Route::get('sale-return', [SaleReturn::class, 'SaleReturnList'])->name('sale-return');
  Route::post('GetOutwardChallan', [SaleReturn::class, 'GetOutwardChallan'])->name('GetOutwardChallan');
  Route::post('GetOutwardChallanProducts', [SaleReturn::class, 'GetOutwardChallanProducts'])->name('GetOutwardChallanProducts');
  Route::post('SaveSaleReturn', [SaleReturn::class, 'SaveSaleReturn'])->name('SaveSaleReturn');
  Route::get('sale-return-challan-view/{id}', [SaleReturn::class, 'SaleReturnChallanView'])->name('sale-return-challan-view');


  //Lead management 
  Route::get('status', [LeadManagement::class, 'Status'])->name('status');
  Route::post('SaveStatus', [LeadManagement::class, 'SaveStatus'])->name('SaveStatus');

  Route::get('lead/{id}', [LeadManagement::class, 'Lead'])->name('lead');
  Route::post('SaveLead', [LeadManagement::class, 'SaveLead'])->name('SaveLead');
  Route::post('GetLeadDetails', [LeadManagement::class, 'GetLeadDetails'])->name('GetLeadDetails');
  Route::post('GetRemarks', [LeadManagement::class, 'GetRemarks'])->name('GetRemarks');


  // bulk import
  Route::post('ImportProducts', [BulkImport::class, 'ImportProducts'])->name('ImportProducts');
  Route::post('importSpecialOffer', [BulkImport::class, 'importSpecialOffer'])->name('importSpecialOffer');
  Route::post('supplier/ImportCustomers', [BulkImport::class, 'ImportCustomers'])->name('supplier/ImportCustomers');


  //reports 
  Route::get('po-report', [Reports::class, 'poReport'])->name('po-report');


  //email

  Route::post('sendEmail', [sendEmail::class, 'sendEmail'])->name('sendEmail');
  //meetings

  Route::get('sale-report-tally', [saleReport::class, 'saleReportTally'])->name('sale-report-tally');
  Route::get('customer-wise-sale-report', [saleReport::class, 'customerWiseSaleReport'])->name('customer-wise-sale-report');
  Route::get('product-wise-sale-report', [saleReport::class, 'productWiseSaleReport'])->name('product-wise-sale-report');
  Route::get('customer-product-report', [saleReport::class, 'customerProductReport'])->name('customer-product-report');
  Route::get('dsr-report', [saleReport::class, 'dsrReport'])->name('dsr-report');
  Route::get('order-vs-stock', [saleReport::class, 'orderVsStock'])->name('order-vs-stock');
  Route::get('slow-fast-moving-products', [saleReport::class, 'slowFastMovingProducts'])->name('slow-fast-moving-products');
  Route::get('category-wise-report', [saleReport::class, 'categoryWiseReport'])->name('category-wise-report');
  Route::get('purchase-report', [saleReport::class, 'purchaseReport'])->name('purchase-report');
  Route::get('purchase-report-product-wise', [saleReport::class, 'purchaseReportProductWise'])->name('purchase-report-product-wise');
  Route::get('purchase-return-report', [saleReport::class, 'purchaseReturnReport'])->name('purchase-return-report');
  Route::get('sale-return-report', [saleReport::class, 'saleReturnReport'])->name('sale-return-report');
  Route::get('product-ledger', [saleReport::class, 'productLedger'])->name('product-ledger');



  Route::get('sale-report', [saleReport::class, 'saleReport'])->name('sale-report');
  Route::get('order-vs-invoice', [saleReport::class, 'orderVsInvoice'])->name('order-vs-invoice');
  Route::get('in-out-report', [saleReport::class, 'inOutReport'])->name('in-out-report');

  

  Route::prefix('meetings')->group(function () {
    Route::get('/', [meetingController::class, 'index'])->name('meetings.index');
    Route::post('/', [MeetingController::class, 'store'])->name('meetings.store');
    Route::put('/{meeting}', [MeetingController::class, 'update'])->name('meetings.update');
    Route::delete('/{meeting}', [MeetingController::class, 'destroy'])->name('meetings.destroy');
    Route::put('/{meeting}/status', [MeetingController::class, 'updateStatus'])->name('meetings.update.status');
    Route::post('/{meeting}/participants', [MeetingController::class, 'addParticipant'])->name('meetings.participants.add');
    Route::delete('/participants/{participant}', [MeetingController::class, 'removeParticipant'])->name('meetings.participants.remove');
    Route::put('/participants/{participant}/status', [MeetingController::class, 'updateParticipantStatus'])->name('meetings.participants.status');
    Route::get('/{meeting}/details', [MeetingController::class, 'getMeetingDetails'])->name('meetings.details');

    Route::post('startMeeting', [MeetingController::class, 'startMeeting'])->name('meetings.startMeeting');
    Route::post('stopMeeting', [MeetingController::class, 'stopMeeting'])->name('meetings.stopMeeting');
  });
});



Route::post('/salesAppLogin', [saleAppController::class, 'salesAppLogin'])->name('salesAppLogin');
Route::get('/sales-app', [saleAppController::class, 'salesApp'])->name('sales-app');


Route::group(['middleware' => ['salesAppAuth']], function () {
  Route::get('/sales-app/dashboard', [saleAppController::class, 'dashboard'])->name('sales-app/dashboard');
  Route::get('/sales-app/ready-to-deliver', [saleAppController::class, 'readyToDeliver'])->name('sales-app/ready-to-deliver');
  Route::post('/sales-app/sendOtpSMS', [saleAppController::class, 'sendOtpSMS'])->name('sales-app/sendOtpSMS');
  Route::post('/sales-app/deliveredChallan', [saleAppController::class, 'deliveredChallan'])->name('sales-app/deliveredChallan');



  Route::get('/sales-app/logout', [saleAppController::class, 'logout'])->name('sales-app/logout');
  Route::get('/sales-app/my-profile', [saleAppController::class, 'myProfile'])->name('sales-app/my-profile');
  Route::post('/sales-app/updateProfile', [saleAppController::class, 'updateProfile'])->name('sales-app/updateProfile');
});


Route::get('/lead-app', [leadAppController::class, 'leadApp'])->name('lead-app');
Route::post('/saveLeadLogin', [leadAppController::class, 'saveLeadLogin'])->name('saveLeadLogin');





Route::group(['middleware' => ['leadAppAuth']], function () {
  Route::get('/lead-app/dashboard', [leadAppController::class, 'dashboard'])->name('lead-app/dashboard');
  Route::get('/lead-app/add-lead', [leadAppController::class, 'addLead'])->name('lead-app/add-lead');
  Route::post('lead-app.saveLead', [leadAppController::class, 'saveLead'])->name('lead-app.saveLead');
  Route::get('lead-app/leads', [leadAppController::class, 'leads'])->name('lead-app/leads');
  Route::post('lead-app/GetRemarks', [LeadManagement::class, 'GetRemarks'])->name('lead-app/GetRemarks');
  Route::post('lead-app/GetLeadDetails', [LeadManagement::class, 'GetLeadDetails'])->name('lead-app/GetLeadDetails');
  Route::post('lead-app/SaveLead', [LeadManagement::class, 'SaveLead'])->name('/lead-app/SaveLead');

  Route::get('lead-app/meetings', [meetingManagement::class, 'meetings'])->name('lead-app/meetings');

  Route::post('lead-app/SaveMeeting', [meetingManagement::class, 'SaveMeeting'])->name('/lead-app/SaveMeeting');
  Route::post('lead-app/startMeeting', [meetingManagement::class, 'startMeeting'])->name('/lead-app/startMeeting');
  Route::post('lead-app/StopMeeting', [meetingManagement::class, 'StopMeeting'])->name('/lead-app/StopMeeting');


  Route::get('lead-app/create-order', [leadAppController::class, 'createOrder'])->name('lead-app/create-order');
  Route::get('lead-app/orders', [leadAppController::class, 'orders'])->name('lead-app/orders');


  Route::post('lead-app/getCustomer', [OrderManagement::class, 'getCustomer'])->name('lead-app/getCustomer');
  Route::post('lead-app/SaveNewOrder', [OrderManagement::class, 'SaveNewOrder'])->name('lead-app/SaveNewOrder');
  Route::get('lead-app/order-view/{id}', [leadAppController::class, 'OrderView'])->name('lead-app/order-view');


  Route::get('/lead-app/my-profile', [leadAppController::class, 'myProfile'])->name('lead-app/my-profile');
  Route::get('/lead-app/logout', [leadAppController::class, 'logout'])->name('lead-app/logout');
  Route::post('leadApp/updateActiveInventory', [Admin::class, 'updateActiveInventory'])->name('leadApp/updateActiveInventory');
});
