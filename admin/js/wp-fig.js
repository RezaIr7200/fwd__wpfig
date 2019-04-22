(function ($) {

	$(document).ready(function(){

		var imagesSeq=[];
		var imagesArr;

		var updateImageItemsInput = function(){
		if( $('.wpfig-images-wrap').length ){
			imagesSeq=[];

			imagesArr = $('.wpfig-images-wrap .wpfig-image-wrap') ;

			imagesArr.each(function(){
				imagesSeq.push( $(this).attr('data-id') ); 
			});

			$("#wpfig_gallery").val(imagesSeq.join(","));

		}
	}


	var meta_gallery_frame;
	// Runs when the image button is clicked.
	$('#wpfig_gallery_button').click(function (e) {

		//Attachment.sizes.thumbnail.url/ Prevents the default action from occuring.
		e.preventDefault();

		// If the frame already exists, re-open it.
		if (meta_gallery_frame) {
			meta_gallery_frame.open();
			return;
		}

		// Sets up the media library frame
		meta_gallery_frame = wp.media.frames.meta_gallery_frame = wp.media({
			title: wpfig_gallery.title,
			button: { text: wpfig_gallery.button },
			library: { type: 'image' },
			multiple: true
		});

		// Create Featured Gallery state. This is essentially the Gallery state, but selection behavior is altered.
		meta_gallery_frame.states.add([
			new wp.media.controller.Library({
				id: 'wpfig-gallery',
				title: 'Select Images for Gallery',
				priority: 20,
				toolbar: 'main-gallery',
				filterable: 'uploaded',
				library: wp.media.query(meta_gallery_frame.options.library),
				multiple: meta_gallery_frame.options.multiple ? 'reset' : false,
				editable: true,
				allowLocalEdits: true,
				displaySettings: true,
				displayUserSettings: true
			}),
		]);

		meta_gallery_frame.on('open', function () {
			var selection = meta_gallery_frame.state().get('selection');
			var library = meta_gallery_frame.state('gallery-edit').get('library');
			var ids = $('#wpfig_gallery').val();
			if (ids) {
				idsArray = ids.split(',');
				idsArray.forEach(function (id) {
					attachment = wp.media.attachment(id);
					attachment.fetch();
					selection.add(attachment ? [attachment] : []);
				});
			}
		});

		meta_gallery_frame.on('ready', function () {
			$('.media-modal').addClass('no-sidebar');
		});

		// When an image is selected, run a callback.
		//meta_gallery_frame.on('update', function() {
		meta_gallery_frame.on('select', function () {
			var imageIDArray = [];
			var imageHTML = '';
			var metadataString = '';
			images = meta_gallery_frame.state().get('selection');
			imageHTML += '<ul class="wpfig-images-wrap">';
			images.each(function (attachment) {
				imageIDArray.push(attachment.attributes.id);
				imageHTML += '<li class="wpfig-image-wrap" data-id="'+attachment.attributes.id+'" draggable="true"><button class="wpfig-image__remove">✕</button><img id="' + attachment.attributes.id + '" src="' + attachment.attributes.sizes.thumbnail.url + '"></li>';
			});
			imageHTML += '</ul>';

			metadataString = imageIDArray.join(",");
			if (metadataString) {
				$("#wpfig_gallery").val(metadataString);
				$("#wpfig-wrap").html(imageHTML);
				setTimeout(function () {
					//ajaxUpdateTempMetaData();
				}, 0);
			}

			initDraggable();
		});

		// Finally, open the modal
		meta_gallery_frame.open();

	});


	$(document.body).on('click', '.wpfig-image__remove', function (event) {

		event.preventDefault();

		if (confirm('Are you sure you want to remove this image?')) {

			var removedImage = $(this).children('img').attr('id');
			var oldGallery = $("#wpfig_gallery").val();
			var newGallery = oldGallery.replace(',' + removedImage, '').replace(removedImage + ',', '').replace(removedImage, '');
			$(this).parents().eq(0).remove();
			$("#wpfig_gallery").val(newGallery);
		}

	});


	// make image items draggabe for ordering

	var dragSrcEl = null;

	function handleDragStart(e) {
	  // Target (this) element is the source node.
	  dragSrcEl = this;
	
	  e.dataTransfer.effectAllowed = 'move';
	  e.dataTransfer.setData('text/html', this.outerHTML);
	
	  this.classList.add('dragElem');
	}
	function handleDragOver(e) {
	  if (e.preventDefault) {
		e.preventDefault(); // Necessary. Allows us to drop.
	  }
	  this.classList.add('over');
	
	  e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.
	
	  return false;
	}
	
	function handleDragEnter(e) {
	  // this / e.target is the current hover target.
	}
	
	function handleDragLeave(e) {
	  this.classList.remove('over');  // this / e.target is previous target element.
	}
	
	function handleDrop(e) {
	  // this/e.target is current target element.
	
	  if (e.preventDefault) { e.preventDefault(); }

	  if (e.stopPropagation) {e.stopPropagation(); }
	
	  // Don't do anything if dropping the same column we're dragging.
	  if (dragSrcEl != this) {
		// Set the source column's HTML to the HTML of the column we dropped on.
		//alert(this.outerHTML);
		//dragSrcEl.innerHTML = this.innerHTML;
		//this.innerHTML = e.dataTransfer.getData('text/html');
		this.parentNode.removeChild(dragSrcEl);
		var dropHTML = e.dataTransfer.getData('text/html');
		this.insertAdjacentHTML('beforebegin',dropHTML);
		var dropElem = this.previousSibling;
		addDnDHandlers(dropElem);

		updateImageItemsInput();
		
	  }
	  this.classList.remove('over');
	  return false;
	}
	
	function handleDragEnd(e) {
	  // this/e.target is the source node.
	  this.classList.remove('over');
	
	  /*[].forEach.call(cols, function (col) {
		col.classList.remove('over');
	  });*/
	}
	
	function addDnDHandlers(elem) {
	  elem.addEventListener('dragstart',	handleDragStart, false);
	  elem.addEventListener('dragenter',	handleDragEnter, false)
	  elem.addEventListener('dragover',		handleDragOver,  false);
	  elem.addEventListener('dragleave',	handleDragLeave, false);
	  elem.addEventListener('drop',			handleDrop,      false);
	  elem.addEventListener('dragend',		handleDragEnd,   false);
	
	}
	
	function initDraggable(){
		var dragables = document.querySelectorAll('#wpfig-wrap li[draggable]');
		[].forEach.call(dragables, addDnDHandlers);
		
	}
	initDraggable();
	
});
})(jQuery);