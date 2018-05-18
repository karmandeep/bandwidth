{literal}

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
  $( function() {
    $( "#datepicker_start" ).datepicker( {minDate: -90 , maxDate: 0 , dateFormat: 'mm/dd/yy'} );
    $( "#datepicker_end" ).datepicker( {minDate: -75 , maxDate: 0, dateFormat: 'mm/dd/yy'} );
	
	
	$("#graph-search").bind('submit' , function(e){
		
		e.preventDefault();
		
		var datepicker_start = $('#datepicker_start').val();
		var datepicker_end = $('#datepicker_end').val();
		
		var from = (Date.parse(datepicker_start))/1000;
		var to = (Date.parse(datepicker_end))/1000;
		
		//if((from + 172800) >= to) {
		if(from >= to) {	
			alert('Invalid Date Selected, End Date should always be greater than start date.');
			
		} else {
		
			$('#graph-60d-image').attr('src' , 'index.php?m=bandwidth&id={/literal}{$id}&bill_id={$bill_id}{literal}&action=displayGraph&from='+from+'&to='+to+'');
		
		}
		//console.log(this);
		
	});
	
  } );
  
  

</script>

{/literal}
<div class="row">

	<div class="col-md-3 pull-md-left sidebar">
    	<div menuitemname="Service Details Overview" class="panel panel-sidebar panel-default panel-actions">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-star"></i>&nbsp;Overview
                <i class="fa fa-chevron-up panel-minimise pull-right"></i>
            </h3>
        </div>
        <div class="list-group list-group-tab-nav">
            <a menuitemname="Information" href="/clientarea.php?action=productdetails&amp;id={$id}#tabOverview" class="list-group-item" id="Primary_Sidebar-Service_Details_Overview-Information">
              Information
            </a>
            <a menuitemname="Bandwidth" href="/index.php?m=bandwidth&amp;id={$id}" class="list-group-item active" id="Primary_Sidebar-Service_Details_Overview-Bandwidth">
              Bandwidth usage
            </a>
        </div>
    	</div>
    </div>
    
	<div class="col-md-9 pull-md-right">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">
                            Bill Summary
                        </h3>
                    </div>
                    <div>
                        <div class="row">
                            <div class="col-sm-4 col-md-4 col-lg-4"></div>
                            <div class="col-sm-8 col-md-8 col-lg-8 pull-md-right margin-10">
                                <table width="100%">
                                    <tr>
                                      <td class="text-left" width="35%"><strong>Bill Name:</strong></td>
                                      <td class="text-left">{$bill->bill_name} </td>
                                    </tr>
                                    <tr>
                                      <td class="text-left"><strong>Transferred:</strong></td>
                                      <td class="text-left">{$transferred} TB of {$totalquota} TB {if $excess_usage > 0} ({$excess_usage_percent}%) {/if} </td>
                                    </tr>
                                    {if $excess_usage > 0}
                                    <tr>
                                      <td class="text-left"><strong>Over Usage:</strong></td>
                                      <td class="text-left">{$excess_usage} GB </td>
                                    </tr>
                                    {/if}
                                    <tr>
                                      <td class="text-left"><strong>Average Rate:</strong></td>
                                      <td class="text-left">{$rate_average} Mbps </td>
                                    </tr>
                                    
                                    <tr>
                                      <td colspan="2">
                                      
                                        {if $excess_usage > 0}
                                        
                                        	<table width="100%">
                                            	<tr>
                                      				<td width="5%" class="text-left"><strong>{$excess_usage_percent}%</strong></td>
                                      				<td class="text-right">
                                                        <div class="progress skill-bar margin-10">
                                                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="{$excess_usage_percent}" aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>	
                                            		</td>
                                    			</tr>
                                            </table>

                                        {else}
                                        
                                        	<table width="100%">
                                            	<tr>
                                      				<td width="5%" class="text-left">
														<span style="background:black; color:white;" class="label label-dark"><strong>{$bandwidth_percent_used}%</strong></span>
                                                    </td>
                                      				<td class="text-right">
                                                    
                                                        <div class="progress skill-bar margin-10">
                                                        {if $bandwidth_percent_used > 75}
                                                            <div class="progress-bar progress-bar-striped progress-bar-danger" role="progressbar" aria-valuenow="{$bandwidth_percent_used}" aria-valuemin="0" aria-valuemax="100">
                                                        {else if $bandwidth_percent_used > 50}
                                                            <div class="progress-bar progress-bar-striped progress-bar-warning" role="progressbar" aria-valuenow="{$bandwidth_percent_used}" aria-valuemin="0" aria-valuemax="100">
                                                        {else if $bandwidth_percent_used > 25}
                                                            <div class="progress-bar progress-bar-striped progress-bar-info" role="progressbar" aria-valuenow="{$bandwidth_percent_used}" aria-valuemin="0" aria-valuemax="100">
                                                        {else}
                                                            <div class="progress-bar progress-bar-striped progress-bar-success" role="progressbar" aria-valuenow="{$bandwidth_percent_used}" aria-valuemin="0" aria-valuemax="100">
                                                       	{/if}
                                                            </div>
                                                        </div>	
                                            		</td>
                                    			</tr>
                                            </table>
                                            
                                        {/if}
                                      </td>
                                    </tr>
                                    
                                </table>
                                
                                
                                
                            </div>
                            
                        
                        </div>

                        
                    
                    </div>
                </div>
            </div>            
        </div>
        
        
        
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="panel panel-default">
                
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">
                            24 Hours
                        </h3>
                    </div>
                
                <div>
                
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <div class="imgwrapper margin-10">
                            	<!--<img id="graph-24h-image" class="img-responsive" src="index.php?m=observium&id={$id}&bill_id={$bill_id}&action=displayGraph&from=-1d&to=now" />-->
                                <img id="graph-24h-image" class="img-responsive" src="index.php?m=bandwidth&id={$id}&bill_id={$bill_id}&action=displayGraph&from={$last24hours_stamp}&to={$rightnow_stamp}" />
                            </div>
                        </div>
                    </div>
                
                </div>
                </div>
            </div>            
        </div>


        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="panel panel-default">
                
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">
                            7 Days
                        </h3>
                    </div>
                
                <div>
                
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <div class="imgwrapper margin-10">
                            	<!--<img id="graph-7d-image" class="img-responsive" src="index.php?m=observium&id={$id}&bill_id={$bill_id}&action=displayGraph&from=-7d&to=now" />-->
                                <img id="graph-7d-image" class="img-responsive" src="index.php?m=bandwidth&id={$id}&bill_id={$bill_id}&action=displayGraph&from={$last7days_stamp}&to={$rightnow_stamp}" />
                            </div>
                        </div>
                    </div>
                
                </div>
                </div>
            </div>            
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="panel panel-default">
                
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">
                            30 Days
                        </h3>
                    </div>
                <div>
                           	
                </div>
                <div>
                
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <div class="imgwrapper margin-10">
                            	<!--<img id="graph-30d-image" class="img-responsive" src="index.php?m=observium&id={$id}&bill_id={$bill_id}&action=displayGraph&from=-30d&to=now" />-->
                                <img id="graph-30d-image" class="img-responsive" src="index.php?m=bandwidth&id={$id}&bill_id={$bill_id}&action=displayGraph&from={$last30days_stamp}&to={$rightnow_stamp}" />
                            </div>
                        </div>
                    </div>
                
                </div>
                </div>
            </div>            
        </div>



        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="panel panel-default">
                
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">
                            Billing Cycle
                        </h3>
                    </div>
                
                <div>
                
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <div class="imgwrapper margin-10">
                                <img id="graph-cycle-image" class="img-responsive" src="index.php?m=bandwidth&id={$id}&bill_id={$bill_id}&action=displayGraph&from={$billing_start}&to={$billing_end}" />
                            </div>
                        </div>
                    </div>
                
                </div>
                </div>
            </div>            
        </div>


        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="panel panel-default">
                
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">
                            Custom date range
                        </h3>
                    </div>
                <div>
                <form name="search" id="graph-search" action="index.php?m=bandwidth&id={$id}&bill_id={$bill_id}&action=displayGraph" method="get">
                	<div class="row">
                    	<div class="col-sm-4 col-md-4 col-lg-4 pull-md-left margin-10">
                            <div class="form-group">
                                <label for="startdate" class="control-label">Start Date</label>
                                <input type="text" id="datepicker_start" readonly name="startdate" value="{$last60days}" class="form-control">
                            </div>
                    	</div>
                        <div class="col-sm-4 col-md-4 col-lg-4 pull-md-left margin-10">              	
                            <div class="form-group">
                                <label for="enddate" class="control-label">End Date</label>
                                <input type="text" id="datepicker_end" readonly name="enddate" value="{$today}" class="form-control">
                            </div>
                    	</div>  
                        <div class="col-sm-3 col-md-3 col-lg-3 pull-md-left margin-10"> 
                        	<label for="submit" class="control-label">&nbsp;</label>
                        	<input type="submit" name="submit" class="form-control btn btn-primary " value="Search">
                        </div>
                    </div>
                </form>                	
                </div>
                <div>
                
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <div class="imgwrapper margin-10">
                            	<!--<img id="graph-30d-image" class="img-responsive" src="index.php?m=observium&id={$id}&bill_id={$bill_id}&action=displayGraph&from=-30d&to=now" />-->
                                <img id="graph-60d-image" class="img-responsive" src="index.php?m=bandwidth&id={$id}&bill_id={$bill_id}&action=displayGraph&from={$last60days_stamp}&to={$rightnow_stamp}" />
                            </div>
                        </div>
                    </div>
                
                </div>
                </div>
            </div>            
        </div>
        
       
        
	</div>


</div>
 <hr>
 <script type="text/javascript">
 
 
    $(document).ready(function() {
      $('.progress .progress-bar').css("width",
                function() {
                    return $(this).attr("aria-valuenow") + "%";
                }
        )
    });

 </script>
 <style>

.progress .skill {
  font: normal 12px "Open Sans Web";
  line-height: 35px;
  padding: 0;
  margin: 0 0 0 20px;
  text-transform: uppercase;
}
.progress .skill .val {
  float: right;
  font-style: normal;
  margin: 0 20px 0 0;
}

.progress-bar {
  text-align: left;
  transition-duration: 3s;
}

</style>