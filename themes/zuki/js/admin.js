$(document).ready(function(){
    // bind click action to admin menu
    $('li.adminRecordMenu').click(function(){
        toggleMenu('adminRecordMenu');
        return false;
    });
});
