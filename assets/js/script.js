jQuery(document).ready(function($) {	

	setTimeout(function() {
	    $('.fade').fadeOut('slow');
	}, 3000); // <-- time in milliseconds

		/* Set Conntent width */
		sizeContent();
		$(window).resize(sizeContent);

		function sizeContent() {
			var windowSize = $('#wpbody-content').width();
			var main = windowSize - 380;
			$('.metabox-main').width(main+'px');
			if(tomAdsEnabled != '1') {
				$('.metabox-main.metabox-options').width('');
			}
		}
	/* Handle Tab Active */
	if ( $('.nav-tab-wrapper').length > 0 ) {
		tom_tabs();
	}

	function tom_tabs() {

		var group = $('.group'),
			navtabs = $('.nav-tab-wrapper a'),
			active_tab = '';

		/* Hide all group on start */
		group.hide();

		/* Find if a selected tab is saved in localStorage */
		if ( typeof(localStorage) != 'undefined' ) {
			active_tab = localStorage.getItem('active_tab');
		}

		/* If active tab is saved and exists, load it's .group */
		if ( active_tab != '' && $(active_tab).length ) {
			$(active_tab).fadeIn();
			$(active_tab + '-tab').addClass('nav-tab-active');
		} else {
			$('.group:first').fadeIn();
			$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
			active_tab = $('.nav-tab-wrapper a:first');
		}

		/* Bind tabs clicks */
		navtabs.click(function(e) {

			e.preventDefault();

			/* Remove active class from all tabs */
			navtabs.removeClass('nav-tab-active');

			$(this).addClass('nav-tab-active').blur();

			if (typeof(localStorage) != 'undefined' ) {
				localStorage.setItem('active_tab', $(this).attr('href') );
			}

			var selected = $(this).attr('href');

			group.hide();
			$(selected).fadeIn();
			checkEmpty(selected);
		});
	}

	checkEmpty($('.nav-tab-active').attr('href'));
	
	function checkEmpty(active_tab) {
		var activeTab = active_tab || '';
		var emptyOptions = '<div class="empty-options">';
			emptyOptions +=		'<h1>There is no option here..</h1>';
			if (tomMode == 'full') {
				emptyOptions +=		'<h4>please create the option <a href="'+tomCreatePage+'#tom-id-new-data">first</a></h4>';
			}
			emptyOptions +=	'</div>';

		if ($(activeTab+" .container-body").find('.tom-item').length == '') {
			$('.hide-if-empty').hide();
			$('#tom-delete-group').show();
			$(activeTab+" .container-body").html(emptyOptions);
		} else {
			if(activeTab == '#new-group') {
				$('#tom-delete-group').hide();
				$('#tom-submit-create').val('Create');
			} else {
				$('#tom-delete-group').show();
				$('#tom-submit-create').val('Save');
			}
			$('.hide-if-empty').show();
		}
	}

	/* Initialize color picker on document ready */
    $('.tom-color').wpColorPicker();	

	// Image Options
	$('.tom-radio-img-img').click(function(){
		$(this).parent().parent().find('.tom-radio-img-img').removeClass('tom-radio-img-selected');
		$(this).addClass('tom-radio-img-selected');
	});

	$('.tom-radio-img-label').hide();
	$('.tom-radio-img-img').show();
	$('.tom-radio-img-radio').hide();


	/* Prevent drag on action button */ 
	$(".dd").delegate("a", "mousedown", function(event) { // mousedown prevent nestable click
	    event.preventDefault();
	    return false;
	});

	$(".dd").delegate( "a.delete-nestable", "click", function(event) { 
	    event.preventDefault();
	    if (confirm("Are you sure to delete option ?")) {
		  // $(this).closest( "li" ).fadeOut(500, function() { 
		  	$(this).closest( "li.dd-item" ).fadeOut('slow').remove(); 
		  	ajaxSubmit('f_create-options','tom_options','save-group');
		  // });
		  // alert('oke');
			var activeTab = $('.nav-tab-active'),
		  	    activeDiv = activeTab.attr('href');
			checkEmpty(activeDiv);
		}
	    return false;
	});

	$(".dd").delegate( "a.save-nestable", "click", function(event) { 
	    event.preventDefault();
	    var id = $(this).closest( "li" ).attr("data-id");
	   	ajaxSubmit('f_create-options','tom_options',id);

	    return false;
	});

	$(".dd").delegate( "a.edit-nestable", "click", function(event) { 
	    event.preventDefault();
	    var id = $(this).closest( "li" ).attr("data-id");
	   	$('.nestable-input#'+id).slideToggle('fast');

	    return false;
	});
	
	/************************************************
	* Trigger ajax submit if nestable list reordered
	************************************************/
	var idList = getIdList();

	$('.dd').on('change', function (event) {
	    var newIDList = getIdList();

	    if (newIDList != idList) {
	        // alert("Order has been changed");
	        ajaxSubmit('f_create-options','tom_options','save-group');
	        idList = newIDList;
	    }
	});

	function getIdList() {
	    var idList = '';
	    $('.dd-item').each(function () {
	        idList += $(this).data('id');
	    });

	    return idList;
	}

	/* Delete group */
	$(document).delegate( "#tom-delete-group", "click", function(event) {
		event.preventDefault();
		if (confirm("Are you sure to delete options group ?")) {			
			var activeTab = $('.nav-tab-active');
			var activeDiv = activeTab.attr('href');
			var prev = activeTab.prev();
			// alert(activeDiv);
			activeTab.fadeOut().remove();
			$(activeDiv).fadeOut().remove();
			ajaxSubmit('f_create-options','tom_options','delete-group');

			prev.addClass('nav-tab-active');
			tom_tabs();
			var activeTab = $('.nav-tab-active'),
		  	    activeDiv = activeTab.attr('href');
			checkEmpty(activeDiv);
		 }

	    return false;
	});


	/* Trigger cek display options if select type change */
	$(document).delegate( ".tom-type", "change", function(event) { 
		event.preventDefault();
		displayOptions(this); // Display default form 
	  	showDefault(this);
	});

	/* function to display or hide repeatable options, and default field */
	function displayOptions(element){

		var containerId = $(element).attr('data-container');
		var arrayName = 'tom_options['+containerId+']';

		var templateRepeatable 	= '<div data-order="1" class="input-options-group">';
		templateRepeatable 		+= 	'<i class="dashicons dashicons-yes"></i>';
		templateRepeatable 		+= 	'<input class="input-opt input-key" name="'+arrayName+'[options][opt-key][]" data-key="key" value="" placeholder="Key">';
		templateRepeatable 		+= 	'<input class="input-opt input-val" name="'+arrayName+'[options][opt-val][]" data-key="value" value="" placeholder="Value">';
		templateRepeatable 		+= 	'<a class="btn-remove dashicons dashicons-dismiss"></a>';
		templateRepeatable 		+= '</div>';

		
		var showOptions = false;
		var val = $(element).val();
		/* switch value of type to display option and default for */
	  	switch (val){
		  	case "select":
		  		showOptions = true;
		  		break;

		  	case "radio":
		  		showOptions = true;
		  		break;

		  	case "multicheck":
		  		showOptions = true;
		  		var templateRepeatable 	= '<div data-order="1" class="input-options-group">';
				templateRepeatable 		+= 	'<i class="dashicons dashicons-yes"></i>';
				templateRepeatable 		+= 	'<input class="input-opt input-key" name="'+arrayName+'[options][opt-key][]" data-key="key" value="" placeholder="Key">';
				templateRepeatable 		+= 	'<input class="input-opt input-val" name="'+arrayName+'[options][opt-val][]" data-key="value" value="" placeholder="Name">';
				templateRepeatable 		+= 	'<a class="btn-remove dashicons dashicons-dismiss"></a>';
				templateRepeatable 		+= '</div>';
		  		break;

		  	case "select-image":
		  		showOptions = true;
		  		var templateRepeatable 	= '<div data-order="1" class="input-options-group">';
				templateRepeatable		+= 	'<div class="tom_media_upload repeatable_upload">';
				templateRepeatable		+= 	'	<div class="tom_media_button">';
				templateRepeatable		+= 	'		<img class="tom_media_image_thumb tom-default-image" src="" style="display:none; width: 30px;"/>';
				templateRepeatable 		+= 	'		<input class="input-opt input-key" name="'+arrayName+'[options][opt-key][]" data-key="key" value="" placeholder="Key">';
				templateRepeatable		+= 	'		<input class="input-opt input-val tom_media_url" type="hidden" name="'+arrayName+'[options][opt-val][]" data-key="val" value="">';
				templateRepeatable		+= 	'		<a href="#" class="tom_button_upload button-secondary">Choose</a>';
				templateRepeatable 		+= 	'		<a class="btn-remove dashicons dashicons-dismiss"></a>';
				templateRepeatable		+= 	'	</div>';
				templateRepeatable		+= 	'</div>';
				templateRepeatable 		+= '</div>';
		  		break;

		  	default:
		  		showOptions = false;
	  	}

	  	if (showOptions == true) {
	  		$('#add-opt-'+containerId).html(templateRepeatable);
	  		$('#'+containerId+'-options').fadeIn(500);
	  	} else {
	  		/* if false hide it */
	  		$('#'+containerId+'-options').fadeOut(500);
	  	}
	  	
	}

	$(document).delegate( ".input-val", "blur", function(event) { 
		event.preventDefault();
		var idDefaultForm = $(this).closest('.options-container').attr('data-default');
		updateDefaultOption(idDefaultForm);
	});

	function showDefault(element) {
		var containerId = $(element).attr('data-container');
		var arrayName = 'tom_options['+containerId+']';
		var type = $(element).val();

		switch (type){
		  	case "select":
		  		inputDefault = 	'<select class="input-default" name="'+arrayName+'[default]" id="tom-default-'+containerId+'">';
		  		inputDefault += '<option value="">Select default option</option>';
		  		inputDefault += '</select>';
		  		updateDefaultOption(containerId);
		  		break;

		  	case "textarea":
		  		inputDefault = '<textarea class="input-default" name="'+arrayName+'[default]" id="tom-default-'+containerId+'"></textarea>';
		  		break;

		  	case "radio":
		  		inputDefault = 	'<select class="input-default" name="'+arrayName+'[default]" id="tom-default-'+containerId+'">';
		  		inputDefault += '<option value="">Select default option</option>';
		  		inputDefault += '</select>';
		  		updateDefaultOption(containerId);
		  		break;

		  	case "checkbox":
		  		inputDefault = 	'<div id="tom-default-'+containerId+'" class="tom-checkbox-default"><input class="input-default" type="checkbox" name="'+arrayName+'[default]" value="1"><span class="status">( Not Checked )</span></div>';
		  		break;

		  	case "multicheck":
		  		inputDefault =  '<div id="tom-default-'+containerId+'" class="tom-checkbox-default">';
		  		inputDefault += '<div class="input-group-multicheck">';
				inputDefault +=		'<input class="input-default input-multicheck" type="checkbox" disabled="disabled"><span class="status">Please create field options</span><br>';
				inputDefault +=	'</div>';
				inputDefault +=	'</div>';
				updateDefaultOption(containerId);
				break;

			case "upload":
		  		inputDefault = '<div id="tom-default-'+containerId+'" class="tom_media_upload">';
				inputDefault += '	<img class="tom_media_image tom-default-image" src="" style="display:none;"/>';
				inputDefault += '	<div class="tom_media_button">';
				inputDefault += '		<input class="tom_media_url input-default" type="hidden" name="'+arrayName+'[default]" value="">';
				inputDefault += '		<a href="#" class="tom_button_upload button-secondary">Choose</a>';
				inputDefault += '		<a href="#" class="tom_remove_image button-primary" style="display:none;">Remove</a>';
				inputDefault += '	</div>';
				inputDefault += '</div>';

		  		break;

		  	case "select-image":
		  		inputDefault = 	'<select class="input-default" name="'+arrayName+'[default]" id="tom-default-'+containerId+'">';
		  		inputDefault += '<option value="">Select default option</option>';
		  		inputDefault += '</select>';
		  		updateDefaultOption(containerId);
		  		break;

		  	case "color":
		  		inputDefault = 	'<input name="'+arrayName+'[default]" id="tom-default-'+containerId+'" type="text" value="" class="tom-color input-default" />';
		  		updateDefaultOption(containerId);
		  		break;

		  	case "editor":
		  		inputDefault = '<textarea class="input-default" name="'+arrayName+'[default]" id="tom-default-'+containerId+'"></textarea>';
		  		break;

		  	case "typography":
		  		/* Clone to get typography options */
		  		var elem = $('#typography-options').clone().attr('id', 'tom-default-'+containerId).show();
		  			/* Change input name to match the option id */
		  			elem.find('.array-default').each(function(i, elemArray) {
		  				var dataName = $(this).attr('data-name');
					    $(elemArray).attr('name', arrayName+'[default]['+dataName+']');
					    $(elemArray).filter('.color-picker').attr( 'class', 'array-default tom-color' ).attr('name', arrayName+'[default]['+dataName+']');
					});
		  		inputDefault = elem;
		  		break;

		  	default:
		  		inputDefault = '<input class="input-default" name="'+arrayName+'[default]" id="tom-default-'+containerId+'" type="text" value="">';
	  	}

	  	$('#'+containerId+'-default').html(inputDefault);
	  	$('.tom-color').wpColorPicker();
	}

	/* Display checkbox status */
  	$(document).delegate( ".tom-checkbox-default input:checkbox", "change", function(event) { 
		event.preventDefault();
		var status = this.checked ? '( Checked )' : '( Not Checked )';
		$(this).siblings('.status').html(status);
	});

	$(document).delegate( ".input .input-required", "change", function(event) { 
		event.preventDefault();
		var status = this.checked ? '( Required )' : '( Not Required )';
		$(this).siblings('.status').html(status);
	});
	
	function updateDefaultOption(containerId) {
			/* get select type value to determine option default type */
			var type = $('.tom-type[data-container="'+containerId+'"]').val();
			var arrayName = 'tom_options['+containerId+']';
			var optionDefault="";
			var key = [];
			var val = [];
			var input = $('#add-opt-'+containerId+' :input');
			input.each(function(i,field){ 
				var dataKey = $(this).attr('data-key');
				if (dataKey == 'key'){
					key.push(field.value);
				}
				if (dataKey == 'value'){
					val.push(field.value);
				}
			});

			var arr3 = {};
				for (var i = 0; i < key.length; i++) {
				    arr3[key[i]] = val[i];
				}

			// console.log(arr3);
			switch (type){
			  	case "multicheck":
			  		$.each( arr3, function( key, name ) {
						if (input.val().length) {
					    	// optionDefault += '<option value="'+key+'">'+val+"</option>";
					    	optionDefault += '<div class="input-group-multicheck">';
							optionDefault +=	'<input class="input-multicheck" type="checkbox" name="'+arrayName+'[default]['+key+']" value="1"> '+name+' <span class="status">( Not Checked )</span><br>';
							optionDefault += '</div>';
					    } else {
					    	optionDefault += '<div class="input-group-multicheck">';
							optionDefault +=	'<input class="input-multicheck" type="checkbox" disabled="disabled"><span class="status">Please create field options</span><br>';
							optionDefault += '</div>';
					    }
				  	});	

			  		break;

			  	case "select-image":
			  		$.each( arr3, function( key, name ) {
						if (input.val().length) {
					    	optionDefault += '<option value="'+key+'">'+key+'</option>';
					    } else {
					    	optionDefault += '<option value="">Select default option</option>';
					    }
				  	});
			  		break;

			  	default:
			  		$.each( arr3, function( key, val ) {
						if (input.val().length) {
					    	optionDefault += '<option value="'+key+'">'+val+"</option>";
					    } else {
					    	optionDefault += '<option value="">Select default option</option>';
					    }
				  	});		
		  		}
		  	/* Push to default container */	
			$('#tom-default-'+containerId).html(optionDefault);
	}


	/* Clone for repeatable options */
	$(document).delegate( "a#new-repeatable", "click", function(event) { // click event
	    event.preventDefault();
	    
	    /* get parent id to append */
		var idToAppend = $( this ).closest('.options-container').find('.input-options').first().attr('id');
	    /* get element to clone */
	    var elemToClone = $( '#'+idToAppend).find('.input-options-group');
		var oldOrder = parseInt(elemToClone.last().attr('data-order'));
		var newOrder = oldOrder+1;

	    var cloneInput = $( '#'+idToAppend).find('.input-options-group').first().clone();
	    cloneInput.attr('data-order', newOrder);
	    cloneInput.find('input.input-opt').val('');
	    /* Remove image for repeatable image */
	    cloneInput.find('.tom-default-image').attr('src','');
	    cloneInput.appendTo( '#'+idToAppend );

	    return false;
	});

	/* Delete repeatable options */
	$(document).delegate( "a.btn-remove", "click", function(event) { // click event
	    event.preventDefault();
	    var repeatableInput = $(this).closest('.input-options').find('.input-options-group');
	    var idDefaultForm = $(this).closest('.options-container').attr('data-default');

	    /* if the input remaining one, disable the delete and just emptied */
	    if (repeatableInput.length <= '1' ) {
	    	$(this).closest('.input-options-group').find('.input-opt').val('');
	    	/* Clear image on repeatable */
	    	$(this).closest('.input-options-group').find('.tom_media_image_thumb').attr('src', '');
	    	updateDefaultOption(idDefaultForm);
	    	showDefault();
	    	return false;
	    }

 		$(this).closest( "div.input-options-group" ).fadeOut(500, function() { 
 			$(this).remove(); 
	    	updateDefaultOption(idDefaultForm);
 		});
	
	    return false;
	});


	/* Add / Clone option to nestable list*/
	$(document).delegate( "#tom-add-options", "click", function(event) {
		event.preventDefault();
		var id = $("#add-tom-options input[id=tom-id-new-data]").val();
        var id = id.replace(/\s+/g, '').toLowerCase();
        if (!id.length){
    		alert('Option ID is required!');
    		return;
    	}

    	/* If active tab new group return */
		if ( $('.nav-tab-active').attr('href') == '#new-group' )  {
			alert('Please create options group first.');
			return;
		}

		var arrayName = 'tom_options['+id+']';

        var name = $("#add-tom-options input[id=tom-name-new-data]").val(),
        	reqChecked = $('#add-tom-options input[id=tom-required-new-data]').attr('checked'),
        	reqStatus = (reqChecked == 'checked') ? 'Required' : 'Not Required',
        	desc = $("#add-tom-options textarea#tom-desc-new-data").val(),
        	type = $("#add-tom-options select#tom-type-new-data").val(),
        	defaultValue = $("#add-tom-options input[id=tom-default-new-data]").val(),
        	activeDiv = $('.nav-tab-active').attr('href');

		template ='<li class="dd-item tom-item" data-id="'+id+'">';
		template +='  <div class="dd-handle"><span id="'+id+'_name">'+name+'</span>';
		template +='    <span class="tom-action-buttons">';
		template +='      <a class="blue edit-nestable" href="#">';
		template +='        <i class="dashicons dashicons-edit"></i>';
		template +='      </a>';
		template +='      <a class="red delete-nestable" href="#">';
		template +='        <i class="dashicons dashicons-trash"></i>';
		template +='      </a>';
		template +='    </span>';
		template +='  </div>';
		template +='  <div class="nestable-input" id="'+id+'" style="display:none;">';
		template +='    <table class="widefat"><tbody><tr class="inline-edit-row inline-edit-row-page inline-edit-page quick-edit-row quick-edit-row-page inline-edit-page alternate inline-editor"><td colspan="5" class="colspanchange" style="padding-bottom:10px;">';
		template +='        <fieldset class="inline-edit-col-left">';
		template +='          <div class="inline-edit-col">';
		template +='            <h4>Edit Option : '+id+'</h4>';
		template +='            <label>';
		template +='              <span class="title">Name</span>';
		template +='              <span class="input-text-wrap input">';
		template +='                <input type="text" name="'+arrayName+'[name]" value="'+name+'">';
		template +='              </span>';
		template +='            </label>';
		template +='            <label>';
		template +='              <span class="title">Required</span>';
		template +='              <span class="input">';
		template +='              	<div class="required-container">';
		template +='              		<input id="'+id+'-required" class="input-required" type="checkbox" name="'+arrayName+'[required]" value="1" '+reqChecked+'>';
		template +='              		<span class="status">( '+reqStatus+' )</span>';
		template +='              	</div>';
		template +='              </span>';
		template +='            </label>';
		template +='            <label>';
		template +='              <span class="title">Description</span>';
		template +='              <span class="input-text-wrap input">';
		template +='                <textarea name="'+arrayName+'[desc]">'+desc+'</textarea>';
		template +='              </span>';
		template +='            </label>';
		template +='          </div>';
		template +='        </div>';
		template +='	    <div class="save-button">';
		template +='	    	<a href="#" class="btn button-primary save-nestable">Save</a>';
		template +='	  	  	<span id="loading-'+id+'" class="tom-loading" style="display:none;"><img src="'+adminUrl+'images/spinner.gif" alt=""></span>';
		template +='	  	</div>';
		template +='      </fieldset>';
		template +='        <fieldset class="inline-edit-col-right">';
		template +='          <div class="inline-edit-col">';
		template +='            <label>';
		template +='              <span class="title">Type</span>';
		template +='              <span id="select_'+id+'" class="input-text-wrap input">';
		/********************************************************************
		*		APPENDED BY FUNCTION 
		********************************************************************/
		template +='              </span>';
		template +='            </label>';
		template +='			<label id="'+id+'-options">';
		template +='			  <span class="title">Options</span>';
		template +='			  <span class="input-text-wrap input">';
		template +='			  <div id="opt-container-'+id+'" class="options-container" data-default="'+id+'">';
		/********************************************************************
		*		APPENDED BY FUNCTION 
		********************************************************************/
		template +='			  <p><a id="new-repeatable" href="#">Add New Field</a></p>';
		template +='			  </div>';
		template +='	          </span>';
		template +='			</label>';
		template +='            <label>';
		template +='              <span class="title">Default</span>';
		template +='              <span class="input-text-wrap input">';
		// template +='              	<input type="hidden" id="'+id+'-hidden-default" value="'+defaultValue+'">';
		template +='              	<div id="'+id+'-default">';
		/********************************************************************
		*		APPENDED BY FUNCTION 
		********************************************************************/
		template +='              	</div>';
		template +='              </span>';
		template +='            </label>';
		template +='          </div>';
		template +='        </fieldset>';
		template +='      </tbody>';
		template +='    </table>';
		template +='  </div>';
		template +='</li>';


		$(activeDiv).find('ol.dd-list').append(template);
		displayOptions('#tom-type-'+id);
		showDefault('#tom-type-'+id);
		cloneNewData(id);
		/* Clear input */
		$('#add-tom-options').find('option:first').attr('selected', 'selected');
		$('#add-tom-options').find('input:checkbox').removeAttr('checked');
		$('#new-data-options').hide();
		$('#add-opt-new-data').html('');
		$('#add-tom-options').find('input, textarea').val(''); 
		$('#new-data-default').html('<input name="default" type="text" id="tom-default-new-data" value="">'); 
		$('.empty-options').remove();
		$('.hide-if-empty').show();
		ajaxSubmit('f_create-options','tom_options','new-data');
		checkEmpty();
	});


	/* function to clone option type, repeatable options to nestable */
	function cloneNewData(id) {
		var arrayName = 'tom_options['+id+']';

		/* Clone select type */
		var orgType = $('#tom-type-new-data');
		var type = orgType.clone();
		type.each(function(index, item) {
		     //set new select name and value 
		     $(item).attr( 'name', arrayName+'[type]' );
		     $(item).attr( 'id', 'tom-type-'+id );
		     $(item).attr( 'data-container', id );
		     $(item).val( orgType.eq(index).val() );

		});
		type.appendTo('#select_'+id);

		/* Clone repeatable Options*/
		var opt = $('#add-opt-new-data').clone().attr('id', 'add-opt-'+id);
			opt.each(function(index, item) {
		     	$(item).find('.input-key').attr( 'name', arrayName+'[options][opt-key][]' );
		     	$(item).find('.input-val').attr( 'name', arrayName+'[options][opt-val][]' );
			});
		opt.prependTo('#opt-container-'+id);

		/* Clone attribute display on options to hide or show */
		var display = $('#new-data-options').css('display');
		$('#'+id+'-options').css('display', display);
		// alert(display);

		/* Clone default field */		
		var orgDef = $('#tom-default-new-data');
		var def = orgDef.clone();
		def.each(function(index, item) {
			/* Use .find('*').andSelf().filter('.input-default') because some input type wrapped by div */
			$(item).find('*').andSelf().filter('.input-default').attr( 'name', arrayName+'[default]' );
			$(item).find('*').andSelf().filter('.input-default').attr( 'id', 'tom-default-'+id );
			$(item).find('*').andSelf().filter('.input-default').val( orgDef.eq(index).val() );

			/* Get color value (use for recreate input color */
			var colorValue = $(item).find('input.tom-color').val();
			/* Loop trough array default */
			$(item).find('.array-default').each(function(i, elemArray) {
			    var dataName = $(this).attr('data-name');
			    $(elemArray).attr('name', arrayName+'[default]['+dataName+']');
			    /* Pass value to new element */
			 	$(elemArray).val( orgDef.find('.array-default').eq(i).val() );
			});
		    
		    /* recreate input color to prevent error */
		    $(item).find('.color-container').html('<input class="array-default tom-color" name="'+arrayName+'[default][color]" type="text" value="'+colorValue+'" data-name="color">');

		});
		$('#'+id+'-default').html(def);
		$('.tom-color').wpColorPicker();
	}

	/* Media upload */
	$(document).delegate( ".tom_button_upload", "click", function(event) {
	    event.preventDefault();
	    var div = $(this).closest('.tom_media_upload');

	    var custom_uploader = wp.media({
	        title: 'Select Option Image',
	        button: {
	            text: 'Add To Option'
	        },
	        multiple: false  // Set this to true to allow multiple files to be selected
	    })
	    .on('select', function() {
	        var attachment = custom_uploader.state().get('selection').first().toJSON();
	        console.log(attachment);
	        $(div).find('.tom_media_image').attr('src', attachment.url);
	        $(div).find('.tom_media_image').show();

	        $(div).find('.tom_button_upload').html('Change');
	        $(div).find('.tom_media_url').val(attachment.url);
	        $(div).find('.tom_media_id').val(attachment.id);
        	$(div).find('.tom_remove_image').show();
        	
	        var idDefaultForm = $(div).closest('.options-container').attr('data-default');
			updateDefaultOption(idDefaultForm);
	    })
	    .open();
	});

	$(document).delegate( ".tom_remove_image", "click", function(event) {
		var div = $(this).closest('.tom_media_upload');

		$(div).find('.tom_media_image').attr('src', '');
        $(div).find('.tom_media_image').hide();
        $(div).find('.tom_button_upload').html('Choose');
        $(div).find('.tom_media_url').val('');
        $(div).find('.tom_media_id').val('');
        $(div).find('.tom_remove_image').hide();
	});


	/* Copy Shortcode */
	var client = new ZeroClipboard( $("a.button-copy-shortcode") );

	client.on( "ready", function( readyEvent ) {
	  // alert( "ZeroClipboard SWF is ready!" );

	  	client.on( 'copy', function(event) {
	  		var shortcode = $(event.target).find('.tooltipValue');
          	event.clipboardData.setData('text/plain', shortcode.text());
          	shortcode.hide();
        } );
	  
	  	client.on( "aftercopy", function( event ) {
	  		$(event.target).find('.tooltip-body').html('Copied to clipboard');
		    // alert("Shortcode copied to clipboard: " + event.data["text/plain"] );
	  	} );

	} );


	/* Tooltip */
	var showTooltip = function(event) {
	  	$('div.tooltip').remove();
	  	var title 	= $(this).find('.tooltipValue').attr('data-title');
	  	var shortcode 	= $(this).find('.tooltipValue').text();
	  	var	elementDiv 	=  '<div class="tooltip type'+tomAdsEnabled+'">';
	  		elementDiv 	+= '	<div class="tooltip-head">'+title+'</div>';
	  		elementDiv 	+= '	<div class="tooltip-body">'+shortcode+'</div>';
	  		elementDiv 	+= '</div>';

	  	$(elementDiv).appendTo($(this));

	  	var tooltipWidth = $(this).find('.tooltip').width();

	  	// alert(tooltipWidth);

	  	var position = $(this).position();
	  	// console.log(tooltipWidth);
	  	if (tomAdsEnabled == '1'){
	  		$('div.tooltip').css({top: position.top - 10, left: position.left + 38});
	  	} else {
	  		$('div.tooltip').css({top: position.top - 10, left: position.left - (tooltipWidth + 10)});
	  	}
	};
 
	var hideTooltip = function() {
	   $('div.tooltip').remove();
	};
 
	$("a.button-copy-shortcode").on({
	   mouseenter : showTooltip,
	   mouseleave: hideTooltip
	});

	/* Submit Form*/
	function ajaxSubmit(formId,optionId,buttonId){
		var formData = $('#'+formId).serialize();

		var data = {
			/* actions must be match with add_action name */
			'action': 'tom_options',
			'id': buttonId,
			'options': optionId,
			'form_data': formData
		};
		/* Post data*/
		$.post(ajaxurl, data, function(response) {
	       	$("#loading-"+buttonId).show();	
			/* Remove notification if exist */
			$('.settings-error').fadeOut('slow').remove();       	
			setTimeout( function() {
					$("#loading-"+buttonId).hide();
					$("#"+buttonId+"_name").html(response.data.name);
					// $('#tom-notification').html(response);
					$('#tom-notification').html(response.message).fadeIn('slow').delay(1000).fadeOut('slow');
		    },1000);
		// console.log(response.data.name);
		},"json");

	return false;
	}
});

/* Iklan */
jQuery(function(){  
	var url = tomAdsEndpoint;
	jQuery.ajax({url: url, dataType:'jsonp'}).done(function(data){  
		//promo_1
		if(typeof data =='object'){  
			jQuery("#promo_1 a").attr("href",data.permalink_promo_1);  
			jQuery("#promo_1 img").attr("src",data.img_promo_1);

			//promo_2
			jQuery("#promo_2 a").attr("href",data.permalink_promo_2);  
			jQuery("#promo_2 img").attr("src",data.img_promo_2);  
		}
	});
});