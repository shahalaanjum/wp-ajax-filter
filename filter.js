	//filter code for blogs
    var pageNumber = 1;

    jQuery('.filter-link').on('click', function(e) {
        e.preventDefault();
        var pageNumber = 1;
        jQuery(this).parent().parent().find('a').removeClass('activeFilter');
        jQuery(this).addClass('activeFilter');
        jQuery('#more_blog_posts').removeClass('activeLoadmore');
        //editFilterInputs(jQuery('#filters-' + jQuery(this).data('type')), jQuery(this).data('id'));
        filterBlogs();
    });

    function editFilterInputs(inputField, value) {
        const currentFilters = inputField.val().split(',');
        const newFilter = value.toString();

        if (currentFilters.includes(newFilter)) {
            const i = currentFilters.indexOf(newFilter);
            currentFilters.splice(i, 1);
            inputField.val(currentFilters);
        } else {
            inputField.val(inputField.val() + ',' + newFilter);
        }
    }


    function filterBlogs() {
        
        if(jQuery('#more_blog_posts').hasClass('activeLoadmore')){
            pageNumber++;
        }
		else {
            pageNumber = 1;
        }
        var catIds = jQuery('.category-list li a.activeFilter').attr('data-id');
        //const taxType = Array(jQuery('li a.activeFilter').attr('data-type'));
        
        var tagIds = jQuery('.post_tag-list li a.activeFilter').attr('data-id');
        // var sortOrder = jQuery('.sort-list li a.activeFilter').attr('data-id');
        // const catIds = jQuery('#filters-category').val().split(',');
        //const tagIds = jQuery('#filters-tag').val().split(',');
        // const sortOrder = jQuery('#filters-order').val().split(',');
        
        jQuery.ajax({
            type: 'POST',
            url: myAjax.ajaxurl,
            dataType: 'json',
            data: {
                action: 'filter_blogs',
                catIds,
                tagIds,
               // taxType,
                // sortOrder,
                //pageNumber,
            },
            success: function(result) {
               
            // if(jQuery('#more_blog_posts').hasClass('activeLoadmore')){
            //     jQuery(".project-tiles").append(res.html);
            // } else{
                //console.log(result);
                jQuery('.projects-grid').html(result.html);

            //}
            if(result.total == pageNumber){
                jQuery("#more_blog_posts").hide();
            } else{
                jQuery("#more_blog_posts").show();
            }
            //jQuery('#result-count').html(res.total);
            },
            error: function(err) {
                console.log(err);
            }
        })
    }


    //load more 
    jQuery("#more_blog_posts").on("click",function(){ // When btn is pressed.
    $("#more_blog_posts").attr("disabled",true); // Disable the button, temp.
    jQuery(this).addClass('activeLoadmore');
    filterBlogs();
    jQuery(this).insertAfter('.project-tiles-portfolio');
    });

    



    
 

