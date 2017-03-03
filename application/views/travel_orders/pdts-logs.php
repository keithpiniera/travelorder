		<style type="text/css">
			.fixed-table-loading {
				display: none;
			}
		</style>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">PDTS Logs</div>
				<div class="panel-body">
					<table data-toggle="table" data-url="" id="table-pdts-logs">
						<thead>
						    <tr>
						        <th data-field="datetime">DateTime</th>
						        <th data-field="description">Description</th>
						    </tr>
						</thead>
					</table>
				</div>
			</div>
		</div><!-- /.col-->
	</div><!-- /.row -->	

	<script type="text/javascript">


		$(document).ready(function(){
			//var baseUrl = "<?php echo base_url() . 'index.php/travel_orders/'; ?>";
			$.ajax({
				url: baseUrl+"load_pdts_logs",
				method: "POST",
				data: {
					tracking_number: '<?php echo $details[0]["tracking_number"]; ?>'
				},
				success: function(response){
					$('#table-pdts-logs').find('tbody').html(response);
					if (response=='') $('#table-pdts-logs').find('tbody').html('<tr class="no-records-found"><td colspan="4">No logs found</td></tr>');
				}
			});
		});

	</script>