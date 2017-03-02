		<style type="text/css">
			.btn._selected {
				display: none;
			}
			.disabled-added-item {
				background-color: #eee;
			    padding: 5px;
			    margin-bottom: 5px;
			    border-radius: 3px;
			    cursor: pointer;
			}
			.added-item {
				background-color: mediumspringgreen;
			    padding: 5px;
			    margin-bottom: 5px;
			    border-radius: 3px;
			    cursor: pointer;
			}
			.added-item.unavailable {
				background-color: #ffb53e;
			}
			.added-item.active {
				background-color: springgreen;
			}
			.added-item > .name {
				font-weight: bold;
			}
			.added-item > .mobile-no {
				font-size: 12px;
			}
			.added-item > .project-code {
				font-size: 12px;
			}
			._indent {
				padding-left: 15px;
			}

			<?php if ( !empty($travel_id) ): ?>
			.btn._group-1 {
				display: none;
			}
			<?php else : ?>
			.btn._group-2 {
				display: none;
			}
			<?php endif; ?>
			.btn._group-3 {
				display: none;
			}
		</style>
		<?php if ( !empty($travel_id) ): ?>

		Date Created: <?php echo date('F d, Y', strtotime($details[0]['date_prepared'])); ?><br>
		Prepared By: <?php echo $details[0]['preparing_name']; ?>  <br><br>

		<div class="row" id="status-to">
			<div class="col-lg-12">
				<div class="alert bg-primary" role="alert">
					<svg class="glyph stroked flag"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-flag"></use></svg> 
						<span><strong>Status:</strong> <?php echo $status['status']; ?></span>&nbsp;&nbsp;&nbsp;
						<?php if (!empty($status['note'])): ?>
						<span><strong>Note:</strong> <?php echo $status['note']; ?></span>
						<?php endif; ?>
					<a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
				</div>
			</div>
		</div><!-- end row -->

		<?php if ( $access_level['can_print'] || $access_level['can_cancel'] || $access_level['can_recommend'] || $access_level['can_approve'] || $access_level['can_decline'] ): ?>
		<div class="row" id="actions-to">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-body">
						<?php if ($access_level['can_print']): ?>
							<a target="_blank" href="<?php echo base_url() . 'index.php/travel_orders/print_preview/'.$travel_id; ?>" class="btn btn-primary">Print</a>
						<?php endif; ?>
						<?php if ($access_level['can_cancel']): ?>
							<a href="#" class="btn btn-warning" id="cancel-to" data-toggle="modal" data-target="#cancel-modal">Cancel Travel</a>
						<?php endif ?>
						<?php if ($access_level['can_recommend']): ?>
							<a href="#" class="btn btn-primary" id="recommend-to">Recommend</a>
						<?php endif ?>
						<?php if ($access_level['can_approve']): ?>
							<a href="#" class="btn btn-primary" id="approve-to">Approve</a>
						<?php endif ?>
						<?php if ($access_level['can_decline']): ?>
							<a href="#" class="btn btn-danger" id="decline-to" data-toggle="modal" data-target="#decline-modal">Decline</a>
						<?php endif ?>
					</div>
				</div>
			</div>
		</div><!-- end row -->
		<?php endif; ?>
		<?php endif; ?>

		<div class="row" id="form-travel">
			<div class="col-lg-3">
				<div class="panel panel-default">
					<div class="panel-heading">Employee</div>
					<div class="panel-body">
						<div id="selected-employee">
							<!-- load/place selected employee here -->
						</div>
						<fieldset id="form-employee">
							<div class="form-group">
								<label class="form-control-label" for="">Name</label>
							  	<select type="text" class="form-control list-employee" id="employee_name">
							  	</select>
							</div>
							<div class="form-group">
								<label class="form-control-label" for="">Mobile No.</label>
							  	<input type="text" class="form-control" id="employee_mobile">
							</div>
							<div class="form-group">
								<label class="form-control-label" for="">Project Code</label>
							  	<select type="text" class="form-control list-project-code" id="project_code">
							  	</select>
							</div>

							<?php if ($access_level['can_edit']): ?>
							<button class="btn btn-primary" id="add-employee">Add</button>
							<button class="btn btn-primary _selected" id="new-employee">New</button>
							<button class="btn btn-primary _selected" id="edit-employee">Edit</button>
							<?php if ($this->session->userdata('type') == 'preparer'): ?>
							<button class="btn btn-danger _selected" id="delete-employee">Delete</button>
							<?php endif; ?>
							<?php endif; ?>
						</fieldset>
					</div>
				</div>
			</div>

			<div class="col-lg-3">
				<div class="panel panel-default">
					<div class="panel-heading">Destination</div>
					<div class="panel-body">
						<div id="selected-destination">
							<!-- load/place selected destination here -->
						</div>
						<fieldset id="form-destination">
							<div class="form-group" id="form-overseas">
								<label class="custom-control custom-radio">
								  	<input name="destination" type="radio" class="custom-control-input" value="form-within-ph">
								  	<span class="custom-control-indicator"></span>
								 	<span class="custom-control-description">Overseas</span>
								</label>
								<fieldset class="form-group _indent">
									<label class="form-control-label" for="">Address</label>
								  	<input type="text" class="form-control" id="overseas_address">
								</fieldset>
							</div>
							
							<div class="form-group" id="form-within-ph">
								<label class="custom-control custom-radio">
								  	<input name="destination" type="radio" class="custom-control-input" value="form-overseas" checked>
								  	<span class="custom-control-indicator"></span>
								 	<span class="custom-control-description">Within Philippines</span>
								</label>
								<fieldset>
									<div class="form-group _indent">
										<label class="form-control-label" for="">Office/Agency/Building</label>
									  	<select type="text" class="form-control list-office" id="office">
									  	</select>
									  	<input id="na-office" type="text" class="form-control" placeholder="Enter Office/Agency/Building name..." style="display: none;"></input>
									</div>
									<div class="form-group _indent">
										<label class="form-control-label" for="">City/Municipality</label>
									  	<select type="text" class="form-control list-city" id="city">
									  	</select>
									</div>
									<div class="form-group _indent">
										<label class="form-control-label" for="">Province</label>
									  	<select type="text" class="form-control list-province" id="province">
									  	</select>
									</div>
								</fieldset>
							</div>
							
							<?php if ($access_level['can_edit']): ?>
							<button class="btn btn-primary" id="add-destination">Add</button>
							<button class="btn btn-primary _selected" id="new-destination">New</button>
							<button class="btn btn-primary _selected" id="edit-destination">Edit</button>
							<button class="btn btn-danger _selected" id="delete-destination">Delete</button>
							<?php endif; ?>
						</fieldset>
					</div>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="panel panel-default">
					<div class="panel-heading">Details</div>
					<div class="panel-body">
						<fieldset id="form-details">
							<div class="form-group">
								<label class="form-control-label" for="">Tracking Number</label>
								  <input type="text" class="form-control" name="tracking_number">
							</div>
							<div class="form-group">
								<label class="form-control-label" for="">Purpose</label>
								<textarea class="form-control" name="purpose" rows="3"></textarea>
							</div>
							<div class="form-group">
								<label class="form-control-label" for="">Inclusive Dates of Travel</label>
								<div class="form-group _indent">
									<div class="row">
										<div class="col-lg-6">
											<label class="form-control-label" for="">From:</label>
											<input type="text" name="date_from" class="form-control datepicker from" readonly="">
										</div>
										<div class="col-lg-6">
											<label class="form-control-label" for="">To:</label>
											<input type="text" name="date_to" class="form-control datepicker to" readonly="">
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="form-control-label" for="">Mode of Transportation</label>
								<div class="form-group _indent">
									<label class="custom-control custom-radio">
									  	<input name="mode_of_transportation" value="PhilRice Vehicle" type="radio" class="custom-control-input">
									  	<span class="custom-control-indicator"></span>
									 	<span class="custom-control-description">PhilRice Vehicle</span>
									</label>
								</div>
								<div class="form-group _indent">
									<label class="custom-control custom-radio">
									  	<input name="mode_of_transportation" value="Private Vehicle" type="radio" class="custom-control-input">
									  	<span class="custom-control-indicator"></span>
									 	<span class="custom-control-description">Private Vehicle</span>
									</label>
								</div>
								<div class="form-group _indent">
									<label class="custom-control custom-radio">
									  	<input name="mode_of_transportation" value="others" type="radio" class="custom-control-input">
									  	<span class="custom-control-indicator"></span>
									 	<span class="custom-control-description">Others</span>
									</label>
									<input class="form-control" type="text" name="others_mode_of_transportation">
								</div>
							</div>
							<div class="form-group">
								<label class="form-control-label" for="">Vehicle Prioritization</label>
								<div class="form-group _indent">
									<label class="custom-control custom-radio">
									  	<input name="vehicle_prioritization" value="Mandatory" type="radio" class="custom-control-input">
									  	<span class="custom-control-indicator"></span>
									 	<span class="custom-control-description">Mandatory</span>
									</label>
								</div>
								<div class="form-group _indent">
									<label class="custom-control custom-radio">
									  	<input name="vehicle_prioritization" value="Necessary" type="radio" class="custom-control-input">
									  	<span class="custom-control-indicator"></span>
									 	<span class="custom-control-description">Necessary</span>
									</label>
								</div>
								<div class="form-group _indent">
									<label class="custom-control custom-radio">
									  	<input name="vehicle_prioritization" value="Last Priority" type="radio" class="custom-control-input">
									  	<span class="custom-control-indicator"></span>
									 	<span class="custom-control-description">Last Priority</span>
									</label>
								</div>
							</div>
							<div class="form-group">
								<label class="form-control-label" for="">Others <span style="font-style:italic;">(Pls Specify; e.g. estimate time of meeting, baggage, luggage)</span></label>
								<textarea class="form-control" name="others" rows="3"></textarea>
							</div>
							<div class="form-group">
								<label class="form-control-label" for="">Time of Departure <span style="font-style:italic;">(from origin)</span></label>
								<select type="text" name="time_of_departure" class="form-control">
									<option value=""></option>
									<option value="TBA">TBA</option>
								<?php 
									$meridiem = array("AM","PM");
									foreach ($meridiem as $m) {
										for ($i = 1; $i <= 12; $i++) {
											for ($j = 0; $j <= 59; $j++){
												if ($j%30 != 0) continue; 
												$time = ($i<10 ? "0".$i : $i) . ":" . ($j<10 ? "0".$j : $j) . " " . $m;
												?>
													<option value="<?php echo $time; ?>"><?php echo $time; ?></option>
												<?php
											}
										}
									}
								 ?>
								</select>
							</div>
							<div class="form-group">
								<label class="form-control-label" for="">Recommended By</label>
								<select type="text" class="form-control list-signatory" name="recommending_id">
								</select>
							</div>
							<div class="form-group">
								<label class="form-control-label" for="">Approved By</label>
								<select type="text" class="form-control list-signatory" name="approving_id">
								</select>
							</div>
						</fieldset>

						<?php if ($access_level['can_edit']): ?>
						<button class="btn btn-primary _group-1" id="save-travel">Save</button>
						<button class="btn btn-danger _group-1" id="cancel-travel">Cancel</button>
						<button class="btn btn-primary _group-2" id="edit-travel">Edit</button>
						<?php endif; ?>
						<?php if ($this->session->userdata('type') == 'admin'): ?>
						<button class="btn btn-danger _group-2" id="delete-travel">Delete</button>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div><!--/.row-->

		<!-- load modals -->
		<?php if ( !empty($travel_id)) $this->load->view('travel_orders/modals'); ?>

		<script type="text/javascript">	
			var listProvinces = <?php echo json_encode($province); ?>;
			var listCities = <?php echo json_encode($city); ?>;
			var listOffices = <?php echo json_encode($office); ?>;
			var listEmployees = <?php echo json_encode($employees); ?>;
			var listSignatories = <?php echo json_encode($signatories); ?>;
			var listProjectCodes = <?php echo json_encode($project_codes); ?>;
			var baseUrl = "<?php echo base_url() . 'index.php/travel_orders/'; ?>";

			/*Employees*/
			(function(){
				$('.list-employee').append($("<option></option>")); 
				for(i in listEmployees) {
					$('.list-employee')
							.append($("<option></option>")
				                .attr("value", listEmployees[i].employee_id)
				                .attr("data-mobile", listEmployees[i].mobile_number)
				                .text(listEmployees[i].name)
				                <?php if ($this->session->userdata('type')!='preparer'): ?>
										.prop('selected', true)
								<?php endif; ?>
				    );
				}
			})();

			/*Project Codes*/
			(function(){
				$('.list-project-code').append($("<option></option>")); 
				for(i in listProjectCodes) {
					$('.list-project-code')
							.append($("<option></option>")
				                .attr("value", listProjectCodes[i].ProjectCode)
				                .text(listProjectCodes[i].ProjectCode)); 
				}
			})();

			/*Offices*/
			(function(){
				$('.list-office').append($("<option></option>")); 
				$('.list-office').append($("<option value=\"na\">Not on the list?</option>")); 
				for(i in listOffices) {
					$('.list-office')
							.append($("<option></option>")
				                .attr("value", listOffices[i].officeCode)
				                .attr("data-citymunCode", listOffices[i].citymunCode)
				                .attr("data-provCode", listOffices[i].provCode)
				                .text(listOffices[i].officeDesc)); 
				}
			})();

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

			/*Signatory*/
			(function(){
				$('.list-signatory').append($("<option></option>")); 
				for(i in listSignatories) {
					$('.list-signatory')
							.append($("<option></option>")
				                .attr("value", listSignatories[i].employee_id)
				                .text(listSignatories[i].name)); 
				}
			})();

			<?php if ( !empty($travel_id) ): ?>
			(function(){
				$('#save-travel').attr('data-value', <?php echo $travel_id; ?>);

				var details = <?php echo json_encode($details); ?>;
				var employees = <?php echo json_encode($selected_employees); ?>;
				var destinations = <?php echo json_encode($destinations); ?>;

				for(i in employees){
					$('#selected-employee').append(
						$('<div>').attr('data-id', employees[i].employee_id)
									.attr('data-mobile', employees[i].mobile_number)
									.attr('data-projectcode', employees[i].project_code)
									.addClass('added-item')
									.html('<div class="name">'+ employees[i].name +'</div>'+
										'<div class="mobile-no">'+ employees[i].mobile_number +'</div>'+
										'<div class="project-code">'+ employees[i].project_code +'</div>')
					);
				}

				for(i in destinations){
					var text = '';
					var data = {};
						data.origin = destinations[i].overseas == '' ? 'ph':'overseas';
						data.overseas = destinations[i].overseas;
						data.newoffice = destinations[i].officeName;
						data.office_id = destinations[i].officeCode;
						data.city_id = destinations[i].citymunCode;
						data.province_id = destinations[i].provCode;
						for (j in listOffices) {
							if ( listOffices[j].officeCode == destinations[i].officeCode)
								data.office = listOffices[j].officeDesc;
						}
						for (j in listCities) {
							if ( listCities[j].citymunCode == destinations[i].citymunCode)
								data.city = listCities[j].citymunDesc;
						}
						for (j in listProvinces) {
							if ( listProvinces[j].provCode == destinations[i].provCode)
								data.province = listProvinces[j].provDesc;
						}

					if ( data.origin == 'ph' ) {
						text = (data.newoffice != "" ? data.newoffice+", ":"") + (data.office_id != "" && data.office_id != "na" ? data.office+", ":"") + (data.city != "" ? data.city+", ":"") + data.province;
					} else {
						text = data.overseas;
					}
					$('#selected-destination').append(
						$('<div>').attr('data-details', JSON.stringify(data))
									.text(text)
									.addClass('added-item')
					);
				}

				for ( i in details) {
					$('[name="tracking_number"]').val(details[i].tracking_number);
					$('[name="purpose"]').val(details[i].purpose);
					$('[name="date_from"]').val(details[i].date_from);
					$('[name="date_to"]').val(details[i].date_to);
					if (details[i].mode_of_trasportation != 'PhilRice Vehicle' && 
						details[i].mode_of_trasportation != 'Private Vehicle') {
						$('[name="mode_of_transportation"][value="others"]').prop('checked', true);
						$('[name="others_mode_of_transportation"]').val(details[i].mode_of_trasportation);
					} else {
						$('[name="others_mode_of_transportation"][value="'+details[i].mode_of_trasportation+'"]').prop('checked', true);
					}
					$('[name="vehicle_prioritization"][value="'+details[i].vehicle_prioritization+'"]').prop('checked', true);
					$('[name="others"]').val(details[i].others);
					$('[name="time_of_departure"]').find('option[value="'+details[i].time_of_departure+'"]').prop('selected', true);
					$('[name="recommending_id"]').find('option[value="'+details[i].recommending_id+'"]').prop('selected', true);
					$('[name="approving_id"]').find('option[value="'+details[i].approving_id+'"]').prop('selected', true);
				}
				
			})();

			<?php endif; ?>
		</script>

		<script type="text/javascript">
			
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

				if ( $('.list-office').val() == "na" ) return;
				$('.list-office').find('option').each(function(){
					if (this.value == '') $(this).prop('selected', true);
					$('#na-office').hide();
				});
			});

			$('.list-city').on('change', function(){
				var self = this;
				$('.list-province').find('option').each(function(){
					if ( self.value.indexOf(this.value) != 0) {
						
					} else {
						$(this).prop('selected', true);
					}
				});

				if ( $('.list-office').val() == "na" ) return;
				$('.list-office').find('option').each(function(){
					if (this.value == '') $(this).prop('selected', true);
					$('#na-office').hide();
				});
			});

			$('.list-office').on('change', function(){
				var self = this;
				$('.list-province').find('option').each(function(){
					if ( $(self).find('option:selected').data('provcode') != this.value) {
						//$(this).hide();
					} else {
						$(this).show();
						$(this).prop('selected', true);
					}
				});

				$('.list-city').find('option').remove(); 
				$('.list-city').append($("<option></option>")); 
				for(i in listCities) {
					if ( listCities[i].citymunCode.indexOf($('.list-province').val()) != 0 ) continue;
					$('.list-city')
							.append($("<option></option>")
				                .attr("value", listCities[i].citymunCode)
				                .text(listCities[i].citymunDesc)
				                .prop('selected', (listCities[i].citymunCode == $(self).find('option:selected').data('citymuncode')) ? true:false)); 
				}

				if ( this.value == 'na') {
					$('#na-office').val('').show();
				} else {
					$('#na-office').hide();
				}
			});

			$('input[name="destination"]').on('change', function(){
				$('fieldset').prop('disabled', false);
				$('#'+this.value).find('fieldset').prop('disabled', true);
			}); 
			$('input[name="destination"]:checked').trigger('change');


			$('input[name="mode_of_transportation"][type="radio"]').on('change', function(){
				if ( this.value == "others" ) $('input[name="others_mode_of_transportation"][type="text"]').prop('disabled', false);
				else $('input[name="others_mode_of_transportation"][type="text"]').prop('disabled', true);
			});
			$('input[name="mode_of_transportation"]:first').prop('checked', true).trigger('change');
			$('input[name="vehicle_prioritization"]:first').prop('checked', true).trigger('change');


			$(document).ready(function(){
				<?php if ( !empty($travel_id) ): ?>
				$('#form-employee').prop('disabled', true);
				$('#form-destination').prop('disabled', true);
				$('#form-details').prop('disabled', true);
				$('.added-item').addClass('disabled-added-item').removeClass('added-item');

				$('#edit-travel').on('click', function(){
					$('#form-employee').prop('disabled', false);
					$('#form-destination').prop('disabled', false);
					$('#form-details').prop('disabled', false);
					$('#selected-destination .disabled-added-item').addClass('added-item').removeClass('disabled-added-item');
					$('#selected-employee .disabled-added-item').each(function(){
						var myID = '<?php echo $this->session->userdata("employee_id"); ?>';
						var myType = '<?php echo $this->session->userdata("type"); ?>';
						if ($(this).data('id') != myID && myType == 'regular' ) return;
						$(this).addClass('added-item').removeClass('disabled-added-item');
					});


					$('.btn._group-1').show();
					$('.btn._group-2').hide();
				});

				$('#delete-travel').on('click', function(){
					var res = confirm('Are you sure you want to delete this travel order?');
					if (!res) return false;

					$.ajax({
						url: baseUrl+'delete_travel_order',
						method: 'POST',
						data: {
							travel_id: <?php echo $travel_id; ?>
						}, 
						success: function(response){
							$('#actions-to').remove();
							$('#form-travel .btn').remove();
							$('#status-to > div').html('<div class="alert bg-danger" role="alert"><svg class="glyph stroked cancel"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-cancel"></use></svg> This travel order has been deleted. <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a></div>');
							
						}
					});
				});

				$('#recommend-to').on('click', function(){
					var res = confirm('Are you sure you want to recommend this travel order?');
					if (!res) return false;

					$.ajax({
						url: baseUrl+'recommend_travel_order',
						method: 'POST',
						data: {
							travel_id: <?php echo $travel_id; ?>
						}, 
						success: function(response){
							$('#actions-to > div').html(response);
						}
					});
				});

				$('#approve-to').on('click', function(){
					var res = confirm('Are you sure you want to approve this travel order?');
					if (!res) return false;

					$.ajax({
						url: baseUrl+'approve_travel_order',
						method: 'POST',
						data: {
							travel_id: <?php echo $travel_id; ?>
						}, 
						success: function(response){
							$('#actions-to > div').html(response);
						}
					});
				});

				$('#submit-cancel-to').on('click', function(){
					var res = confirm('Are you sure you want to cancel this travel order?');
					if (!res) return false;
					
					$('.cancel-modal').trigger('click');

					$.ajax({
						url: baseUrl+'cancel_travel_order',
						method: 'POST',
						data: {
							travel_id: <?php echo $travel_id; ?>,
							remarks: $('#cancel_message').val()
						}, 
						success: function(response){
							$('#actions-to > div').html(response);
						}
					});
				});

				$('#submit-decline-to').on('click', function(){
					var res = confirm('Are you sure you want to decline this travel order?');
					if (!res) return false;
					
					if($('#decline_message').val()=='') {alert('Please enter remarks.'); return false;}

					$('.cancel-modal').trigger('click');

					$.ajax({
						url: baseUrl+'decline_travel_order',
						method: 'POST',
						data: {
							travel_id: <?php echo $travel_id; ?>,
							remarks: $('#decline_message').val(),
							access: '<?php echo $access_level["type"]; ?>'
						}, 
						success: function(response){
							$('#actions-to > div').html(response);
						}
					});
				});
				<?php endif; ?>

				<?php if ($this->session->userdata('type')!='preparer'): ?>
					$('.list-employee').trigger('change')
				<?php endif; ?>

				$('.datepicker.from').datepicker({
					minDate: 'today',
					onSelect: function(){
						$('.datepicker.to').datepicker("option", "minDate", $(this).val());
						$(this).trigger('change');
					}
				});

				$('.datepicker.to').datepicker({
					minDate: 'today'
				});

				$('.datepicker.from').on('change', function(){
					var from = $(this).val();
					var today = "<?php echo date('m/d/Y'); ?>";

					if ( from == today ) {
						$.ajax({
							url: "<?php echo site_url(); ?>/travel_orders/generate_time_of_departure",
							method: 'POST',
							success: function(response){
								var time = JSON.parse(response);
								$('select[name="time_of_departure"] option').hide();
								$('select[name="time_of_departure"]').find('option[value=""]').show();
								$('select[name="time_of_departure"]').find('option[value="TBA"]').show();
								for(i in time) {
									$('select[name="time_of_departure"]').find('option[value="'+ time[i] +'"]').show();
								}
							}
						});
					} else {
						$('select[name="time_of_departure"] option').show();
					}

				});
			});
		</script>

		<script src="<?php echo base_url(); ?>assets/js/travel_orders/create-travel-order.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/travel_orders/travel-order-actions.js"></script>