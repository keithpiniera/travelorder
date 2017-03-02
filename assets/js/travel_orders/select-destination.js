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