// assets/js/recipe.js
require('../css/app.scss');
require('../css/recipe.css');

var $ = require('jquery');

$(document).ready(function(){
	$('.method').focus();
});

$('.recipe_subtitle div').on('click', function(){

	$('#'+$(this).attr('class')).show().siblings().hide();

});


getUrlData = function(url) {

	//path to function in recipe controller
	var path = $('#get_data_url').val();
	$.ajax({
	        type:'POST',
	        url: path,
	        data: { url : url },
	        success: function(response) {
	       $('#get_data_results').html(response);

	    }});
	    return false;
};


searchRecipes= function(searchTerm) {
//path to function in recipe controller
var path = $('#search_url').val();
console.log(path);
$.ajax({
        type:'POST',
        url: path,
        data: { searchTerm : searchTerm },
        success: function(response) {

       $('#search_results').html(response);

    }});
    return false;
};

