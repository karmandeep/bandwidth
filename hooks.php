<?php
/**
 * WHMCS SDK Sample Addon Module Hooks File
 *
 * Hooks allow you to tie into events that occur within the WHMCS application.
 *
 * This allows you to execute your own code in addition to, or sometimes even
 * instead of that which WHMCS executes by default.
 *
 * @see https://developers.whmcs.com/hooks/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */
require_once __DIR__ . '/observium_class.php';

// Require any libraries needed for the module to function.
// require_once __DIR__ . '/path/to/library/loader.php';
//
// Also, perform any initialization required by the service's library.
use WHMCS\Module\Addon\Bandwidth\Observium_class;
use WHMCS\View\Menu\Item as MenuItem;

/**
 * Register a hook with WHMCS.
 *
 * This sample demonstrates triggering a service call when a change is made to
 * a client profile within WHMCS.
 *
 * For more information, please refer to https://developers.whmcs.com/hooks/
 *
 * add_hook(string $hookPointName, int $priority, string|array|Closure $function)
 */
add_hook('ClientEdit', 1, function(array $params) {
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
    } catch (Exception $e) {
        // Consider logging or reporting the error.
    }
});

add_hook('ClientAreaPrimarySidebar', 1, function($menu) {	
	 
	 if(isset($_GET['id'])):
	 
		 $hosting_id = $_GET['id'];
		 //Set the ID
		 //Get the Product ID from service ID
		 $check_group_query = full_query("select count(*) as result from tblproducts, tblhosting, mod_observium 
		 								where tblhosting.packageid = tblproducts.id and tblhosting.id = '" . $hosting_id . "'
										and tblproducts.gid = mod_observium.gid;");
										
		 $group_check = mysql_fetch_array($check_group_query , MYSQL_ASSOC);
		 
		 if($group_check['result']):
		 
			 if (!is_null($menu->getChild('Service Details Overview'))) {
				// Add a link to the module filter.
				$menu->getChild('Service Details Overview')
					->addChild(
						'Bandwidth usage',
						array(
							'uri' => 'index.php?m=bandwidth&id='.$hosting_id,
							'order' => 15,
						)
					);
			}
		endif; //END Grouop Check
		
	endif; //END Isset ID
});
//ClientEdit
add_hook('PreCronJob', 1, function($vars) {
    // Perform hook code here...
	logActivity('Observium CronJob: 1. Calling Invoice function: runOverageInvoces() on PreCronJob ', 0);
	runOverageInvoces();
	
	logActivity('Observium CronJob: 1 Send Overage Emails sendOversageEmails() on PreCronJob ', 0);
	sendOversageEmails();	
	
});

/*add_hook('ClientAreaPage', 1, function($vars) {
    // Perform hook code here...
	runOverageInvoces();
});*/


add_hook('DailyCronJob', 1, function($vars) {
    // Perform hook code here...
	logActivity('Observium CronJob: 1. Calling Invoice function: runOverageInvoces() on DailyCronJob ', 0);
	runOverageInvoces();

	logActivity('Observium CronJob: 1 Send Overage Emails sendOversageEmails() on DailyCronJob ', 0);
	sendOversageEmails();	
	
});

/*add_hook('AdminAreaPage', 1, function($vars) {
	runOverageInvoces();
});*/

add_hook('AfterCronJob', 1, function($vars) {
	
//add_hook('ClientAreaPage', 1, function($vars) {
    // Perform hook code here...
//	try {
		//Get Today's Date Y-m-d
		
	logActivity('Observium CronJob: 1. Calling Invoice function: runOverageInvoces() on AfterCronJob ', 0);	
	runOverageInvoces();	
			 // `$params`.
//    } catch (Exception $e) {
        // Consider logging or reporting the error.
 //   }

	logActivity('Observium CronJob: 1 Send Overage Emails sendOversageEmails() on AfterCronJob ', 0);
	sendOversageEmails();	
 
});


function runOverageInvoces() {
	
		
		logActivity('Observium CronJob: 2. Invoice function: runOverageInvoces() ', 0);	

		$right_now = Carbon\Carbon::now()->timestamp; 
		$today = Carbon\Carbon::now()->format('Y-m-d');
		
		$service_list = [];
		//Check if the product group is applicable to observium
		//Get the Services which are expring today.
		$services_query = full_query("select tblhosting.*, unix_timestamp(tblhosting.nextduedate) as stampdate, tblproducts.gid from tblhosting , tblproducts, mod_observium where tblhosting.packageid = tblproducts.id and mod_observium.gid = tblproducts.gid and '" . $right_now . "' > unix_timestamp(tblhosting.nextduedate)");
		
		if(mysql_num_rows($services_query) > 0):
			
			logActivity('Observium CronJob: 3. Services with billing cycle ending Found in Function runOverageInvoces() ', 0);	
			
			while($services = mysql_fetch_array($services_query , MYSQL_ASSOC)) {
		
					$hosting_id = $services['id'];
					//bill_id			
					$bill_id_query = full_query("select tblcustomfieldsvalues.value as bill_id from tblcustomfieldsvalues , tblcustomfields, tblhosting 
												where tblcustomfields.type = 'product' 
												and tblcustomfields.relid = tblhosting.packageid and tblcustomfields.id = tblcustomfieldsvalues.fieldid 
												and tblcustomfieldsvalues.relid = tblhosting.id and tblcustomfields.fieldname = 'bill_id' and tblhosting.id = '" . $hosting_id . "'");
								
								
					$bill_id_array = mysql_fetch_array($bill_id_query , MYSQL_ASSOC);
					//$bill_id = $bill_id_array['bill_id'];		
					
					//overage_cost
					$overage_cost_query = full_query("select tblcustomfieldsvalues.value as overage_cost from tblcustomfieldsvalues , tblcustomfields, tblhosting 
												where tblcustomfields.type = 'product' 
												and tblcustomfields.relid = tblhosting.packageid and tblcustomfields.id = tblcustomfieldsvalues.fieldid 
												and tblcustomfieldsvalues.relid = tblhosting.id and tblcustomfields.fieldname = 'overage_cost' and tblhosting.id = '" . $hosting_id . "'");
								
								
					$overage_cost_array = mysql_fetch_array($overage_cost_query , MYSQL_ASSOC);
					
					//Bill_id and ovarage_price MERGE
					$service_list[] = array_merge(['bill_id' => $bill_id_array['bill_id'] ] , ['overage_cost' => $overage_cost_array['overage_cost']] , $services);	
			
			}
			
			$observium = new Observium_class();
			$vars = [];
			$addons_query = select_query("tbladdonmodules" , 'setting , value' , ['module' => 'bandwidth']);
			while($addons_array = mysql_fetch_array($addons_query , MYSQL_ASSOC)) {
				$vars[$addons_array['setting']] = $addons_array['value'];
			}
			
			$observium->init($vars);
		
			logActivity('Observium CronJob: 4. API initilization in Function runOverageInvoces() ', 0);	

			$bills = $observium->request(['cmd' => 'bills/']);
			
			$excessDefaultRate = $vars['excessDefault'];
			
			
			if($bills->status == 'ok'):
			
				logActivity('Observium CronJob: 5. API Status Ok runOverageInvoces() ', 0);	

				foreach($service_list as $key => $value):
				
					if(!empty($value['bill_id'])) {
						
						logActivity('Observium CronJob: 6. Bill ID ' . $value['bill_id'] . ' Found in runOverageInvoces() ', 0);	

						if(is_object($bills->bill)):	
							$bill = $bills->bill->$value['bill_id'];
							$excess_usage = 0;
					
							//Hide Them Once Testing is DONE
							//$bill->total_data = 20080000000000;
							//$bill->bill_quota = 20000000000000;
							if($bill->total_data > $bill->bill_quota):
								$excess_usage = ($bill->total_data - $bill->bill_quota);
								logActivity('Observium CronJob: 7. Excess usage ' . $excess_usage . ' in runOverageInvoces() ', 0);	
							else:
								logActivity('Observium CronJob: 7. Excess usage not Found Moving to Next in runOverageInvoces() ', 0);
							endif;
							
							if($excess_usage > 0):
								

								//$invoice_due_query = select_query("tblconfiguration" , "value" , ['setting' => 'CreateInvoiceDaysBefore']);
								//$invoice_due_array = mysql_fetch_array($invoice_query , MYSQL_ASSOC);
								//mod_observium_invoices
								//Check if there is invoice already generated.
								$present_invoice_query = full_query("select count(invoice_id) as invoices from mod_observium_invoices where unix_timestamp(start) <= '" . $right_now . "' and unix_timestamp(end) >= '" . $right_now . "' and bill_id = '" . $value['bill_id'] ."' and service_id = '" . $value['id'] ."'");
								$present_invoice = mysql_fetch_array($present_invoice_query , MYSQL_ASSOC);
								
								if(!$present_invoice['invoices']) {
									
									logActivity('Observium CronJob: 8. Generating Invoice in runOverageInvoces() ', 0);	

									//Generate the WHMCS Invoice 
									if(!empty($value['overage_cost'])):
										$excessRate = $value['overage_cost'];
									else:
										$excessRate = $vars['excessDefault'];
									endif;
									
									$excess_usage_charges = $excess_usage/1000000000000 * $excessRate;
				
									$command = 'CreateInvoice';
									$postData = array(
										'userid' => $value['userid'],
										'status' => 'Unpaid',
										'sendinvoice' => '1',
										'paymentmethod' => $value['paymentmethod'],
										//'taxrate' => '10.00',
										'date' => $today,
										'duedate' => $today,
										'itemdescription1' => 'Excess bandwidth usage - ' . $value['domain'] . "\r\n" . 'Usage: ' . number_format(($bill->total_data/1000000000000),2) . ' TB ' . "\r\n" . 'Commitment: ' . ($bill->bill_quota/1000000000000) . ' TB ' . "\r\n" . 'Excess: ' . number_format(($excess_usage/1000000000000),2) . ' TB ' . "\r\n" . 'Overage rate: ' . number_format($excessRate,2),
										'itemamount1' => $excess_usage_charges,
										'itemtaxed1' => '0',
										
									);
									//$adminUsername = 'ADMIN_USERNAME'; // Optional for WHMCS 7.2 and later
									
									$start = $today;
									$end = new \Carbon\Carbon($today); 
									
									switch($value['billingcycle']) {
										
										case 'Monthly':
											$end->addMonth();
										break;
										
										case 'Quarterly':
											$end->addMonths(3);
										break;
										
										case 'Semi-Annually':
											$end->addMonths(6);
										break;
						
										case 'Annually':
											$end->addYear();
										break;
						
										case 'Biennially':
											$end->addYears(2);
										break;
						
										case 'Triennially':
											$end->addYears(3);
										break;
										
									}
									
									$end = $end->format('Y-m-d');
									
									//Generate the invoice
									$results = localAPI($command, $postData);
									
									if($results['result'] === 'success'):
									
										logActivity('Observium CronJob: 9. Invoice: ' . $results['invoiceid'] . ' Generated in runOverageInvoces() ', 0);	
										insert_query("mod_observium_invoices" , ['invoice_id' => $results['invoiceid'], 'bill_id' => $value['bill_id'], 'service_id' => $value['id'], 'start' => $start, 'end' => $end]);
										
									endif;
								} else {
									
									logActivity('Observium CronJob: 8. Invoice ID:  ' . $present_invoice['invoices'] . ' Already generated in runOverageInvoces() ', 0);	
									logActivity('Observium CronJob: 9. No Action in runOverageInvoces() ', 0);	

								}
								
								

								
							endif;
						endif;
					}
				endforeach;
			endif;

		endif;
	
		logActivity('Observium CronJob: 10. Complete in runOverageInvoces() ', 0);	

	
}


function sendOversageEmails() {

		$right_now = Carbon\Carbon::now()->timestamp; 
		$today = Carbon\Carbon::now()->format('Y-m-d');
		
		logActivity('Observium CronJob: 2. Select Hosting function: sendOversageEmails() ', 0);
		//Get the Group of Hosting ID
		$service_list = [];
		//$services_query = full_query("select tblhosting.*, unix_timestamp(tblhosting.nextduedate) as stampdate, tblproducts.gid from tblhosting , tblproducts, mod_observium where tblhosting.packageid = tblproducts.id and mod_observium.gid = tblproducts.gid and unix_timestamp(tblhosting.nextduedate) > '" . $right_now . "'");
		$services_query = full_query("select tblhosting.*, unix_timestamp(tblhosting.nextduedate) as stampdate, tblproducts.gid from tblhosting , tblproducts, mod_observium where tblhosting.packageid = tblproducts.id and mod_observium.gid = tblproducts.gid and unix_timestamp(tblhosting.nextduedate) > '" . $right_now . "'");
		if(mysql_num_rows($services_query) > 0):
		
			logActivity('Observium CronJob: 3. Services within Selected GID Found in Function sendOversageEmails() ', 0);	

			while($services = mysql_fetch_array($services_query , MYSQL_ASSOC)) {

					$hosting_id = $services['id'];
					//bill_id			
					$bill_id_query = full_query("select tblcustomfieldsvalues.value as bill_id from tblcustomfieldsvalues , tblcustomfields, tblhosting 
												where tblcustomfields.type = 'product' 
												and tblcustomfields.relid = tblhosting.packageid and tblcustomfields.id = tblcustomfieldsvalues.fieldid 
												and tblcustomfieldsvalues.relid = tblhosting.id and tblcustomfields.fieldname = 'bill_id' and tblhosting.id = '" . $hosting_id . "'");
								
								
					$bill_id_array = mysql_fetch_array($bill_id_query , MYSQL_ASSOC);
					//$bill_id = $bill_id_array['bill_id'];		
					
					//overage_cost
					$overage_cost_query = full_query("select tblcustomfieldsvalues.value as overage_cost from tblcustomfieldsvalues , tblcustomfields, tblhosting 
												where tblcustomfields.type = 'product' 
												and tblcustomfields.relid = tblhosting.packageid and tblcustomfields.id = tblcustomfieldsvalues.fieldid 
												and tblcustomfieldsvalues.relid = tblhosting.id and tblcustomfields.fieldname = 'overage_cost' and tblhosting.id = '" . $hosting_id . "'");
								
								
					$overage_cost_array = mysql_fetch_array($overage_cost_query , MYSQL_ASSOC);
					
					//Bill_id and ovarage_price MERGE
					$service_list[] = array_merge(['bill_id' => $bill_id_array['bill_id'] ] , ['overage_cost' => $overage_cost_array['overage_cost']] , $services);	

			}
			
			
		endif;

		$observium = new Observium_class();
		$vars = [];
		$addons_query = select_query("tbladdonmodules" , 'setting , value' , ['module' => 'bandwidth']);
		while($addons_array = mysql_fetch_array($addons_query , MYSQL_ASSOC)) {
			$vars[$addons_array['setting']] = $addons_array['value'];
		}
		
		$observium->init($vars);
	
		logActivity('Observium CronJob: 4. API initilization in Function sendOversageEmails() ', 0);	

		$bills = $observium->request(['cmd' => 'bills/']);

		$excessDefaultRate = $vars['excessDefault'];


		if($bills->status == 'ok'):
			
				logActivity('Observium CronJob: 5. API Status Ok sendOversageEmails() ', 0);	

				foreach($service_list as $key => $value):
					//Service is under way.
					
					if(!empty($value['bill_id'])) {
						
						logActivity('Observium CronJob: 6. Bill ID ' . $value['bill_id'] . ' Found in sendOversageEmails() ', 0);	

						if(is_object($bills->bill)):	
							$bill = $bills->bill->$value['bill_id'];
							$type = 0;
							//Hide Them Once Testing is DONE
							//$bill->total_data = 27080000000000;
							//$bill->bill_quota = 20000000000000;
							//Calculate What Percent of Data Has Been Used.
						
							if(empty($value['overage_cost'])):
								$value['overage_cost'] = $excessDefaultRate;
							endif;
						
							$bandwidth_used = $bill->total_data / $bill->bill_quota;
							$bandwidth_percent_used = number_format($bandwidth_used * 100 , 2);
							
							switch(true) {
								
								
								case ($bandwidth_percent_used > 100):
									$type = 100;
								break;
								
								case ($bandwidth_percent_used > 75):
									$type = 75;
								break;								
								
								case ($bandwidth_percent_used > 50):
									$type = 50;
								break;
								
								default:
									$type = 0;
								break;
								
							}
							if($type > 0) {
							//Run the Query to check if the value already exists.
								$email_sent_query = full_query("select count(bill_id) as bill from mod_observium_emails where bill_id = '" . $value['bill_id'] . "' and type = '" . $type . "' and userid = '" . $value['userid'] . "' and unix_timestamp(start) <= '" . $right_now . "' and unix_timestamp(end) >= '" . $right_now . "' and service_id = '" . $value['id'] ."'");
								//echo "select count(bill_id) as bill from mod_observium_emails where bill_id = '" . $value['bill_id'] . "' and type = '" . $type . "' and userid = '" . $value['userid'] . "' and unix_timestamp(start) <= '" . $right_now . "' and unix_timestamp(end) >= '" . $right_now . "'";
								$email_sent = mysql_fetch_array($email_sent_query , MYSQL_ASSOC);
								
								if(!$email_sent['bill']) {
									
									//Lets Send Email. First Load The template.
									//tblemailtemplates
									//50-bandwidth-usage
									//75-bandwidth-usage
									//100-bandwidth-usage
									$email_template_query = full_query("select * from tblemailtemplates where name = '" . $type . "-bandwidth-usage'");
									$email_template = mysql_fetch_array($email_template_query , MYSQL_ASSOC);
									
									$start = new \Carbon\Carbon($value['nextduedate']); 
									$end = $value['nextduedate'];
									switch($value['billingcycle']) {
										
										case 'Monthly':
											$start->subMonth();
										break;
										
										case 'Quarterly':
											$start->subMonths(3);
										break;
										
										case 'Semi-Annually':
											$start->subMonths(6);
										break;
						
										case 'Annually':
											$start->subYear();
										break;
						
										case 'Biennially':
											$start->subYears(2);
										break;
						
										case 'Triennially':
											$start->subYears(3);
										break;
										
									}
									
									$start = $start->format('Y-m-d');
									
									
									$command = 'SendEmail';
									$postData = array(
										'messagename' => $email_template['name'],
										'id' => $value['userid'],
										//'total_bandwidth' => $bill->bill_quota,
										'customtype' => 'general',
										'customsubject' => $email_template['subject'],
										'custommessage' => $email_template['message'],
										'customvars' => base64_encode(
																serialize(
																array_merge(
																		array("percentage_bandwidth_used" => $bandwidth_percent_used,
																			  "total_bandwidth" => number_format(($bill->bill_quota/1000000000000),2), 
																			  "bandwidth_used" => number_format(($bill->total_data/1000000000000),2)),
																			  $value
																		   )
																		  )
																	  )
									);
									
									$results = localAPI($command, $postData);
									//echo '<pre>';
									//print_r($results);
									if($results['result'] === 'success'):
										//Now Lets Insert the Rush
										insert_query("mod_observium_emails" , ['bill_id' => $value['bill_id'], 'service_id' => $value['id'], 'userid' => $value['userid'], 'type' => $type, 'start' => $start, 'end' => $end]);
										logActivity('Observium CronJob: 7. Email Sent in sendOversageEmails() ', 0);	
									endif;
									
									
								} else {
										logActivity('Observium CronJob: 7. Email was already Sent to the customer #' . $value['userid'] . ' in sendOversageEmails() ', 0);	
								}
							} else {
								
								logActivity('Observium CronJob: 7. Usage in limits in sendOversageEmails() ', 0);	

							}
							
							//echo '<pre>';
							//print_r("I am mouse");
							
						endif;
					}
					
				endforeach; //Foreach Service
			endif;
			
			
					
		logActivity('Observium CronJob: 8. Complete in sendOversageEmails() ', 0);	


}
