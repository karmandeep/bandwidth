<?php
/**
 * WHMCS SDK Sample Addon Module
 *
 * An addon module allows you to add additional functionality to WHMCS. It
 * can provide both client and admin facing user interfaces, as well as
 * utilise hook functionality within WHMCS.
 *
 * This sample file demonstrates how an addon module for WHMCS should be
 * structured and exercises all supported functionality.
 *
 * Addon Modules are stored in the /modules/addons/ directory. The module
 * name you choose must be unique, and should be all lowercase, containing
 * only letters & numbers, always starting with a letter.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "addonmodule" and therefore all functions
 * begin "addonmodule_".
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/addon-modules/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . '/observium_class.php';
// Require any libraries needed for the module to function.
// require_once __DIR__ . '/path/to/library/loader.php';
//
// Also, perform any initialization required by the service's library.

use WHMCS\Module\Addon\Bandwidth\Observium_class;
use WHMCS\Module\Addon\Bandwidth\Admin\AdminDispatcher;
use WHMCS\Module\Addon\Bandwidth\Client\ClientDispatcher;

/**
 * Define addon module configuration parameters.
 *
 * Includes a number of required system fields including name, description,
 * author, language and version.
 *
 * Also allows you to define any configuration parameters that should be
 * presented to the user when activating and configuring the module. These
 * values are then made available in all module function calls.
 *
 * Examples of each and their possible configuration parameters are provided in
 * the fields parameter below.
 *
 * @return array
 */
 
 

/***/
 
function bandwidth_config()
{
    return array(
        'name' => 'Observium Bandwith API', // Display name for your module
        'description' => 'This module provides an WHMCS Addon Module which is integrated to Observium module. (With Email Support)', // Description displayed within the admin interface
        'author' => 'Karmandeep Singh', // Module author name
        'language' => 'english', // Default language
        'version' => '1.1', // Version number
        'fields' => array(
            // a text field type allows for single line text input
            'observiumURL' => array(
                'FriendlyName' => 'Observium URL',
                'Type' => 'text',
                'Size' => '55',
                'Default' => '',
                'Description' => 'Please enter the url of your observium installation.',
            ),
            // a password field type allows for masked text input
            'apiUsername' => array(
                'FriendlyName' => 'API Username',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ),
            // the yesno field type displays a single checkbox option
            'apiPassword' => array(
                'FriendlyName' => 'API Password',
                'Type' => 'password',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ),
			'excessDefault' => array(
                'FriendlyName' => 'Excess Usage Default',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => '',
            ),
        )
    );
}

/**
 * Activate.
 *
 * Called upon activation of the module for the first time.
 * Use this function to perform any database and schema modifications
 * required by your module.
 *
 * This function is optional.
 *
 * @return array Optional success/failure message
 */
function bandwidth_activate()
{
	
    // Create custom tables and schema required by your module
    $query = "CREATE TABLE IF NOT EXISTS `mod_observium` (`id` int( 11) NOT NULL AUTO_INCREMENT ,`gid` int( 11 ) NOT NULL DEFAULT 0, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    full_query($query);

    $query = "CREATE TABLE IF NOT EXISTS `mod_observium_invoices` (`id` int( 11) NOT NULL AUTO_INCREMENT , `invoice_id` int( 11 ) NOT NULL DEFAULT 0, `bill_id` int( 11 ) NOT NULL DEFAULT 0, `service_id` int( 11 ) NOT NULL DEFAULT 0, `start` date DEFAULT NULL, `end` date DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    full_query($query);

	$query = "CREATE TABLE IF NOT EXISTS `mod_observium_emails` (`id` int( 11) NOT NULL AUTO_INCREMENT ,`bill_id` int( 11 ) NOT NULL DEFAULT 0, `service_id` int( 11 ) NOT NULL DEFAULT 0, `userid` int( 11 ) NOT NULL DEFAULT 0, `type` varchar(50) NULL, `start` date DEFAULT NULL, `end` date DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
	full_query($query);		

    return array(
        'status' => 'success', // Supported values here include: success, error or info
        'description' => 'Module Observium Successfully Activated.',
    );
}

/**
 * Deactivate.
 *
 * Called upon deactivation of the module.
 * Use this function to undo any database and schema modifications
 * performed by your module.
 *
 * This function is optional.
 *
 * @return array Optional success/failure message
 */
function bandwidth_deactivate()
{
    // Undo any database and schema modifications made by your module here
    $query = "DROP TABLE `mod_observium`";
    full_query($query);

    $query = "DROP TABLE `mod_observium_invoices`";
    full_query($query);

	$query = "DROP TABLE `mod_observium_emails`";
    full_query($query);
	
    return array(
        'status' => 'success', // Supported values here include: success, error or info
        'description' => 'Module Observium Successfully DeActivated.',
    );
}

/**
 * Upgrade.
 *
 * Called the first time the module is accessed following an update.
 * Use this function to perform any required database and schema modifications.
 *
 * This function is optional.
 *
 * @return void
 */
function bandwidth_upgrade($vars)
{
    $currentlyInstalledVersion = $vars['version'];
// alter table `mtowhmcs`.`mod_observium_invoices` add column `bill_id` int (11) DEFAULT '0' NOT NULL  after `invoice_id`

    /// Perform SQL schema changes required by the upgrade to version 1.1 of your module
    
	if($currentlyInstalledVersion < 1.1) {

		$query = "DELETE FROM `mod_observium_invoices`";
		full_query($query);
		
		$query = "ALTER TABLE `mod_observium_invoices` ADD column `bill_id` int (11) DEFAULT '0' NOT NULL  after `invoice_id`";
		full_query($query);

		$query = "ALTER TABLE `mod_observium_invoices` ADD column `service_id` int (11) DEFAULT '0' NOT NULL  after `bill_id`";
		full_query($query);
		
		$query = "CREATE TABLE IF NOT EXISTS `mod_observium_emails` (`id` int( 11) NOT NULL AUTO_INCREMENT ,`bill_id` int( 11 ) NOT NULL DEFAULT 0, `service_id` int( 11 ) NOT NULL DEFAULT 0, `userid` int( 11 ) NOT NULL DEFAULT 0, `type` varchar(50) NULL, `start` date DEFAULT NULL, `end` date DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
		full_query($query);		

	}
}

/**
 * Admin Area Output.
 *
 * Called when the addon module is accessed via the admin area.
 * Should return HTML output for display to the admin user.
 *
 * This function is optional.
 *
 * @see AddonModule\Admin\Controller@index
 *
 * @return string
 */
function bandwidth_output($vars)
{
	$observium = new Observium_class();
	$observium->init($vars);
	
	$send_request = $observium->request(['cmd' => 'status']);
	
	if($send_request->status == 'ok'):
	
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	
		$dispatcher = new AdminDispatcher();
		$response = $dispatcher->dispatch($action, $vars);

	    echo $response;
	
	else:
	
		echo 'Unable to Connect: Please Check your API credentials.';
	
	endif;
}

/**
 * Admin Area Sidebar Output.
 *
 * Used to render output in the admin area sidebar.
 * This function is optional.
 *
 * @param array $vars
 *
 * @return string
 */
function bandwidth_sidebar($vars)
{

    /*$sidebar = '<p>Sidebar output HTML goes here</p>';
    return $sidebar;*/
}

/**
 * Client Area Output.
 *
 * Called when the addon module is accessed via the client area.
 * Should return an array of output parameters.
 *
 * This function is optional.
 *
 * @see AddonModule\Client\Controller@index
 *
 * @return array
 */
 
function bandwidth_clientarea($vars)
{
	
	
	if(isset($_GET['id'])):
	
		 $hosting_id = $_GET['id'];
		 //Set the ID
		 //Get the Product ID from service ID
		 $check_group_query = full_query("select count(*) as result from tblproducts, tblhosting, mod_observium 
		 								where tblhosting.packageid = tblproducts.id and tblhosting.id = '" . $hosting_id . "'
										and tblproducts.gid = mod_observium.gid;");
										
		 $group_check = mysql_fetch_array($check_group_query , MYSQL_ASSOC);
		//First Check and the Set
	
		 if($group_check):
		 	
			//Get the bill bill_id
			
			$bill_id_query = full_query("select tblcustomfieldsvalues.value as bill_id from tblcustomfieldsvalues , tblcustomfields, tblhosting 
										where tblcustomfields.type = 'product' 
										and tblcustomfields.relid = tblhosting.packageid and tblcustomfields.id = tblcustomfieldsvalues.fieldid 
										and tblcustomfieldsvalues.relid = tblhosting.id and tblcustomfields.fieldname = 'bill_id' and tblhosting.id = '" . $hosting_id . "'");
						
						
		 	$bill_id_array = mysql_fetch_array($bill_id_query , MYSQL_ASSOC);
			//$bill_id = $bill_id_array['bill_id'];		
			
			$overage_cost_query = full_query("select tblcustomfieldsvalues.value as overage_cost from tblcustomfieldsvalues , tblcustomfields, tblhosting 
										where tblcustomfields.type = 'product' 
										and tblcustomfields.relid = tblhosting.packageid and tblcustomfields.id = tblcustomfieldsvalues.fieldid 
										and tblcustomfieldsvalues.relid = tblhosting.id and tblcustomfields.fieldname = 'overage_cost' and tblhosting.id = '" . $hosting_id . "'");
						
						
		 	$overage_cost_array = mysql_fetch_array($overage_cost_query , MYSQL_ASSOC);
			//$bill_id = $bill_id_array['bill_id'];		
			
			$vars = array_merge(['bill_id' => $bill_id_array['bill_id'] , 'overage_cost' => $overage_cost_array['overage_cost']] , ['id' => $hosting_id] , $vars);
			
			$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
		
			$dispatcher = new ClientDispatcher();
			return $dispatcher->dispatch($action, $vars);
		 
		 endif; //END Grouop Check
	
	
	endif;
}
