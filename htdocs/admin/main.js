/**
 * Created by mirel on 9/16/14.
 */

$(document).ready(function () {
    $('.user-messages').click(function () {
        $(this).fadeOut();
    })
})


function crontabLogPreview(id, action, link){
    $('#log-dialog').load(link.href, function(){
        $( "#log-dialog" ).dialog({
            height : 650,
            width : '90%'
        });
    });

    return false;
}