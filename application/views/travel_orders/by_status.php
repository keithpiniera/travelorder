		<style type="text/css">
			.table-my-travels th:nth-child(1){width: 10%;}
			.table-my-travels th:nth-child(2){width: 30%;}
			.table-my-travels th:nth-child(3){width: 25%;}
			.table-my-travels th:nth-child(4){width: 10%;}
			.table-my-travels th:nth-child(5){width: 10%;}
			.table-my-travels th:nth-child(6){width: 15%;}
			.fixed-table-loading {
				display: none;
			}
			.outdated {
				opacity: 0.5;
				background-color: black;
			}
		</style>

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<!-- Displays ongoing Travels -->
					<div class="panel-heading">	<?php echo ucwords(str_replace('_', ' ', $panel_header));?></div>
					<div class="panel-body">
						<table data-toggle="table" data-url="" id="table-<?php echo strtolower($panel_header); ?>" class="table-my-travels">
						    <thead>
						    <tr>
						        <th data-field="tracking">Tracking No.</th>
						        <th data-field="purpose">Purpose</th>
						        <th data-field="destination">Destination</th>
						        <th data-field="dates">Date</th>
						        <th data-field="time">Time of Departure</th>
						        <th data-field="prepared-by">Prepared by</th>
								<!-- 
						        <?php if (in_array(strtolower($panel_header), array('pending', 'canceled', 'declined'))): ?>
						        <th data-field="status">Remarks</th>
						    	<?php elseif (in_array(strtolower($panel_header), array('for_recommendation', 'for_approval'))): ?>
						    	<th data-field="recommend"><?php echo strtolower($panel_header) == 'for_recommendation' ? 'Recommend':'Approve'; ?></th>
						    	<th data-field="decline">Decline</th>
						    	<?php endif; ?>
						    	 -->
						    </tr>
						    </thead>
						</table>

						<?php 

						$config['base_url'] = base_url() . 'index.php/travel_orders/status/'. strtolower($panel_header);
						$config['total_rows'] = $rows;
						$config['per_page'] = 10;

						$this->pagination->initialize($config);
						echo $this->pagination->create_links();

						 ?>
					</div>
				</div>
			</div>
		</div><!--/.row-->

		<script type="text/javascript">

			var baseUrl = "<?php echo base_url() . 'index.php/travel_orders/'; ?>";

			function loadMyTravels(status){
				<?php $this->pagination->cur_page = $this->pagination->cur_page == 0 ? 1:$this->pagination->cur_page; ?>
				$.ajax({
					url: baseUrl+"load_travel_orders",
					method: "POST",
					data: {
						<?php if ( strtolower($panel_header) == 'search' ): ?>
						keyword: '<?php echo $keyword; ?>',
						<?php endif; ?>
						status: status,
						offset: '<?php echo $this->pagination->cur_page * $config["per_page"] - $config["per_page"];?>',
						limit: '<?php echo $config["per_page"]; ?>'
					},
					success: function(response){
						$('#table-'+status).find('tbody').html(response);
						if (response=='') $('#table-'+status).find('tbody').html('<tr class="no-records-found"><td colspan="4">No matching records found</td></tr>');
					}
				});
			}
			loadMyTravels('<?php echo strtolower($panel_header); ?>');

			$('.table-my-travels').on('click', 'tbody tr', function(){
				if ( $(this).data('travelid') == undefined ) return false;
				window.location.href = baseUrl + 'view_travel/' + $(this).data('travelid');
			});
		</script>
	
