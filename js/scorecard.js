$(function() {
    // We will override this
    $('#submit').hide();
    
    $("#settings :input").change(submit);
    
    $('#loading').hide();
    $('#loading').html('<img src="img/spinner.gif" alt="loading indicator" />');
    
    $('a.def_link').hover(
        get_def_by_id//,
        //function() {console.log("Leaving");}
    );
        
   $('#popup').hide();
        
});

function get_def_by_id(event) {
    href = $(event.currentTarget).attr("href");
    def_id = href.substr(href.lastIndexOf('#') + 1);
    
    $.get('glossary_array.php', {id: def_id}, function(definition){
       $('#popup').html(definition).dialog({
			autoOpen: false,
			// title: 'Dialog Title'
       });
        
       $('#popup').dialog('open');
       //return false;
       //console.log(definition)
    });
}

function submit() {
    $('#loading').show();
    $.post(
        "scorecard_results.php",
        $('#settings').serialize() + "&ajax=ajax",
        function(data) {
            $('#results').html(data);
            $('#loading').hide();
            $('a.def_link').hover(
                get_def_by_id,
                function() {console.log("Leaving");}
            );
    });
}

