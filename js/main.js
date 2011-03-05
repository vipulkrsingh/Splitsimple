$(document).ready(function() {

$.ajax({
  type: 'POST',
  url: '../main.php',
  data: "q=1",
  success: function(data){	
   alert(data);
  },
  //dataType:'json'
});


jQuery("abbr.timeago").timeago();


var markup = "<tr><td colspan='2'>${Name}</td><td>Released: ${ReleaseYear}</td><td>Director: ${Director}</td></tr>"

/* Compile markup string as a named template */
$.template( "movieTemplate", markup );

/* Render the named template */
$( "#showBtn" ).click( function() {
  $( "#movieList" ).empty();
  $.tmpl( "movieTemplate", movies ).appendTo( "#movieList" );
});

/*
	$('#inputpanel')[0].reset();
	$('#debittoggle').live("click",function(){ 
      
	   $('.chkbox').each(function(){
		$checkbox = $(this);
		$checkbox.attr('checked', !$checkbox.attr('checked'));
		});
 });
	
  $('#submit').click(function(){
  $.ajax({
  type: 'POST',
  url: '../main.php',
  data: $('#inputpanel').serialize(),
  success: function(data){	
   window.location.reload();
  },
  dataType:'json'
});

  });

  $('.approve').live("click",function(){
  //var mytool_array=where_is_mytool.split("/");
  $.ajax({
  type: 'POST',
  url: '../approve.php',
  data: "trId=" + $(this).attr('name').split(",")[0] + "&userId=" + $(this).attr('name').split(",")[1] + "&groupId=" + $(this).attr('name').split(",")[2],
  success: function(data){
	 window.location.reload();
  },
  dataType:'json'
  });
  
  });
  
  $('.deltr').live("click",function(){
  //var mytool_array=where_is_mytool.split("/");
  
if(window.confirm("Are you sure you want to delete this?"))
{ 
  $.ajax({
  type: 'POST',
  url: '../delete.php',
  data: "trId=" + $(this).attr('id'),
  success: function(data){
	 window.location.reload();
  },
  dataType:'json'
  });
  
}

  });
  
*/  
  });