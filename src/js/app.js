import '../css/global.scss';

const $ = require('jquery');

// create global $ and jQuery variables
global.$ = global.jQuery = $;

require('bootstrap');

// Custom file input
$('input[type="file"]').change(function(e){
    var fileName = e.target.files[0].name;
    $(this).next('.custom-file-label').html(fileName);
});