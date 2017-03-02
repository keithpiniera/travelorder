	function initDatepicker() {
		$('.datepicker').removeClass('hasDatepicker').attr('id', '');

		$('.emp-in-to').each(function(){
			var self = this;
			$(this).find('.datepicker.from').datepicker({
				minDate: '+1d',
				onSelect: function(){
					$(self).find('.datepicker.to').datepicker("option", "minDate", $(this).val());
				}
			});

			$(this).find('.datepicker.to').datepicker({
				minDate: $(self).find('.datepicker.from').val()
			});
		});
	} initDatepicker();

	function addEmployeeRow() {
		
		for (var i = 0; i < $('.add-control input').val(); i++) {
			var clone = $('.emp-in-to:last').clone();
			clone.insertAfter($('.emp-in-to:last')).find('[name="emp[$][id]"], [name="emp[$][name]"], [name="emp[$][mobile_number]"]').val('');
		}
		$('.add-control input').val(1);
		initDatepicker();
	}

	$('.add-control button').on('click', addEmployeeRow);
	$('.add-control input#add').on('keyup', function(e){
		var key = e.which;
		if ( key == 13 ) addEmployeeRow();
	});

	$('body').on('click', '.x-btn', function(){
		$(this).parent().parent().remove();
	});

	$('body').on('change', 'select[name="emp[$][employee_id]"]', function(){
		var gparent = $(this).parent().parent();
		if ( gparent.find('input[name="emp[$][mobile_number]"]').length == 1 ) 
			gparent.find('input[name="emp[$][mobile_number]"]').val( $(this).find('option:selected').data('mobile') );
	});

	$('body').on('click', ':not(#employees)', function(){
		$('#employees').hide();
		$('input.employee').attr('data-list', '');
	});


	$('body').on('click', '#project-codes > div', function(){
		var input = $('input.project-code[data-list="active"]');
		input.val( $(this).text() );
	});

	$('body').on('click', ':not(#project-codes)', function(){
		$('#project-codes').hide();
		$('input.project-code').attr('data-list', '');
	});

	function validateTravel(){
		var fields = [];
		// atleast one person in travel
			// with name mobile no. proj code purpose and date
			// if employee row must complete
		$('.emp-in-to').each(function(){
			if ( empty($(this).find('select[name="emp[$][name]"]')) || empty($(this).find('input[name="emp[$][employee_id]"]')) ) {
				if ( $.inArray('Employee\'s Name', fields) == -1 ) {
					fields.push('Employee\'s Name');
				}
			}
			if ( empty($(this).find('input[name="emp[$][mobile_number]"]'))) {
				if ( $.inArray('Mobile Number', fields) == -1 ) {
					fields.push('Mobile Number');
				}
			}
			if ( empty($(this).find('select[name="emp[$][project_code]"]'))) {
				if ( $.inArray('Project Code', fields) == -1 ) {
					fields.push('Project Code');
				}
			}
			if ( empty($(this).find('textarea[name="emp[$][purpose]"]'))) {
				if ( $.inArray('Purpose', fields) == -1 ) {
					fields.push('Purpose');
				}
			}
			if ( empty($(this).find('input[name="emp[$][travel_date_from]"]')) || empty($(this).find('input[name="emp[$][travel_date_to]"]')) ){
				if ( $.inArray('Inclusive Date/s of Travel', fields) == -1 ) {
					fields.push('Inclusive Date/s of Travel');
				}
			}
		});

		// mode of transportaion not empty
		if ( empty('input[name="mode_of_transportation"]') ) {
			fields.push('Mode of Transportation');
		}
		// if not choosen philrice vehicle 
			// vehicle prioritization should be disable?
		if ( empty('input[name="vehicle_prioritization"]') ) {
			fields.push('Vehicle Prioritization');
		}

		// approved by not empty
		if ( empty('select[name="approving_id"]') ) {
			fields.push('Approved by');
		}

		return fields;
	}

	function empty(elem){
		return $(elem).val() == "";
	}

	$('.btn-save').on('click', function(){
		var decision = confirm('Are you sure you want to save this?');
		if (!decision) return false;

		var fields = validateTravel();
		if ( fields.length > 0 ) {
			alert('Cannot be saved because following field/s are empty: \n\n' + fields.join('\n') );
			return false;
		}

		if ($('#destinations > div').length == 0) {
			alert('Please add at least one destination.');
			return false;
		}
		$('#destinations > div').each(function(i){
			var addressVar = {regCode:'', provCode:'', citymunCode:'', brgyCode:'', officeCode:'', 'newOfficeCode':''};
			for (j in addressVar) {
				$('#destinations').append( $('<input type="hidden" name="destination['+ i +']['+ j +']" value="'+ $(this).data(j.toLowerCase()) +'">') );
			}
		});

		var empCtr = 0;
		$('.emp-in-to').each(function(){
			$(this).find('input, textarea, select').each(function(){
				var empName = $(this).attr('name');
				empName     = empName.replace('$', empCtr)
				$(this).attr('name', empName);
			});
			empCtr++;
		});

		$('.form').wrap('<form></form>');
		var travel = $('form').serialize();
		$('.form').unwrap();
		
		$.ajax({
         type: "POST",
         url: "http://192.168.11.65/travelorder/index.php/travel_orders/save_travel", 
         data: {
         	travel: travel
         },
         success: function(data){
         		if (!isNaN(data))
         			window.location.href = 'http://192.168.11.65/travelorder/index.php/travel_orders/view/'+data;
         		else console.log(data);
            }
        });
	});



	function getTravels(status,per_page="",cur_page="") {

		$.ajax({
         type: "POST",
         url: "http://192.168.11.65/travelorder/index.php/travel_orders/get_travels", 
         data: {
         	status: status,
         	per_page: per_page,
         	cur_page: cur_page
         },
         success: function(data){
         		data = JSON.parse(data);

         		var list = '';
                for (var i = 0; i < data.length; i++) {
                	
                	var recommend_btn = 
				  				'<button class="btn btn-success btn-xs" title="Approve" onclick="recommendTravelOrder('+ data[i].travel_id +')">'+
                                  	'<span class="glyphicon glyphicon-ok recommend-glph"></span>'+
                                '</button>'+
                                '<button class="btn btn-danger btn-xs" title="Decline" onclick="openDeclineModal('+ data[i].travel_id +',\'recommend\')">'+
                                    '<span class="glyphicon glyphicon-remove recommend-decline-glph"></span>'+
                                '</button>';

                    var approve_btn = 
                    			'<button class="btn btn-success btn-xs" title="Approve" onclick="approveTravelOrder('+ data[i].travel_id +')">'+
                                  	'<span class="glyphicon glyphicon-ok approve-glph"></span>'+
                                '</button>'+
                                '<button class="btn btn-danger btn-xs" title="Decline" onclick="openDeclineModal('+ data[i].travel_id +',\'approve\')">'+
                                    '<span class="glyphicon glyphicon-remove approve-decline-glph"></span>'+
                                '</button>';

                    var btn1 = 
                    		'<a href="http://192.168.11.65/travelorder/index.php/travel_orders/view/'+ data[i].travel_id +'">'+
					        	'<button class="btn btn-info btn-xs">'+
					        		'<span class="glyphicon glyphicon-eye-open"></span>'+ 
					        	'</button>'+
					       	'</a>'+
                            '<button class="btn btn-primary btn-xs" onclick="getEmployeesInTravelOrder('+ data[i].travel_id +')" style="background-color: #FF7e47; border-color: #FF7e47;" data-toggle="modal" data-target="#select-employee"><span class="glyphicon glyphicon-print"></span></button>';
                                

                    var btn2 = '<button data-travel_id="'+ data[i].travel_id +'" class="btn btn-danger btn-delete btn-xs list" onclick="" title="delete"><span class="glyphicon glyphicon-trash"></span></button>';
                    var btn3 = '<button data-travel_id="'+ data[i].travel_id +'" class="btn btn-warning btn-cancel btn-xs list" onclick="" title="cancel"><span class="glyphicon glyphicon-ban-circle"></span></button>';

                    if ( status == 'pending' ) {
                    	if ( data[i].recommend == 2 ) {
 							recommend_btn = 'Recommended';
	 					} else {
	 						recommend_btn = '--';
	 					}
                    	approve_btn = '--';
                    }
                    if ( status == 'cancelled' ) {
                    	btn1 = '<a href="http://192.168.11.65/travelorder/index.php/travel_orders/view/'+ data[i].travel_id +'">'+
					        	'<button class="btn btn-info btn-xs">'+
					        		'<span class="glyphicon glyphicon-eye-open"></span>'+ 
					        	'</button>'+
					       	'</a>';
					    btn3 = '';
					    if ( data[i].recommend == 1 ) {
                   			recommend_btn = "Declined: " + data[i].recommend_remarks;
	 					} else if ( data[i].recommend == 2 ) {
	 						recommend_btn = 'Recommended';
	 					} else  {
	 						recommend_btn = '--';
	 					}

	 					if ( data[i].approve == 1 ) {
 							approve_btn = "Declined: " + data[i].approve_remarks;
	 					} else if ( data[i].approve == 2 ) {
	 						approve_btn = 'Approved';
	 					} else {
	 						approve_btn = '--';
	 					}
                    }
                   	if ( status == 'declined' ) {
                   		if ( data[i].recommend == 1 ) {
                   			recommend_btn = "Declined: " + data[i].recommend_remarks;
	 					} else if ( data[i].recommend == 2 ) {
	 						recommend_btn = 'Recommended';
	 					} else  {
	 						recommend_btn = '--';
	 					}

	 					if ( data[i].approve == 1 ) {
 							approve_btn = "Declined: " + data[i].approve_remarks;
	 					} else if ( data[i].approve == 2 ) {
	 						approve_btn = 'Approved';
	 					} else {
	 						approve_btn = '--';
	 					}
                   		
                   	}
                   	if ( status == 'approved' ) {
                   		recommend_btn = 'Recommended';
                   		if ( data[i].recommend == 3 ) {
 							recommend_btn = '--';
	 					}
                   		approve_btn = 'Approved';
                   	}

                   	if ( status == 'for_recommendation' ) {
                   		approve_btn = '--';
                   		btn3 = '';
                   	}
                   	if ( status == 'for_approval' ) {
                   		if ( data[i].recommend == 2 ) {
 							recommend_btn = 'Recommended';
	 					}
	 					if ( data[i].recommend == 3 ) {
 							recommend_btn = '--';
	 					}
	 					btn3 = '';
                   	}

                   	if ( data[i].user_type != 'admin' ) {
                   		// if not admin do not display delete button
                   		btn2 = '';
                   	}

                	list += '<div class="trow">' +
						        '<div>'+ data[i].travel_dates +'</div>' +
						        '<div>'+ data[i].employees +'</div>' +
						        '<div>'+ data[i].purposes +'</div>' +
						        '<div>'+ data[i].destinations +'</div>' +
						        '<div>'+ recommend_btn +'</div>' +
						        '<div>'+ approve_btn +'</div>' +
						        '<div>'+ btn1 + btn2 + btn3 +'</div>' +
					    	'</div>';
                }
                if (list == '') list = "No travel orders("+ status.replace('_',' ') +") to show.";
                $('.to-view .tbody').html(list)
            }
        });
	}

	function getProjectCodes(project_code) {
		$.ajax({
         type: "POST",
         url: "http://192.168.11.65/travelorder/index.php/travel_orders/get_project_codes" , 
         data: {
         	project_code: project_code
         },
         success: function(data){
         		console.log(data)
            }
        });
	}

	function getEmployeesInTravelOrder(travel_id) {
		$.ajax({
         type: "POST",
         url: "http://192.168.11.65/travelorder/index.php/travel_orders/get_employees_in_travel_order" , 
         data: {
         	travel_id: travel_id
         },
         success: function(data){
         		data = JSON.parse(data);
         		var employees = '<div>'+
	                               	'<input type="checkbox" name="emp_all"/>'+
	                                '<span style="margin-left: 10px;">All</span>'+
	                            '</div>';
         		for (i in data){
         			employees += '<div>'+
	                               	'<input type="checkbox" name="employee" value="'+ data[i].employee_id +'"/>'+
	                                '<span style="margin-left: 10px;">'+ data[i].emp_fullname +'</span>'+
	                            '</div>';
         		}
         		$('#select-employee .modal-body').html(employees);
         		$('#select-employee #btn-print').attr('onclick', 'printTravelOrder('+ travel_id +')');
            }
        });
	}

	$('body').on('click', '.btn-delete', function(){
		var decision = confirm('Are you sure you want to delete this?');
		if ( !decision ) return false;

		var travel_id;
		var self = this;
		if ($(this).hasClass('page')) {
			// redirect to delete page
			travel_id = $('input[name="travel_id"]').val();
		} else {
			// delete row
			travel_id = $(this).data("travel_id");
		}

		$.ajax({
	        type: "POST",
	        url: "http://192.168.11.65/travelorder/index.php/travel_orders/delete_travel" , 
	        data: {
	         	travel_id: travel_id
	        },
	        success: function(data){
	        	if ($(self).hasClass('page')) {
					// redirect to delete page
					window.location.reload();
				} else {
					// delete row
					$(self).parent().parent().remove();
				}
	        }
	    });
		
	});

	$('body').on('click', '.btn-cancel', function(){
		var decision = confirm('Are you sure you want to cancel this?');
		if ( !decision ) return false;

		var travel_id;
		var self = this;
		if ($(this).hasClass('page')) {
			// redirect to delete page
			travel_id = $('input[name="travel_id"]').val();
		} else {
			// delete row
			travel_id = $(this).data("travel_id");
		}

		$.ajax({
	        type: "POST",
	        url: "http://192.168.11.65/travelorder/index.php/travel_orders/cancel_travel_order" , 
	        data: {
	         	travel_id: travel_id
	        },
	        success: function(data){
	        	if ($(self).hasClass('page')) {
					// redirect to delete page
					window.location.reload();
				} else {
					// delete row
					$(self).parent().parent().remove();
				}
	        }
	    });
		
	});

	$('body').on('click', '#select-employee input[name="emp_all"]', function(){
		if ( $(this).is(':checked') )	
			$('#select-employee input[type="checkbox"]').prop('checked','true');
		else 
			$('#select-employee input[type="checkbox"]').prop('checked','');
	});

	function recommendTravelOrder(travel_id){
		var decision = confirm('Are you sure you want to recommend this?');
		if (!decision) return false;

		var self = event.target;
		$.ajax({
         type: "POST",
         url: "http://192.168.11.65/travelorder/index.php/travel_orders/recommend_travel_order" , 
         data: {
         	travel_id: travel_id
         },
         success: function(data){
         		if ( $(self).hasClass('in-page') ) {
         			window.location.reload();
         		} else {
         			$('button[onclick="recommendTravelOrder('+ travel_id +')"]').parent().parent().remove();
         		}
            }
        });
	}

	function approveTravelOrder(travel_id){
		var decision = confirm('Are you sure you want to approve this?');
		if (!decision) return false;

		var self = event.target;
		$.ajax({
         type: "POST",
         url: "http://192.168.11.65/travelorder/index.php/travel_orders/approve_travel_order" , 
         data: {
         	travel_id: travel_id
         },
         success: function(data){
         		if ( $(self).hasClass('in-page') ) {
         			window.location.reload();
         		} else {
         			$('button[onclick="approveTravelOrder('+ travel_id +')"]').parent().parent().remove();
         		}
            }
        });
	}

	function declineRecommendTravelOrder(travel_id){
		var self = event.target;
		$.ajax({
         type: "POST",
         url: "http://192.168.11.65/travelorder/index.php/travel_orders/decline_recommend_travel_order" , 
         data: {
         	travel_id: travel_id,
         	remarks: $('#decline_message').val()
         },
         success: function(data){
         		$('#decline-travel').modal('hide');
         		if ( $(self).hasClass('in-page') ) {
         			window.location.reload();
         		} else {
         			$('button[onclick="openDeclineModal('+ travel_id +',\'recommend\')"]').parent().parent().remove();
         		}
            }
        });
	}

	function declineApproveTravelOrder(travel_id){
		var self = event.target;
		$.ajax({
         type: "POST",
         url: "http://192.168.11.65/travelorder/index.php/travel_orders/decline_approve_travel_order" , 
         data: {
         	travel_id: travel_id,
         	remarks: $('#decline_message').val()
         },
         success: function(data){
         		$('#decline-travel').modal('hide');
         		if ( $(self).hasClass('in-page') ) {
         			window.location.reload();
         		} else {
         			$('button[onclick="openDeclineModal('+ travel_id +',\'approve\')"]').parent().parent().remove();
         		}
            }
        });	
	}

	function openDeclineModal(travel_id, for_){
		var modal = $('#decline-travel');
		modal.modal('show');
		modal.attr('data-travel_id', travel_id);
		modal.attr('data-for', for_);
	}

	function submitDeclineMessage() {
		var travel_id = $('#decline-travel').attr('data-travel_id');
		var for_ = $('#decline-travel').attr('data-for');

		if ( empty('#decline_message') ) {
			alert('Please enter reason why you want to decline this then try again.');
			return false;
		}

		switch(for_) {
			case 'recommend':
				declineRecommendTravelOrder(travel_id);
			break;
			case 'approve':
				declineApproveTravelOrder(travel_id);
			break;
		}
	}


	$('input[name="mode_of_transportation"][type="radio"]').on('click', onclickMOT);
	$('input[name="time_of_departure"][type="checkbox"]').on('click', onclickTOD);

	function onclickMOT(){
		if ( $('#mot_others').is(':checked') ) 
			$('input[name="mode_of_transportation"][type="text"]').removeAttr('disabled');
		else 
			$('input[name="mode_of_transportation"][type="text"]').attr('disabled', 'TRUE');	
	}

	function onclickTOD(){
		if ( $('input[name="time_of_departure"][type="checkbox"]').is(':checked') ) {
			$('.tod').attr('disabled', 'TRUE');	
			$('input[name="time_of_departure"][type="hidden"]').val('TBA');	
		}
		else {
			$('.tod').removeAttr('disabled');
			set_TOD();
		}

	}

	$('.tod').on('change', set_TOD);

	function set_TOD(){
		$('input[name="time_of_departure"][type="hidden"]').val( $('#hour').val() + ':' + $('#minute').val() + ' ' + $('#meridiem').val() );
	}

	$(document).ready(function(){
		// to set correct value on time of departure field
		onclickTOD();
	});

	function edit(){
		$('.form').find('select, textarea, input, button').removeAttr('disabled');
		onclickMOT();
		onclickTOD();
		showButtons(2);
		hideButtons(1);
	}

	function cancel(){
		window.location.reload();
	}

	function showButtons(n){
		$('.btn-group'+n).show();
	}

	function hideButtons(n){
		$('.btn-group'+n).hide();
	}

	function printTravelOrder(travel_id){
		var employees = [];
		if ( !$('input[name="emp_all"]').prop('checked') ) {
			employees = $.map($('input[name="employee"]:checked'), function( emp ){
				return $(emp).val();
			});
		}
		window.location.href = 'http://192.168.11.65/travelorder/index.php/travel_orders/download/'+ travel_id +'/'+ encodeURIComponent(employees.join('&'));
	}

	$('#nav-travel').on('click', 'li', function(){
		$(this).siblings().removeClass('active');
		$(this).addClass('active');
	});

	$('#add-destination').on('click', function(){
		var destination = $('<div>');
		var isEmpty = true;
		var isTheSame = false;

		if ( !empty('select[name="office"]') ) {
			if ( $('select[name="office"]').val() != 'new' ) {
				destination.attr('data-officeCode', $('select[name="office"]').val() );
				destination.text( destination.text() + $('select[name="office"] option:selected').text() + ', ');
				isEmpty = false;
			}
			else {
				if ( empty('input[name="new_office"]') ) {
					alert('Enter Office Name');
					return false;
				}
				if ( empty('select[name="region"]') || empty('select[name="province"]') ) {
					alert('Select atleast Region and Province');
					return false;
				}
				destination.attr('data-newOfficeCode', $('input[name="new_office"]').val() );
				destination.text( destination.text() + $('input[name="new_office"]').val() + ', ');	
				isEmpty = false;
			}	
		}
		if ( !empty('select[name="brgy"]') ) {
			destination.attr('data-brgyCode', $('select[name="brgy"]').val() );
			destination.text( destination.text() + $('select[name="brgy"] option:selected').text() + ', ');
			isEmpty = false;
		}
		if ( !empty('select[name="city"]') ) {
			destination.attr('data-citymunCode', $('select[name="city"]').val() );
			destination.text( destination.text() + $('select[name="city"] option:selected').text() + ', ');
			isEmpty = false;
		}
		if ( !empty('select[name="province"]') ) {
			destination.attr('data-provCode', $('select[name="province"]').val() );
			destination.text( destination.text() + $('select[name="province"] option:selected').text() + ', ');
			isEmpty = false;
		}
		if ( !empty('select[name="region"]') ) {
			destination.attr('data-regCode', $('select[name="region"]').val() );
			destination.text( destination.text() + $('select[name="region"] option:selected').text() );	
			//isEmpty = false;
		}

		$('#destinations > div').each(function(){
			if ($(this).text().trim() == destination.text()) {
				isTheSame = true;
				return;
			}
		});

		if ( isEmpty ) {
			alert('Select atleast Region and Province');
			return false;
		}

		if ( isTheSame ) {
			alert('Destination already added.');
			return false;
		}

		$('#destinations').append(destination);
		selDestination.reset();
	});

	function DestinationSelect(){

		var selectRegion = $('select[name="region"]');
		var selectProvince = $('select[name="province"]');
		var selectCity = $('select[name="city"]');
		var selectBrgy = $('select[name="brgy"]');
		var selectOffice = $('select[name="office"]');
		var divDestination = $('#destinations');
		var btnDelete = $('#delete-destination');
		var btnEdit = $('#edit-destination');
		var btnNew = $('#new-destination');

		selectRegion.on('change', loadProvince);
		selectRegion.on('change', loadCity);
		selectRegion.on('change', loadBrgys);
		selectRegion.on('change', loadOffice);

		selectProvince.on('change', loadCity);
		selectProvince.on('change', loadBrgys);
		selectProvince.on('change', loadOffice);

		selectCity.on('change', loadBrgys);
		selectCity.on('change', loadOffice);

		selectBrgy.on('change', loadOffice);

		//if office changed, reselect region to brgy if have
		selectOffice.on('change', autoSelectDestination);
		selectOffice.on('change', toggleNewOffice);

		//auto select on click
		divDestination.on('click', '> div', autoSelectDestination_onClick);

		btnEdit.on('click', editDestination);
		btnDelete.on('click', deleteDestination);
		btnNew.on('click', newDestination);

		// loads provinces that has similar code
		function loadProvince(event){
			selectProvince.html('<option value="">Select Provinces...</option>');
			for (i in provinces) {
				if ( provinces[i].provCode.indexOf(event.target.value) == 0 ) {
					selectProvince.append($('<option>', {
					    value: provinces[i].provCode,
					    text: provinces[i].provDesc
					}));
				}
			}
		}

		// loads cities that has similar code
		function loadCity(event){
			selectCity.html('<option value="">Select City/Municipality...</option>');
			for (i in cities) {
				if ( cities[i].citymunCode.indexOf(event.target.value) == 0 ) {
					selectCity.append($('<option>', {
					    value: cities[i].citymunCode,
					    text: cities[i].citymunDesc
					}));
				}
			}
		}

		// loads barangays that has similar code
		function loadBrgys(event){
			selectBrgy.html('<option value="">Select Barangay...</option>');
			for (i in brgys) {
				if ( brgys[i].brgyCode.indexOf(event.target.value) == 0 ) {
					selectBrgy.append($('<option>', {
					    value: brgys[i].brgyCode,
					    text: brgys[i].brgyDesc
					}));
				}
			}
		}

		// loads offices that has similar code
		function loadOffice(event){
			if (event.target.value == '') return;

			selectOffice.html('<option value="">Select Office...</option>'+
										'<option value="new">Add New Office</option>');
			for (i in offices) {
				if ( offices[i].regCode.indexOf(event.target.value) == 0 || 
					offices[i].provCode.indexOf(event.target.value) == 0 ||  
					offices[i].citymunCode.indexOf(event.target.value)== 0 || 
					offices[i].brgyCode.indexOf(event.target.value) == 0 ) {
					selectOffice.append($('<option>', {
					    value: offices[i].officeCode,
					    text: offices[i].officeDesc
					}));
				}
			}

			toggleNewOffice();
		}

		function autoSelectDestination(event){
			for (i in offices) {
				if ( offices[i].officeCode == event.target.value ) {
					var office = offices[i];
					selectRegion.find('option[value="'+ office.regCode +'"]').prop('selected', true).trigger('change');
					selectProvince.find('option[value="'+ office.provCode +'"]').prop('selected', true).trigger('change');
					selectCity.find('option[value="'+ office.citymunCode +'"]').prop('selected', true).trigger('change');
					selectBrgy.find('option[value="'+ office.brgyCode +'"]').prop('selected', true).trigger('change');
					selectOffice.find('option[value="'+ office.officeCode +'"]').prop('selected', true);
				}
			}
		}

		function autoSelectDestination_onClick(event){
			// if selects are disabled return false;
			if (selectRegion.is(':disabled')) return false;

			var div = $(event.target);

			selectRegion.find('option[value="'+ div.data('regcode') +'"]').prop('selected', true).trigger('change');
			selectProvince.find('option[value="'+ div.data('provcode') +'"]').prop('selected', true).trigger('change');
			selectCity.find('option[value="'+ div.data('citymuncode') +'"]').prop('selected', true).trigger('change');
			selectBrgy.find('option[value="'+ div.data('brgycode') +'"]').prop('selected', true).trigger('change');
			selectOffice.find('option[value="'+ div.data('officecode') +'"]').prop('selected', true);

			if ( div.data('newOfficeCode') != undefined ) {
				selectOffice.find('option[value="new"]').prop('selected', true).trigger('change');
				$('[name="new_office"]').val(div.data('newOfficeCode'));
			}

			$('.to-details .group-1').hide();
			$('.to-details .group-2').show();
			div.addClass('active');
		}

		function toggleNewOffice(event){
			var elemNewOffice = $('[name="new_office"]');
			var contNewOffice = elemNewOffice.parent();
			if ( selectOffice.val() == 'new' ) {
				contNewOffice.show();
			}
			else {
				contNewOffice.hide();
			}
		}

		function resetDestination(){
			selectRegion.find('option[value=""]').prop('selected', true);
			selectProvince.find('option[value=""]').prop('selected', true);
			selectCity.find('option[value=""]').prop('selected', true);
			selectBrgy.find('option[value=""]').prop('selected', true);
			selectOffice.find('option[value=""]').prop('selected', true);
			$('[name="new_office"]').val();
			$('[name="new_office"]').parent().hide();
		}

		function editDestination(){
			var ans = confirm('Are you sure you want to update this destination?');
			if (!ans) return false;

			$('#destinations > div.active').remove();
			$('#add-destination').trigger('click');
			$('.to-details .group-2').hide();
			$('.to-details .group-1').show();
		}

		function deleteDestination(){
			var ans = confirm('Are you sure you want to remove this destination?');
			if (!ans) return false;

			$('#destinations > div.active').remove();
			resetDestination();
			$('.to-details .group-2').hide();
			$('.to-details .group-1').show();
		}

		function newDestination(){
			var ans = confirm('Are you sure you want to discard current selected destination?');
			if (!ans) return false;

			resetDestination();
			$('#destinations > div.active').removeClass('active');
			$('.to-details .group-2').hide();
			$('.to-details .group-1').show();
		}

		return {
			reset: resetDestination
		};

	}

	var selDestination = DestinationSelect();


	$('body').on('change', '.employee-list', function(){
		// get all selected employee
		// return array
		var selected = $.map($('.employee-list'), function(el, i){
			if ( !empty(el) ) {
				return $(el).val();
			}
		});

		$('.employee-list option').show();
		for (i in selected) {
			$('.employee-list').find('option[value="'+ selected[i] +'"]').hide();
		}

	});