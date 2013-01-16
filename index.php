<!DOCTYPE html>

<html>

<head>

<Title>Packrat Redux</title>

<script src="js/jquery-1.7.1.min.js"></script>
<script src="js/jquery.isotope.min.js"></script>
<script src="js/json2.js"></script>
<script src="js/packrat.js"></script>

<script>

	var token = "";

</script>

<link rel="shortcut icon" href="resources/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Monda" type="text/css"/>
<link rel="stylesheet" href="styles/packrat.css"/>

</head>


<body>

<script>

	$(document).ready(function() {

		token = readCookie("token");

		if (token == null) {

			$("#login").removeClass("hidden");
			$("#username").focus();
			$(document).keypress(function(e) {
				if(e.which == 13) {

					$(document).unbind();
					$("#login").fadeOut("fast");
					
					$("#notice").html("<p>Logging in...</p>");
	
					var username = $("#username").val();
					var password = $("#password").val();

					$.ajax({
						url : "scripts/login.php",
						data: "username="+username+"&password="+password,
						type : "POST",
						success : function(data) {
					
							if (data == "0") {

								$("#notice").html("<p>Error logging in...</p>");
								$("#notice").fadeIn("fast", function() {
									window.setTimeout( function () {
										$("#notice").fadeOut("fast", function() {
											$("#login").fadeIn("fast");
										});
									}, 1000);
								});
								

							} else {

								startDND();		

								$("#login").unbind();
								token = readCookie(token);
								$("#searchbar").fadeIn("fast");
								$("#search").focus();
								fetchDocuments(query="",type="all")

							}
						
						}
					});

				}
			});

		} else {

			startDND();
	
			$("#searchbar").fadeIn("fast");
			$("#search").focus();
			fetchDocuments(query="",type="all");

		}

	
	// Bind to the seach input, and upon each keypress fetch documents.
	$("#search").keyup(function(e) {

		var query = $("#search").val();
		fetchDocuments(query, type="query");

	});	

	});

</script>

<div id="container">

	<img class="backgroundImg" src="resources/packrat.png"/>
	<div id="UI">

	</div>

	<div id="FullViewer">
		<div id="controls" align='center'>
			<table width="100%">
				<tr>
					<td class="navTable" align="center" width="25%"><a href="javascript:void(0);" onclick='$("#editForm").show();' >Edit</a></td>
					<td class="navTable" align="center" width="50%"><a href="javascript:void(0);" onclick='' >Add Page</a></td>
					<td align="center" width="25%"><a href="javascript:void(0);" onclick='closeDocumentViewer()'>Close</a></td>
				</tr>
			</table>
			<div id="editForm">
				<div id="editMetaContainer"></div>
				Tags<input type="text" id="editTags" value=""/><br><br>
				<a href="javascript:void(0);" onclick=''>Add Data</a>
			</div>

		</div>
		<div id="fullContainer"></div>
	</div>

	<div id="filedrag">

		<div id="fileviewer">



		</div>

		<div id="infoPrompt" align="center">

			<input type="text" id="usrTitle" placeholder="Title" style="width:15%;" /> <input type="text" id="usrCategory" placeholder="Category" style="width:15%;"/> <input type="text" id="usrMeta" placeholder="Author : Name, Address : 123 Fake Street" style="width:15%;"/> <input type="text" id="usrTags" placeholder="Tag and tag and tag" style="width:15%;"/> <input type="button" id="startUpload" value="Upload"/>

		</div>

		<div id="submitbutton">
			<button type="submit">Upload Files</button>
		</div>

	</div>

</div>

<div id="notice" align="center" class="hidden prompt"/>

</div>

<div id="searchbar" align="center" class="hidden prompt">
	<input type="text" id="search" placeholder="Search..."/>
</div>

<div id="login" align="center" class="hidden prompt">
	<input type="text" id="username" placeholder="Username"/> <input type="password" id="password" placeholder="Password" />
</div>

<form id="upload" action="scripts/upload.php" method="POST" enctype="multipart/form-data">
	<input type="hidden" id="UploadID" name="UploadID" value="" />
	<input type="hidden" id="Page" name="Page" value = "1" />
	<input type="hidden" id="Title" name="Title" value = "" />
	<input type="hidden" id="Category" name="Category" value = "" />
	<input type="hidden" id="Meta" name="Meta" value = "" />
	<input type="hidden" id="Tags" name="Tags" value = "" />
	<!-- Add in more fields here, please -->
	<fieldset>
		<input type="hidden" id="MAX_FILE_SIZE" name = "MAX_FILE_SIZE" value="300000" />
		<div>
			<label for="fileselect">Files to Upload:</label>
			<input type="file" id="fileselect" name="fileselect[]" multiple="multiple" />
		</div>
	</fieldset>
</form>

</body>

</html>
