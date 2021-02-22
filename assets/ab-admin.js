jQuery(document).ready(function($){
    $('.upload-default-btn').click(function(e) {
	    var thiss = $(this);
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            //console.log(uploaded_image.toJSON());
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            thiss.parent().find(".regular_def_image").val(image_url);
            thiss.parent().find("#def_def_image").attr("src", image_url);
        });
    });
});