(function($, window, document, undefined) {
  $('.submit-like').on('click', function(e) {
    e.preventDefault();
    var post_id = $(this).data('id');
    $.ajax({
      url: mangatalk_likes_params.ajax_url,
      type: 'post',
      data: {
        action: 'mangatalk_submit_like',
        post_id: post_id
      },
      success : function( response ) {
        $('#likes-count').html( response );
      }
    });
    
    console.log(e);
  });
})(jQuery, window, document);