	

	/* 
     * Start Employee Script
     *
	 */

	// setting mobile number when employee is selected
	$('.list-employee').on('change', function(){
		$('#employee_mobile').val($(this).find('option:selected').data('mobile'));
	});

	// adding of employees
	$('#add-employee').on('click', function(){
		var employee = {};
		employee.name = $('#employee_name').find('option:selected').text();
		employee.id = $('#employee_name').val();
		employee.mobile = $('#employee_mobile').val();
		employee.project_code = $('#project_code').val();

		for ( i in employee ) {
			if ( employee[i] == '' ) {
				alert("Please fill-up Employee's Name, Mobile No. and Project Code. Thank you.");
				return false;
			}
		}

		$('#selected-employee').append(
			$('<div>').attr('data-id', employee.id)
				.attr('data-mobile', employee.mobile)
				.attr('data-projectcode', employee.project_code)
				.addClass('added-item')
				.html('<div class="name">'+ employee.name +'</div>'+
					'<div class="mobile-no">'+ employee.mobile +'</div>'+
					'<div class="project-code">'+ employee.project_code +'</div>')
		);

		resetEmployee();
	});

	$('#selected-employee').on('click', '.added-item', function(){
		var employee = {};
		employee.id = $(this).data('id');
		employee.mobile = $(this).data('mobile');
		employee.project_code = $(this).data('projectcode');

		$('#employee_name').prop('disabled', true).find('option[value="'+ employee.id +'"]').prop('selected', true);
		$('#employee_mobile').val(employee.mobile);
		$('#project_code').val(employee.project_code);

		$('#add-employee').hide();
		$('#form-employee .btn._selected').show();
		$('#selected-employee .added-item').removeClass('active');
		$(this).addClass('active');
	});

	// remove active and display add button
	$('#new-employee').on('click', function(){
		resetEmployee();
	});

	// update selected employee
	$('#edit-employee').on('click', function(){
		var employee = {};
		employee.mobile = $('#employee_mobile').val();
		employee.project_code = $('#project_code').val();

		$('#selected-employee .added-item.active').data('mobile', employee.mobile)
								.find('.mobile-no').text(employee.mobile);

		$('#selected-employee .added-item.active').data('projectcode', employee.project_code)
								.find('.project-code').text(employee.project_code);
		
		resetEmployee();
	});

	// delete selected employee
	$('#delete-employee').on('click', function(){
		$('#selected-employee .added-item.active').remove();	
		resetEmployee();
	});

	function resetEmployee(){
		//fields
		$('#employee_name').prop('disabled', false).val('');
		$('#employee_mobile').val('');
		$('#project_code').val('');

		//buttons
		$('#add-employee').show();
		$('#form-employee .btn._selected').hide();
		$('#selected-employee .added-item').removeClass('active');

		filterEmployeeOption();
	}

	// filter out employees and signatory to prevent duplication
	function filterEmployeeOption(){
		// get all selected employee from html
		// return array
		var selected = [];
		$('#selected-employee .added-item').each(function(){
			selected.push($(this).data('id'));
		});

		$('.list-signatory').each(function(){
			selected.push($(this).val());
		});

		$('.list-employee option').show();
		$('.list-signatory option').show();
		for (i in selected) {
			$('.list-employee').find('option[value="'+ selected[i] +'"]').hide();
			$('.list-signatory').find('option[value="'+ selected[i] +'"]').hide();
		}
	}
	filterEmployeeOption();

	/*
     * End Employee Script
     *
	 */


	 /*
      * Start Destination Script
      *
	  */

	$('#add-destination').on('click', function(e, edata=""){
		var destination = $('[name="destination"]:checked').val();
		var has_theSame = false;
		var text = '';
		var data = {};
			data.origin = '';
			data.overseas = '';
			data.newoffice = '';
			data.office_id = '';
			data.city_id = '';
			data.province_id = '';
			data.office = '';
			data.city = '';
			data.province = '';

		if ( destination == 'form-overseas' ) {

			// get form within philippines fields
			data.origin = "ph";
			data.office_id = $('#office').val();
			data.city_id = $('#city').val();
			data.province_id = $('#province').val();
			data.office = $('#office').find('option:checked').text();
			data.city = $('#city').find('option:checked').text();
			data.province = $('#province').find('option:checked').text();

			// if office not on the list
			if ( data.office_id == 'na' ) {
				data.newoffice = $('#na-office').val();
				if (data.newoffice == '') {
					alert("Please fill-up office/agency/building name. Thank you.");
					return false;
				}
			}

			if ( data.province == '' ) {
				alert("Please select at least a province. Thank you.");
				return false;
			}

			text = (data.newoffice != "" ? data.newoffice+", ":"") + (data.office_id != "" && data.office_id != "na" ? data.office+", ":"") + (data.city != "" ? data.city+", ":"") + data.province;
		} 
		if ( destination == 'form-within-ph') {

			// get form overseas fields
			data.origin = "overseas";
			text = data.overseas = $('#overseas_address').val();

			if ( data.overseas == '' ) {
				alert("Please enter the Overseas's Address. Thank you.");
				return false;
			}
		}

		// if destination has the same destination
		// if edit true not run
		if ( edata.edit != true ) {
			$('#selected-destination .added-item').each(function(){
				if ( text == $.trim($(this).text()) ) has_theSame = true;
	 		});
	 		if ( has_theSame) {
	 			alert("This destination has already been added to selected destinations.");
	 			return false;
	 		}
		}

		var div = $('<div>').attr('data-details', JSON.stringify(data))
					.text(text)
					.addClass('added-item');	

		if ( edata.edit != true ) {
			$('#selected-destination').append(div);
			resetDestination();
		} else {
			$('#selected-destination .added-item.active').replaceWith(div);
		}
	});

	$('#selected-destination').on('click', '.added-item', function(){
		var data = $(this).data('details');

		$('[name="destination"]').prop('disabled', true);
		if ( data.origin == "ph" ) {
			$('[name="destination"][value="form-overseas"]').prop('checked', true);
			$("#office").find('option[value="'+ data.office_id +'"]').prop('selected', true);
			$("#city").find('option[value="'+ data.city_id +'"]').prop('selected', true);
			$("#province").find('option[value="'+ data.province_id +'"]').prop('selected', true);
			if (data.newoffice != '') {
				$("#newoffice").val(data.newoffice).show();
			} else {
				$("#newoffice").val('').hide();
			}
		}
		if ( data.origin == "overseas") {
			$('[name="destination"][value="form-within-ph"]').prop('checked', true);
			$("#overseas_address").val(data.overseas);
		}
		
		$('#add-destination').hide();
		$('#form-destination .btn._selected').show();
		$('#selected-destination .added-item').removeClass('active');
		$(this).addClass('active');
	});

	// reset destination fields and buttons
	$('#new-destination').on('click', function(){
		resetDestination();
	});

	// replace selected destination
	$('#edit-destination').on('click', function(){
		$('#add-destination').trigger('click', {edit:true});
	});

	// delete selected destination
	$('#delete-destination').on('click', function(){
		$('#selected-destination .added-item.active').remove();	
		resetDestination();
	});

	function resetDestination(){
		//fields
		$('[name="destination"]').prop('disabled', false);
		$('#overseas_address').val('');
		$('#office').val('');
		$('#newoffice').val('').hide();
		$('#city').val('');
		$('#province').val('');

		//buttons
		$('#add-destination').show();
		$('#form-destination .btn._selected').hide();
		$('#selected-destination .added-item').removeClass('active');
	}

	/*
     * End Destination Script
     *
	 */


	/*
     * Start Validation Script
     *
	 */

	 $('.list-signatory').on('change', function(){
	 	$('.list-signatory option').show();
	 	$('.list-signatory').find('option[value="'+ this.value +'"]').hide();

	 	// remove from employee list if selected
	 	if ( $('.list-employee').find('option[value="'+ this.value +'"]').length > 0 ) {
	 		$('.list-employee').find('option[value="'+ this.value +'"]').hide();
	 	} else {
	 		filterEmployeeOption();
	 	}

	 });

	 /*
     * End Save Script
     *
	 */


	/*
     * Start Save Script
     *
	 */

	$('#save-travel').on('click', function(){
		var res = confirm('Are you sure you want to save this travel order?');
		if (!res) return false;

		// check required details
		if ( $('#selected-employee .added-item').length == 0 ) {alert('Please add atleast one employee.'); return false;}
		if ( $('#selected-employee .added-item.unavailable').length > 0 ) {alert('Please check employee availability.'); return false;}
		if ( $('#selected-destination .added-item').length == 0 ) {alert('Please add atleast one destination.'); return false;}
		if ( $('[name="tracking_number"]').val() == '' ) {alert('Please fill-up Tracking Number.'); return false;}
		if ( $('[name="purpose"]').val() == '' ) {alert('Please fill-up Purpose.'); return false;}
		if ( $('[name="date_from"]').val() == '' ) {alert('Please fill-up Date From.'); return false;}
		if ( $('[name="date_to"]').val() == '' ) {alert('Please fill-up Date To.'); return false;}
		if ( $('[name="mode_of_transporation"]:checked').val() == 'others' && $('[name="others_mode_of_transportation"]').val() == '') {alert('Please fill-up Mode of Transporation.'); return false;}
		if ( $('[name="vehicle_prioritization"]:checked').val() == '') {alert('Please fill-up Time of Departure.'); return false;}
		if ( $('[name="time_of_departure"]').val() == '') {alert('Please fill-up Time of Departure.'); return false;}
		if ( $('[name="approving_id"]').val() == '') {alert('Please fill-up Approved by.'); return false;}
		if ( !isTimeValid() ) {alert('Please check inclusive dates and time of departure.'); return false;}

		//convert selected employees and destination to input
		$('#selected-employee .added-item').each(function(i){
			$('#selected-employee')
				.append($('<input type="hidden" name="employee['+ i +'][employee_id]">').val( $(this).data('id')))
				.append($('<input type="hidden" name="employee['+ i +'][mobile_number]">').val( $(this).data('mobile')))
				.append($('<input type="hidden" name="employee['+ i +'][project_code]">').val( $(this).data('projectcode')));
		});

		$('#selected-destination .added-item').each(function(i){
			var data = $(this).data('details');
			$('#selected-destination')
				.append($('<input type="hidden" name="destinations['+ i +'][overseas]">').val( data.overseas))
				.append($('<input type="hidden" name="destinations['+ i +'][officeCode]">').val( data.office_id))
				.append($('<input type="hidden" name="destinations['+ i +'][citymunCode]">').val( data.city_id))
				.append($('<input type="hidden" name="destinations['+ i +'][provCode]">').val( data.province_id))
				.append($('<input type="hidden" name="destinations['+ i +'][officeName]">').val( data.newoffice));
		});

		$('#form-travel').wrap('<form id="form"></form>');
		var data = $('#form').serialize();
		$('#form-travel').unwrap("");

		// submit
		$.ajax({
			url: baseUrl+'save_travel_order',
			method:'POST',
			data:{
				data: data,
				travel_id: $(this).data('value') != undefined ? $(this).data('value'):''
			},
			success: function(response){
				if ( !isNaN( response) )
					window.location.href = baseUrl + 'view_travel/'+ response;
			}
		});
	});

	function isDateValid(){
		var mindate = $.datepicker.formatDate('mm/dd/yy', new Date());
		var date_from = $('[name="date_from"]').val();
		return mindate < date_from;
	}

	function isTimeValid(){
		var mindate = $.datepicker.formatDate('mm/dd/yy', new Date());
		var date_from = $('[name="date_from"]').val();
		if (mindate > date_from) return false;
		if (mindate < date_from) return true;

		var time_of_departure = $('[name="time_of_departure"]').val();
		var datetime = new Date().toLocaleTimeString();
		var pcsTimeOfDeparture = '';
		var pcsTimeNow = '';
		var departure = {};
		var now = {};

		if ( time_of_departure != '' && time_of_departure != 'TBA' ) {
			pcsTimeOfDeparture = time_of_departure.split(':');
			pcsTimeNow = datetime.split(':');

			departure.hour = parseInt(pcsTimeOfDeparture[0]);
			departure.minute = parseInt(pcsTimeOfDeparture[1].split(' ').shift());
			departure.meridiem = pcsTimeOfDeparture[1].split(' ').pop();

			now.hour = parseInt(pcsTimeNow[0]);
			now.minute = parseInt(pcsTimeNow[1]);
			now.meridiem = pcsTimeNow[2].split(' ').pop();

			now.meridiem = now.meridiem == 'AM' ? 0:12;
			departure.meridiem = departure.meridiem == 'AM' ? 0:12;

			now.totalhours = now.hour + now.meridiem;
			departure.totalhours = departure.hour + departure.meridiem;

			now.totalMinutes = now.totalhours * 60 + now.minute;
			departure.totalMinutes = departure.totalhours * 60 + departure.minute;

			return now.totalMinutes < departure.totalMinutes;
		}

		return true;
	}

	/*
     * End Save Script
     *
	 */

	 /*
     * Start Cancel Script
     *
	 */
	
	$('#cancel-travel').on('click', function(){
		window.location.reload();
	});

	/*
     * End Cancel Script
     *
	 */



	$('.datepicker').on('change', validateEmployeeAvailability);
	$('#add-employee').on('click', validateEmployeeAvailability);
	function validateEmployeeAvailability(){
		var employees_id = $.map($('#selected-employee').find('.added-item'), function(el, i){
			return $(el).data('id');
		});
		var date_from = $('[name="date_from"]').val();
		var date_to = $('[name="date_to"]').val();
		//date_from = '02/20/2017';
		//date_to = '02/20/2017';
		if (employees_id.length == 0) return false;
		if (date_from == '') return false;
		if (date_to == '') return false;

		$.ajax({
			url: baseUrl+'check_employee_availability',
			method:'POST',
			data:{
				date_from: date_from,
				date_to: date_to,
				employees_id: employees_id,
				travel_id: $('#save-travel').data('value') != undefined ? $('#save-travel').data('value'):''
			},
			success: function(response){
				response = JSON.parse(response);
				var ids = response.employee_ids;
				
				$('#selected-employee').find('.added-item').removeClass('unavailable');

				if (ids.length > 0) {
					$('#save-travel').attr('data-ready', "false");
					alert(response.msg);
					for ( i in ids ) {
						$('#selected-employee').find('.added-item[data-id="'+ids[i]+'"]').addClass('unavailable');
					}
				} else {
					$('#save-travel').attr('data-ready', "true");
				}
			}
		});
	}

	/*
     * Mobile Number characters only
     *
	 */
	$('#employee_mobile').on('keypress', function(e){
		var k = e.which;
		var c = String.fromCharCode(k);
		var regex = /[0-9-+]/g;
		if (!regex.test(c)) return false;
	});