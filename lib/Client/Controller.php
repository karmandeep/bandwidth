<?php

namespace WHMCS\Module\Addon\Bandwidth\Client;
use WHMCS\Module\Addon\Bandwidth\Observium_class;

/**
 * Sample Client Area Controller
 */
class Controller {

    /**
     * Index action.
     *
     * @param array $vars Module configuration parameters
     *
     * @return array
     */
    public function index($vars)
    {
		
		$services_query = select_query("tblhosting" , "", ['id' => $vars['id']]);
	 	$services_array = mysql_fetch_array($services_query , MYSQL_ASSOC);


		$start = new \Carbon\Carbon($services_array['nextduedate']); 
		$end = $services_array['nextduedate'];
		switch($services_array['billingcycle']) {
			
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
		
		//https://noc.as133480.net.au/api/v0/bills/
		$observium = new Observium_class();
		$observium->init($vars);
				
		$bills = $observium->request(['cmd' => 'bills/']);
		$bill = $bills->bill->$vars['bill_id'];
		
		$rate_average = $bill->rate_average / 1000000;
		
		$bandwidth_used = $bill->total_data / $bill->bill_quota;
		$bandwidth_percent_used = number_format($bandwidth_used * 100 , 2);
		//$bandwidth_percent_used = $bandwidth_percent_used;
				
		//Over-rage
		//$bill->total_data = 127129134000000;
		$excess_usage = 0;
		$excess_usage_percent = 0;
		//if(1):
		if($bill->total_data > $bill->bill_quota):
			$excess_usage = $bill->total_data - $bill->bill_quota;
			$excess_usage_percent = number_format((($bill->total_data*100)/$bill->bill_quota),0);
		endif;
		
		if(isset($_GET['check'])):
			$bandwidth_percent_used = $_GET['check'];
		endif;
		
			return array(
				'pagetitle' => 'Bandwidth usage details',
				'breadcrumb' => array(
					'index.php?m=bandwidth&id=' . $vars['id']  => 'Bandwidth usage details',
				),
				'templatefile' => 'publicpage',
				'sidebar' => true,
				'requirelogin' => true, // Set true to restrict access to authenticated client users
				'forcessl' => false, // Deprecated as of Version 7.0. Requests will always use SSL if available.
				'vars' => array(
					'modulelink' => $vars['modulelink'],
					'id' => $vars['id'],
					'bill_id' => $vars['bill_id'],
					'bill' => $bill,
					'rate_average' => number_format($rate_average,2),
					'transferred' => number_format(($bill->total_data / 1000000000000),2),
					'totalquota' => number_format(($bill->bill_quota / 1000000000000),0),
					'excess_usage' => number_format(($excess_usage / 1000000000000),4),
					'bandwidth_percent_used' => $bandwidth_percent_used,
					'excess_usage_percent' => $excess_usage_percent,
					'billing_start' => $start->timestamp,
					'billing_end' =>  \Carbon\Carbon::parse($end)->timestamp,
					'today' => \Carbon\Carbon::now()->format('m/d/Y'),
					'last30days' => \Carbon\Carbon::now()->subMonth()->format('m/d/Y'),
					'rightnow_stamp' => \Carbon\Carbon::now()->timestamp,
					'last24hours_stamp' =>  \Carbon\Carbon::now()->subDay()->timestamp,
					'last7days_stamp' =>  \Carbon\Carbon::now()->subDays(7)->timestamp,
					'last30days_stamp' =>  \Carbon\Carbon::now()->subMonth()->timestamp,
					'last60days_stamp' =>  \Carbon\Carbon::now()->subMonth(2)->timestamp,
					'last60days' => \Carbon\Carbon::now()->subMonth(2)->format('m/d/Y'),
					
				),
			);
    }
	
	/**
	* Display Graph
	*
	*/
	public function displayGraph($vars) {
			
			$from = $_GET['from'];
			$to = $_GET['to'];

			header("Content-Type: image/png");

			//Get Graph
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://noc.as133480.net.au/graph.php?type=bill_bits&from=" . $from . "&to=" . $to . "&id=" . $vars['bill_id'] . "&width=800&total=0");

			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			   
			$content = curl_exec($ch);
			curl_close($ch);			
			echo $content;
			exit;

	}

    
}