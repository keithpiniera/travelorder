		<style type="text/css">
			.table-my-travels th:nth-child(1){width: 40%;}
			.table-my-travels th:nth-child(2){width: 20%;}
			.table-my-travels th:nth-child(3){width: 20%;}
			.fixed-table-loading {
				display: none;
			}
			.btn._group-2 {
				display: none;
			}
		</style>	
	<div class="row">
		<div class="col-lg-8">
			<div class="panel panel-default">
				<!-- Displays ongoing Travels -->
				<div class="panel-heading"><?php echo $panel_header; ?></div>
				<div class="panel-body">
					<table data-toggle="table" data-url=""id="table-previous" class="table-offices">
						<thead>
						 	<tr>
						        <th data-field="dates">Office/Agency/Building</th>
						        <th data-field="destination">City</th>
						        <th data-field="time">Province</th>
						    </tr>
						</thead>
					</table>

					<?php 
						
						$config['base_url'] = base_url() . 'index.php/travel_orders/offices/';
						$config['total_rows'] = $rows;
						$config['per_page'] = 10;

						$this->pagination->initialize($config);
						echo $this->pagination->create_links();
					?>
				</div>
			</div>
		</div><!-- /.col-->

		<div class="col-lg-4">
			<div class="panel panel-default">
				<!-- Displays ongoing Travels -->
				<div class="panel-heading">Details</div>
				<div class="panel-body">
					<fieldset id="form-office">
						<div class="form-group">
							<label class="form-control-label" for="">Office Name</label>
							<input class="form-control" type="text" name="officeDesc">
							<input class="form-control" type="hidden" name="officeCode">
						</div>
						<div class="form-group">
							<label class="form-control-label" for="">City/Municipality</label>
							<select class="form-control list-city" type="text" name="citymunCode"></select>
						</div>
						<div class="form-group">
							<label class="form-control-label" for="">Province</label>
							<select class="form-control list-province" type="text" name="provCode"></select>
						</div>
					</fieldset>

					<button class="btn btn-primary _group-1" id="save-office">Save</button>
					<button class="btn btn-danger _group-1" id="cancel-office">Cancel</button>
					<button class="btn btn-primary _group-2" id="new-office">New</button>
					<button class="btn btn-primary _group-2" id="edit-office">Edit</button>
					<button class="btn btn-danger _group-2" id="delete-office">Delete</button>
				</div>
			</div>
		</div><!-- /.col-->
	</div><!-- /.row -->	

	<script type="text/javascript">
		var listProvinces = <?php echo json_encode($province); ?>;
		var listCities = <?php echo json_encode($city); ?>;

		/*Cities*/
			(function(){
				$('.list-city').append($("<option></option>")); 
				for(i in listCities) {
					$('.list-city')
							.append($("<option></option>")
				                .attr("value", listCities[i].citymunCode)
				                .text(listCities[i].citymunDesc)); 
				}
			})();

			/*Provinces*/
			(function(){
				$('.list-province').append($("<option></option>")); 
				for(i in listProvinces) {
					$('.list-province')
							.append($("<option></option>")
				                .attr("value", listProvinces[i].provCode)
				                .text(listProvinces[i].provDesc)); 
				}
			})();

		(function(){
			<?php $this->pagination->cur_page = $this->pagination->cur_page == 0 ? 1:$this->pagination->cur_page; ?>
			$.ajax({
				url: '<?php echo base_url(); ?>index.php/travel_orders/get_offices',
				method: 'POST',
				data:{
					offset: '<?php echo $this->pagination->cur_page * $config["per_page"] - $config["per_page"];?>',
					limit: '<?php echo $config["per_page"]; ?>'
				},
				success: function(response){
					$('.table-offices tbody').html(response);
					if (response=='') $('.table-offices tbody').html('<tr class="no-records-found"><td colspan="3">No matching records found</td></tr>');
				}
			});
		})();

			$('.list-province').on('change', function(){
				var self = this;
				$('.list-city').find('option').remove(); 
				$('.list-city').append($("<option></option>")); 
				for(i in listCities) {
					if ( listCities[i].citymunCode.indexOf(this.value) != 0 ) continue;
					$('.list-city')
							.append($("<option></option>")
				                .attr("value", listCities[i].citymunCode)
				                .text(listCities[i].citymunDesc)); 
				}
			});

			$('.list-city').on('change', function(){
				var self = this;
				$('.list-province').find('option').each(function(){
					if ( self.value.indexOf(this.value) != 0) {
						
					} else {
						$(this).prop('selected', true);
					}
				});

			});


		$('#save-office').on('click', function(){
			if ($('[name="officeDesc"]').val() == '' ) {alert('Please fill-up Office Name.'); return false;}
			if ($('[name="citymunCode"]').val() == '' ) {alert('Please fill-up City/Municipality.'); return false;}
			if ($('[name="provCode"]').val() == '' ) {alert('Please fill-up Province.'); return false;}

			$('#form-office').wrap('<form></form>');
			var data = $('form').serialize();
			$('#form-office').unwrap('');

			$.ajax({
				url: '<?php echo base_url(); ?>index.php/travel_orders/save_office',
				method: 'POST',
				data: {
					data:data
				},
				success: function(response){
					window.location.reload();
				}
			});
		}); 

		$('#new-office, #cancel-office').on('click', function(){
			$('[name="officeCode"]').val('');
			$('[name="officeDesc"]').val('');
			$('[name="citymunCode"]').val('');
			$('[name="provCode"]').val('');

			$('#form-office').prop('disabled', false);
			$('.btn._group-2').hide();
			$('.btn._group-1').show();
		}); 

		$('#edit-office').on('click', function(){
			$('#form-office').prop('disabled', false);
			$('.btn._group-2').hide();
			$('.btn._group-1').show();
		}); 

		$('#delete-office').on('click', function(){
			var q = confirm('Are you sure you want to delete this record?');
			if (!q) return false;

			$.ajax({
				url: '<?php echo base_url(); ?>index.php/travel_orders/delete_office',
				method: 'POST',
				data: {
					officeCode: $('[name="officeCode"]').val()
				},
				success: function(response){
					window.location.reload();
				}
			});
		}); 

		$('.table-offices').on('click', 'tbody tr', function(){
			var data = $(this).data('details');
			$('[name="officeCode"]').val(data.officeCode);
			$('[name="officeDesc"]').val(data.officeDesc);
			$('[name="provCode"]').find('option[value="'+ data.provCode +'"]').prop('selected', true);
			$('[name="citymunCode"]').find('option[value="'+ data.citymunCode +'"]').prop('selected', true);

			$('#form-office').prop('disabled', true);
			$('.btn._group-1').hide();
			$('.btn._group-2').show();
		});
	</script>