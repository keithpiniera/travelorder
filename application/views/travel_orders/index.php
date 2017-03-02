		<style type="text/css">
			.table-my-travels th:nth-child(1){width: 18%;}
			.table-my-travels th:nth-child(2){width: 30%;}
			.table-my-travels th:nth-child(3){width: 15%;}
			.table-my-travels th:nth-child(4){width: 22%;}
			.fixed-table-loading {
				display: none;
			}
		</style>

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-primary">
					<!-- Displays ongoing Travels -->
					<div class="panel-heading">Ongoing</div>
					<div class="panel-body">
						<table data-toggle="table" data-url="" id="table-ongoing" class="table-my-travels">
						    <thead>
						    <tr>
						        <th data-field="dates">Inclusive Dates of Travel</th>
						        <th data-field="destination">Destination</th>
						        <th data-field="time">Time of Departure</th>
						        <th data-field="prepared-by">Prepared by</th>
						    </tr>
						    </thead>
						</table>
					</div>
				</div>
			</div>
		</div><!--/.row-->

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-info">
					<!-- Displays Upcoming Travels for the next few days -->
					<div class="panel-heading">Upcoming</div>
					<div class="panel-body">
						<table data-toggle="table" data-url="" id="table-upcoming" class="table-my-travels">
						    <thead>
						    <tr>
						        <th data-field="dates">Inclusive Dates of Travel</th>
						        <th data-field="destination">Destination</th>
						        <th data-field="time">Time of Departure</th>
						        <th data-field="prepared-by">Prepared by</th>
						    </tr>
						    </thead>
						</table>
					</div>
				</div>
			</div>
		</div><!--/.row-->

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<!-- Displays previous Travels-->
					<div class="panel-heading">Previous</div>
					<div class="panel-body">
						<table data-toggle="table" data-url="" id="table-previous" class="table-my-travels">
						    <thead>
						    <tr>
						        <th data-field="dates">Inclusive Dates of Travel</th>
						        <th data-field="destination">Destination</th>
						        <th data-field="time">Time of Departure</th>
						        <th data-field="prepared-by">Prepared by</th>
						    </tr>
						    </thead>
						</table>
					</div>
				</div>
			</div>
		</div><!--/.row-->

		<script type="text/javascript">

			var baseUrl = "<?php echo base_url() . 'index.php/travel_orders/'; ?>";

			function loadMyTravels(status){
				$.ajax({
					url: baseUrl+"my_travels",
					method: "POST",
					data: {
						status: status
					},
					success: function(response){
						$('#table-'+status).find('tbody').html(response);
					}
				});
			}
			loadMyTravels('upcoming');
			loadMyTravels('ongoing');
			loadMyTravels('previous');

			$('.table-my-travels').on('click', 'tbody tr', function(){
				window.location.href = baseUrl + 'view_travel/' + $(this).data('travelid');
			});
		</script>
	
