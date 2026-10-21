<?php
defined('BASEPATH') or exit('No direct script access allowed');


//  -------------------------------------  Promo --------------------------------------------------------

// Blacklist
$route['promo/viewBlacklist']               = 'promo/blacklist/viewBlacklist';
$route['promo/updateBlacklistForm']         = 'promo/blacklist/updateBlacklistForm';
$route['promo/updateBlacklist']             = 'promo/blacklist/updateBlacklist';
$route['promo/browseBltable1']              = 'promo/blacklist/browseBltable1';
$route['promo/browseBltable2']              = 'promo/blacklist/browseBltable2';
$route['promo/addBlacklistForm']            = 'promo/blacklist/addBlacklistForm';
$route['promo/addBlacklistFormManual']      = 'promo/blacklist/addBlacklistFormManual';
$route['promo/reportedbyBl']                = 'promo/blacklist/reportedbyBl';
$route['promo/addBlacklist']                = 'promo/blacklist/addBlacklist';
// End Blacklist

// Clearance
$route['promo/clearanceList']               = 'promo/clearance/clearanceList';
$route['promo/filterYear']                  = 'promo/clearance/filterYear';
$route['promo/clearanceDetails']            = 'promo/clearance/clearanceDetails';
$route['promo/clearanceUpdateForm']         = 'promo/clearance/clearanceUpdateForm';
$route['promo/clearanceUpdate']             = 'promo/clearance/clearanceUpdate';
$route['promo/changeReason']                = 'promo/clearance/changeReason';
$route['promo/searchPromoClearance']        = 'promo/clearance/searchPromoClearance';
$route['promo/nameSearch']                  = 'promo/clearance/nameSearch';
$route['promo/reprintClearance']            = 'promo/clearance/reprintClearance';
$route['promo/selectPromoClearance']        = 'promo/clearance/selectPromoClearance';
$route['promo/secureClearance']             = 'promo/clearance/secureClearance';
$route['promo/clearanceRequirement']        = 'promo/clearance/clearanceRequirement';
$route['promo/browseEpas']                  = 'promo/clearance/browseEpas';
$route['promo/uploadClearance']             = 'promo/clearance/uploadClearance';
// End Clearance

// Contract
$route['promo/searchPromoContract']         = 'promo/contract/searchPromoContract';
$route['promo/selectPromoContract']         = 'promo/contract/selectPromoContract';
$route['promo/generatePermitForm']          = 'promo/contract/generatePermitForm';
$route['promo/generateContractForm']        = 'promo/contract/generateContractForm';
$route['promo/savePermit']                  = 'promo/contract/savePermit';
$route['promo/saveContract']                = 'promo/contract/saveContract';
$route['promo/searhContractWitness']        = 'promo/contract/searhContractWitness';
$route['promo/eocList']                     = 'promo/contract/eocList';
$route['promo/viewAppraisal']               = 'promo/contract/viewAppraisal';
$route['promo/checkRenewalReq']             = 'promo/contract/checkRenewalReq';
$route['promo/uploadClearanceForm']         = 'promo/contract/uploadClearanceForm';
$route['promo/uploadClearanceRenewal']      = 'promo/contract/uploadClearanceRenewal';
$route['promo/getSelectRenewal']            = 'promo/contract/getSelectRenewal';
$route['promo/renewContract']               = 'promo/contract/renewContract';
$route['promo/setSession']                  = 'promo/contract/setSession';
$route['promo/transferRateForm']            = 'promo/contract/transferRateForm';
$route['promo/checkStores']                 = 'promo/contract/checkStores';
$route['promo/transferRateSave']            = 'promo/contract/transferRateSave';
$route['promo/contractsDetails']            = 'promo/contract/contractsDetails';
// End Contract

// Profile
$route['promo/profilePic']                  = 'promo/profile/profilePic';
$route['promo/profileData']                 = 'promo/profile/profileData';
$route['promo/basicInfo']                   = 'promo/profile/basicInfo';
$route['promo/contactInfo']                 = 'promo/profile/contactInfo';
$route['promo/famEducBackground']           = 'promo/profile/famEducBackground';
$route['promo/modal_form']                  = 'promo/profile/modal_form';
$route['promo/save_modal_form']             = 'promo/profile/save_modal_form';
$route['promo/viewExam']                    = 'promo/profile/viewExam';
$route['promo/viewExam_history']            = 'promo/profile/viewExam_history';
$route['promo/viewApp_details']             = 'promo/profile/viewApp_details';
$route['promo/viewInt_details']             = 'promo/profile/viewInt_details';
$route['promo/appHistory']                  = 'promo/profile/appHistory';
$route['promo/viewContract']                = 'promo/profile/viewContract';
$route['promo/editContract']                = 'promo/profile/editContract';
$route['promo/contractFile']                = 'promo/profile/contractFile';
$route['promo/getSelect']                   = 'promo/profile/getSelect';
$route['promo/save_editContract']           = 'promo/profile/save_editContract';
$route['promo/save_uploadContract']         = 'promo/profile/save_uploadContract';
$route['promo/benefits']                    = 'promo/profile/benefits';
$route['promo/supervisorList']              = 'promo/profile/supervisorList';
$route['promo/save_supervisor_form']        = 'promo/profile/save_supervisor_form';
$route['promo/docFile_view']                = 'promo/profile/docFile_view';
$route['promo/docFile_uploadForm']          = 'promo/profile/docFile_uploadForm';
$route['promo/docFile_upload']              = 'promo/profile/docFile_upload';
$route['promo/remarks']                     = 'promo/profile/remarks';
$route['promo/addtoExtend']                 = 'promo/profile/addtoExtend';
// End Profile

// Outlet
$route['promo/changeOutletHistory']         = 'promo/outlet/changeOutletHistory';
$route['promo/searchPromoOutlet']           = 'promo/outlet/searchPromoOutlet';
$route['promo/selectPromoOutlet']           = 'promo/outlet/selectPromoOutlet';
$route['promo/changeOutletDetails']         = 'promo/outlet/changeOutletDetails';
$route['promo/getSelectOutlet']             = 'promo/outlet/getSelectOutlet';
$route['promo/addOutlet']                   = 'promo/outlet/addOutlet';
$route['promo/removeOutlet']                = 'promo/outlet/removeOutlet';
$route['promo/transferOutlet']              = 'promo/outlet/transferOutlet';
// End Outlet

// Promo
$route['promo/masterfile']                  = 'promo/promo/masterfile';
$route['promo/searchApplicant']             = 'promo/promo/searchApplicant';
$route['promo/searchTagtoRecruitment']      = 'promo/promo/searchTagtoRecruitment';
$route['promo/tagToRecruitment']            = 'promo/promo/tagToRecruitment';
$route['promo/tagToRecruitment']            = 'promo/promo/tagToRecruitment';
$route['promo/getSeverData']                = 'promo/promo/getSeverData';
$route['promo/updateSeverData']             = 'promo/promo/updateSeverData';
$route['promo/searchPromoExport']           = 'promo/promo/searchPromoExport';
$route['promo/selectPromoExport']           = 'promo/promo/selectPromoExport';
$route['promo/exportPromoData']             = 'promo/promo/exportPromoData';
// End Promo

// Reports
$route['promo/generateQbe']                 = 'promo/reports/generateQbe';
$route['promo/generatePromoStat']           = 'promo/reports/generatePromoStat';
$route['promo/generateMonthlyStat']         = 'promo/reports/generateMonthlyStat';
$route['promo/generateAnnualStat']          = 'promo/reports/generateAnnualStat';
$route['promo/contractUploadMonitoring']    = 'promo/reports/contractUploadMonitoring';
$route['promo/generateDueContractsExcel']   = 'promo/reports/generateDueContractsExcel';
$route['promo/generateDutySched']           = 'promo/reports/generateDutySched';
$route['promo/dutySchedList']               = 'promo/reports/dutySchedList';
$route['promo/dutySchedListData']           = 'promo/reports/dutySchedListData';
$route['promo/generateTermRepExcel']        = 'promo/reports/generateTermRepExcel';
$route['promo/termRepList']                 = 'promo/reports/termRepList';
$route['promo/newPromo']                    = 'promo/reports/newPromo';
$route['promo/promoStat']                   = 'promo/reports/promoStat';
$route['promo/generateStatRep']             = 'promo/reports/generateStatRep';
$route['promo/failedEpas']                  = 'promo/reports/failedEpas';
// End Reports

// Resignation
$route['promo/searchPromoResignation']      = 'promo/resignation/searchPromoResignation';
$route['promo/selectPromoResignation']      = 'promo/resignation/selectPromoResignation';
$route['promo/addResignationForm']          = 'promo/resignation/addResignationForm';
$route['promo/addResignation']              = 'promo/resignation/addResignation';
$route['promo/tagResignationTable']         = 'promo/resignation/tagResignationTable';
$route['promo/tagResignation']              = 'promo/resignation/tagResignation';
$route['promo/resignationList']             = 'promo/resignation/resignationList';
$route['promo/uploadLetterForm']            = 'promo/resignation/uploadLetterForm';
$route['promo/uploadLetter']                = 'promo/resignation/uploadLetter';
// End Resignation

// Setup
$route['promo/updateForm']                  = 'promo/setup/updateForm';
$route['promo/agencyList']                  = 'promo/setup/agencyList';
$route['promo/companyAgency']               = 'promo/setup/companyAgency';
$route['promo/agencyComps']                 = 'promo/setup/agencyComps';
$route['promo/productCompany']              = 'promo/setup/productCompany';
$route['promo/updateCompanyAgency']         = 'promo/setup/updateCompanyAgency';
$route['promo/updateProductCompany']        = 'promo/setup/updateProductCompany';
$route['promo/compProducts']                = 'promo/setup/compProducts';
$route['promo/companyList']                 = 'promo/setup/companyList';
$route['promo/productList']                 = 'promo/setup/productList';
$route['promo/updateAgency']                = 'promo/setup/updateAgency';
$route['promo/updateCompany']               = 'promo/setup/updateCompany';
$route['promo/updateProduct']               = 'promo/setup/updateProduct';
$route['promo/departmentList']              = 'promo/setup/departmentList';
$route['promo/deptStores']                  = 'promo/setup/deptStores';
$route['promo/updateDepartment']            = 'promo/setup/updateDepartment';
$route['promo/buList']                      = 'promo/setup/buList';
$route['promo/updateBu']                    = 'promo/setup/updateBu';
$route['promo/searchPromoSetup']            = 'promo/setup/searchPromoSetup';
$route['promo/selectPromoSetup']            = 'promo/setup/selectPromoSetup';
$route['promo/subordinates']                = 'promo/setup/subordinates';
$route['promo/promoList']                   = 'promo/setup/promoList';
$route['promo/updateSubordinates']          = 'promo/setup/updateSubordinates';
// End Setup

// User
$route['promo/searchPromoUsers']            = 'promo/user/searchPromoUsers';
$route['promo/selectPromoUsers']            = 'promo/user/selectPromoUsers';
$route['promo/save_userAccount']            = 'promo/user/save_userAccount';
$route['promo/promoUserAccessRoles']        = 'promo/user/promoUserAccessRoles';
$route['promo/accessRoles']                 = 'promo/user/accessRoles';
$route['promo/managePromoUserAccounts']     = 'promo/user/managePromoUserAccounts';
$route['promo/userAccount']                 = 'promo/user/userAccount';
$route['promo/managePromoInchargeAccounts'] = 'promo/user/managePromoInchargeAccounts';
$route['promo/updatePromoInchargeAccounts'] = 'promo/user/updatePromoInchargeAccounts';
$route['promo/addPromoInchargeAccount']     = 'promo/user/addPromoInchargeAccount';
$route['promo/updateUserAccount']           = 'promo/user/updateUserAccount';
$route['promo/formUserAccess']              = 'promo/user/formUserAccess';
$route['promo/updateMainMenu']              = 'promo/user/updateMainMenu';
$route['promo/updateSubMenu']               = 'promo/user/updateSubMenu';

// End User

// Utility
$route['promo/logs']                        = 'promo/logs/logs';
$route['promo/logsAdmin']                   = 'promo/logs/logsAdmin';
$route['promo/logsSearch']                  = 'promo/logs/logsSearch';
// End Utility

// Pdf
$route['promo/generateClearance']           = 'promo/pdf/generateClearance';
$route['promo/generateContract']            = 'promo/pdf/generateContract';
$route['promo/generatePermit']              = 'promo/pdf/generatePermit';
$route['promo/viewPdf']                     = 'promo/pdf/viewPdf';
$route['promo/generateDueContractsPDF']     = 'promo/pdf/generateDueContractsPDF';
$route['promo/generateTermRepPdf']          = 'promo/pdf/generateTermRepPdf';
$route['promo/generateTermContract']        = 'promo/pdf/generateTermContract';
$route['promo/viewPdfClearance']            = 'promo/pdf/viewPdfClearance';
// End Pdf

// Page
$route['promo']                             = 'promo/page/menu';
$route['promo/logout']                      = 'promo/page/logout';
$route['promo/page/(:any)/(:any)']          = 'promo/page/menu/$1/$2';
$route['promo/page/(:any)/(:any)/(:any)']   = 'promo/page/menu/$1/$2/$3';
// End Page

// Guest
$route['guest']                             = 'guest/menu';
$route['guest/(:any)/(:any)']               = 'guest/menu/$1/$2';
$route['guest/(:any)/(:any)/(:any)']        = 'guest/menu/$1/$2/$3';
// End Guest

//  -------------------------------------  END Promo ----------------------------------------------------


$route['placement/dashboard']                       = 'placement/dashboard';


$route['default_controller']                        = 'login';
$route['404_override']                              = 'pagenotfound';
$route['translate_uri_dashes']                      = FALSE;
