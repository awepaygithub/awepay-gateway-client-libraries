
<div class="wrap">
	<h1>Awepay Payout Plugin</h1>
	<?php 
	if(!empty($payout_response)){
		if(is_array($payout_response)){
			echo "<div class='error_alert'>";
			foreach($payout_response as $msg){
				echo "<p>".$msg."</p>";
			}
			echo "</div>";
		}else{
			echo "<h3 class='success_alert'>".$payout_response."</h3>";
		} 
	}
	
	?>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab-1">Payout Request</a></li>
	</ul>

	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">

			<form method="post">
				<?php 
					settings_fields( 'awepay_payout_options_group' );
					do_settings_sections( 'awepay_payout_request' );
					submit_button();
				?>
			</form>
			
		</div>

		
	</div>
</div>