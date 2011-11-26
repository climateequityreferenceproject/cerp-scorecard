$(function() {
    // We will override this
    $('#submit').hide();
    
    $("#settings :input").change(submit);
    
    $('#loading').hide();
    $('#loading').html('<img src="img/spinner.gif" alt="loading indicator" />');

});

function submit() {
    $('#loading').show();
    $.post(
        "scorecard_results.php",
        $('#settings').serialize() + "&ajax=ajax",
        function(data) {
            $('#results').html(data);
            $('#loading').hide();
        });
}
