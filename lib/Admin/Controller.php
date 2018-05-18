<?php

namespace WHMCS\Module\Addon\Bandwidth\Admin;
use WHMCS\Module\Addon\Bandwidth\Observium_class;

/**
 * Sample Admin Area Controller
 */
class Controller {

	public function save() {
		
		$gid = $_POST['gid'];
		
		//First Delete all the values.
		full_query('delete from mod_observium');
		
		//Insert Query
		foreach($gid as $key => $value):
			insert_query("mod_observium" , ['gid' => $value]);
		endforeach;
		
		header("Location: addonmodules.php?module=bandwidth&msg=Updated+Successfully");
   		exit;
	}
    /**
     * Index action.
     *
     * @param array $vars Module configuration parameters
     *
     * @return string
     */
    public function index($vars)
    {
		
    $selected_product_groups = [];
	$selected_product_groups_query = select_query("mod_observium", "gid", '' );
	while ($selected_data = mysql_fetch_array($selected_product_groups_query, MYSQL_ASSOC)) {
		$selected_product_groups[$selected_data['gid']] = 'ok';		
	}
				
	$product_group_string = '';
	$product_group_query = select_query("tblproductgroups", "*", '' , 'id');
	while ($data = mysql_fetch_array($product_group_query, MYSQL_ASSOC)) {
		if($selected_product_groups[$data['id']] == 'ok'):
			$product_group_string .= '<option selected="selected" value="' . $data['id'] . '">' . $data['name'] . '</option>';
		else:
			$product_group_string .= '<option  value="' . $data['id'] . '">' . $data['name'] . '</option>';
		endif;
	}
	
	if(isset($_GET['msg'])):
		$msg = $_GET['msg'] . '<br />';
	endif;
		
		
        return <<<EOF
<form name="product_group_selection" action="" method="post">
<input type="hidden" name="action" value="save">

<span>$msg</span>
Please Select the product groups to whom you would like the addon should work with.		
<table cellpadding="5" cellspacing="5" border="0" >
	<tr>
		<td style="padding:5px;">
		
			<select id="list1" name="gid[]" multiple="multiple" style="width:250px;" rows=20>
				$product_group_string
			</select>
		
		</td>
		
		
	</tr>
	
	<tr>
		<td style="padding:5px;">
		
			<input type="submit" name="save" value="Save">
		</td>
	
	</tr>
	
</table>
</form>


EOF;
    }

   
}
