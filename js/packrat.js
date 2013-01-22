function $id(id) {
	return document.getElementById(id);
}

function FileDragHover(e) {
	e.stopPropagation();
	e.preventDefault();
	e.target.className = (e.type == "dragover" ? "hover" : "");
	if (e.type == "dragover") {
		$("#UI").fadeOut('fast');
		$("#searchbar").fadeOut('fast');
		$("#filedrag").fadeIn('fast');
	}

}

// global files variable
var loadedImages = 0;

function FileSelectHandler(e) {

	FileDragHover(e);

	$("#fileviewer").fadeTo('fast',0);	
	
	var ID = (new Date).getTime();
	$("#UploadID").val(ID);

	files = e.target.files || e.dataTransfer.files;
	console.log(files);
	for (var i = 0, f ; f = files[i]; i++) {
		var fNum = i;
		// Get the image
		var reader = new FileReader();
		
		var itemPrefix = '<div class="item" data-size="'+f.size+'"><img src="" height="300" width="300" /><p class="ID" style="display:none;">'+i+'</p></div>';
		$("#fileviewer").append(itemPrefix);
		reader.onload = function(e) {
			
			var img = new Image();
			var newHeight = 0;	

			var loaded = false;
			img.onload = function() {	
				var width = img.width;
				var height = img.height;
				var maxWidth = Math.floor($(document).width() / files.length);
				var newHeight = ( maxWidth  * height) / width;
	/*			if (newHeight <= $('#fileviewer').height() ) {

					maxWidth = Math.floor( ($("#fileviewer").height() * maxWidth) / newHeight);
					newHeight = $("#fileviewer").height();

				}
	*/
				var $item = $(".item[data-size="+e.loaded+"] img");
				$item.attr({"src" : e.target.result,
					"height" : newHeight,
					"width" : maxWidth});
				loadedImages += 1;
				if (loadedImages == files.length ) {

					$("#fileviewer").isotope({
						itemSelector : '.item',
						layoutMode : "fitRows",
					});
					$("#fileviewer").fadeTo('fast',1);	

				}

				

			}
			img.src = e.target.result;
			loaded = false;
						
		}
		reader.readAsDataURL(f);
		//$('#UI').isotope( 'insert', $item)
	}

	$("#startUpload").click( function (){
		startUpload(files);
	});

}

function startUpload(files) {

	var totalFiles = files.length;
	var successNum = 0;
	for (var i = 0, f ; f = files[i]; i++) {

		$("#Page").val(i + 1);
		$("#Title").val($("#usrTitle").val());
		$("#Category").val($("#usrCategory").val());
		$("#Tags").val($("#usrTags").val());
		$("#Meta").val($("#usrMeta").val());

		var xhr = new XMLHttpRequest();
		if ( xhr.upload) {

			// Start the upload
			xhr.open("POST", $id("upload").action, true);
			xhr.setRequestHeader("X_FILENAME", f.name);
			xhr.setRequestHeader("Page", $("#Page").val());
			xhr.setRequestHeader("UploadID", $("#UploadID").val());
			xhr.setRequestHeader("Title", $("#Title").val());
			xhr.setRequestHeader("Category", $("#Category").val());
			xhr.setRequestHeader("Tags", $("#Tags").val());
			xhr.setRequestHeader("Meta", $("#Meta").val());
			xhr.send(f);

			xhr.onreadystatechange = function () {

				if (xhr.readyState == 4) {

					$("#filedrag").fadeOut('fast');
					$("#UI").fadeIn('fast');
					$("#searchbar").fadeIn('fast');
					fetchDocuments(query="",type="all");

				}

			}

		}
		

	}			


	// Start the physical upload
	// Change the UI to reflect the upload status.
	// Store the UI and re-fetch all documents when commpleted.

}

function startDND() {

	$('img').bind('dragstart', function(event) {event.preventDefault(); });
	$('div').bind('dragstart', function(event) {event.preventDefault(); });

	if (window.File && window.FileList && window.FileReader) {

		var fileselect= $id("fileselect");
		var filedrag = $id("container");
		var submitbutton = $id("submitbutton");
		fileselect.addEventListener("change" , FileSelectHandler, false);
		var xhr = new XMLHttpRequest();
		if (xhr.upload) {
			filedrag.addEventListener("dragover", FileDragHover, false);
			filedrag.addEventListener("dragleave", FileDragHover, false);
			filedrag.addEventListener("drop", FileSelectHandler, false);
			filedrag.style.display = "block";
			submitbutton.style.display = "none";
		}

	}

}


function createCookie(name,value,days) {
        if (days) {
                var date = new Date();
                date.setTime(date.getTime()+(days*24*60*60*1000));
                var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
}

function eraseCookie(name) {
        createCookie(name,"",-1);
}

function dynamicLayout() {

	var defaultWidth = 240;
	var displayWidth = $(document).width();
	var differentWidths = displayWidth / defaultWidth;
	var maximumRow = Math.floor(differentWidths);
	var bestWidth = displayWidth / maximumRow;

	var currentHeight = $(".item img").height();
	var currentWidth = $(".item img").width();
	var newHeight = ( currentHeight * bestWidth ) / currentWidth;

	$(".item img").width(bestWidth + "px").height(newHeight + "px");
	
	$("#UI").isotope("reLayout");

}

function fetchDocuments(query, type) {

	if (query == "") {

		type="all";

	}
		
	// Adjust the widths of the columns to better fit the display.
	var defaultWidth = 240;
	var displayWidth = $(document).width();
	var differentWidths = displayWidth / defaultWidth;
	var maximumRow = Math.floor(differentWidths);
	var bestWidth = displayWidth / maximumRow;

	$.ajax({
		url : "scripts/fetchImages.php",
		type : "POST",
		data : "token="+token+"&type="+type+"&query="+query+"&bestwidth="+bestWidth,
		success : function(JSONdata) {

			// Load isotope and all of the images

			$("#UI").isotope("destroy");

			$("#UI").html("");


			$("#UI").isotope({
				itemSelector : '.item',
				layoutMode : "fitRows",
				masonry: {
					columnWidth: bestWidth
				}
			});

			var documents = $.parseJSON(JSONdata);
			
			$.each(documents, function(key, value) {

				var imageThumb =  value.Pages[1];
				var image = "uploads/" + value.Pages[1];

				var $item = $('<div class="item" data-id="'+key+'" data-json='+"'"+JSON.stringify(value)+"'"+'" > <span class="itemLabel">'+value.Metadata.Title+'</span><img src="scripts/otfImages.php?url=' + imageThumb + '&width='+bestWidth+'" width="' + bestWidth + '" height="' + value.Height + '" /></div>');
				$('#UI').isotope( 'insert', $item);
			
				$($item).click(function() {

					$("#UI").fadeOut('fast');
					$(".backgroundImg").fadeOut("fast");
					$("#searchbar").fadeOut("fast");
					$("#FullViewer").fadeIn('fast');
					
					var itemJSON = $($item).attr("data-json");
					
					var itemAttr = $.parseJSON(itemJSON);
					$("#editTitle").attr("value",itemAttr.Metadata.Title);
					$("#editCategory").attr("value",itemAttr.Metadata.Category);
					$.each(itemAttr.Metadata, function(key,value) {
						var htmlCurrent = $("#editMetaContainer").html();
						var newHtml = htmlCurrent + "<a href='javascript:void(0);' onclick='editKey()' >" + key + "</a>" +"<input id='meta_"+ escape(key)  +"' type='text' value='"+value+"'/><br>";
						$("#editMetaContainer").html(newHtml);
					});
					$("#editMeta").attr("value",JSON.stringify(itemAttr.Metadata));
					$("#editTags").attr("value",itemAttr.Tags);
					$.each(itemAttr.Pages, function(pagekey, pagevalue) {

						$("#fullContainer").html( $("#fullContainer").html() + "<div class='fullitem'><img src='uploads/"+pagevalue+"'/></div>");
						

					});


				});	

				// Add in individual item clicks

			});

			// Other necessary bits and pieces.
			


		}
	});

}

function closeDocumentViewer() {

	$('#FullViewer').fadeOut('fast');
	
	$("#fullContainer").html("");
	$("#editForm").html("");
	

	$('.backgroundImg').fadeIn('fast');
	$('#UI').fadeIn('fast');
	$('#searchbar').fadeIn('fast');

}

