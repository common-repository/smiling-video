//  Copyright 2017 Smiling Video. All Rights Reserved.
(function($){
    var modal = {};
    $(document).ready(function(){
        modal = initModal();
        $('#smiling_video_add_media').click(function(e) {
            e.preventDefault();
            modal.wrapper.css("display","block");
            //todo clear modal content
            var pagetitle = $("input[name=post_title]#title").val();
            var pagecontent = $("textarea[name=content]#content").val();
            //console.log(pagetitle);
            //console.log(pagecontent);
            var action = $(this).data("action");
            //console.log(action);
            AjaxLoadGrid(action,modal,pagetitle,pagecontent);
        });
        $('#smiling_video_videogallery').click(function(e){
            e.preventDefault();
            modal.wrapper.css("display","block");
            var action = $(this).data("action");
            //console.log(action);
            AjaxLoadGallery(action,modal,'','');
        });
    });
    
    
    function initModal(){
        var modalhtml = '<div id="smiling_video_modal" class="modal">\n\
                            <div class="modal-content-wrapper">\n\
                            <div class="modal-content">\n\
                                <span class="smiling_video_modal_close">&times;</span>\n\
                                <div class="my_modal_content">Attendere...</div>\n\
                            </div>\n\
                            </div>\n\
                        </div>';
        if($("#smiling_video_modal").length <= 0){
            $("body").append(modalhtml);
        }
        var modal = {};
        modal.wrapper = $('#smiling_video_modal'); 
        modal.content = $("#smiling_video_modal .my_modal_content");
        var span = document.getElementsByClassName("smiling_video_modal_close")[0];
        span.onclick = function() {
            modal.wrapper.css("display","none");
            modal.wrapper.removeClass("smiling_loaded");
            modal.content.html('Attendere...'); 
        };

        //console.log("modal loaded");
        return modal;
  }
  function AjaxLoadGrid(action,modal,pagetitle,pagecontent){
        var target = modal.content;
        $.post( ajax_object.ajax_url, 
                {
                    'action': action,
                    'pagetitle': pagetitle,     
                    'pagecontent': pagecontent,     
                    'ajax_url': ajax_object.ajax_url,
                    'security': ajax_object.security,
                }, 
                function(response) {
                    //console.log(response);
                    target.html(response);
                    modal.wrapper.addClass("smiling_loaded");
                    AttachGridHandler(modal);
                }
        );
  }
    function AjaxLoadGallery(action,modal,categs,sources,langs,text){
        var target = modal.content;
        $.post( ajax_object.ajax_url, 
                {
                    'action': action,
                    'categs': categs,     
                    'source': sources,     
                    'lang': langs,     
                    'text': text,     
                    'ajax_url': ajax_object.ajax_url,
                    'security': ajax_object.security,
                }, 
                function(response) {
                    //console.log(response);
                    target.html(response);
                    modal.wrapper.addClass("smiling_loaded");
                    AttachGridHandler(modal);
                }
        );
  }
  function AttachGridHandler(modal){
        $(".single-box .smiling_video_insert_snippet").click(function(e){
            e.preventDefault();
            var id =    $(this).parent().find("#_id").text();
            var code = $(this).parent().find("textarea").val();
            var snippet = '[smiling_video id="'+id+'"]'+code+'[/smiling_video]';
            wp.media.editor.insert(snippet);
            modal.wrapper.css("display","none");
            modal.wrapper.removeClass("smiling_loaded");
            modal.content.html('Attendere...'); 
        });
        $('#smiling_video_search').click(function(e) {
            e.preventDefault();
            var categs = [$("#categories").val()];
            //console.log(categs);
            var source = $("#sources").val();
            //console.log(source);
            var lang = $("#languages").val();
            //console.log(lang);
            var text =  $("#filtertext").val();
            //console.log(text);
            
            modal.wrapper.removeClass("smiling_loaded");
            modal.content.html('Attendere...'); 
            var action = $(this).data("action");
            //console.log(action);
            AjaxLoadGallery(action,modal,categs,source,lang,text);
        }); 
        
        /* start TYPE MP4*/
            $('.video-box.video-mp4').click(function(e) {
                e.preventDefault();
                var target = $(this).find("video").get(0);
                if(target.paused){
                    $(this).find(".overlay-wrapper").hide();
                    $(this).find(".play").hide();
                    target.play();
                }else{
                    $(this).find(".overlay-wrapper").show();
                    $(this).find(".play").show();
                    target.pause();
                }
                $(".video-box").not(this).each(function(){
                    $(this).find(".overlay-wrapper").show();
                    $(this).find(".play").show();
                    $(this).find("video").get(0).pause();
                });
            }); 
        /* end TYPE MP4*/
        
        /* start TYPE HLS*/
            //videojs.removePlayers();
            videos_hls = {};
            $('.video-box.video-hls').each(function() {
                var id = $(this).find("video").attr('id');
                videos_hls[id] = videojs(id);
                //var options = { id: id, };
                videos_hls[id].muted(false);
//                videos_hls[id].on('pause', function() {
//                    videos_hls[id].pause();
//                });
            }); 
            $('.video-box.video-hls').click(function(e) {
                e.preventDefault();
                var target = $(this).find(".video-autowrapped").attr('id');
                target = videos_hls[target];
                if(target.paused()){
                    $(this).find(".overlay-wrapper").hide();
                    $(this).find(".play").hide();
                    target.play();
                }else{
                    $(this).find(".overlay-wrapper").show();
                    $(this).find(".play").show();
                    target.pause();
                }
                $(".video-box").not(this).each(function(){
                    $(this).find(".overlay-wrapper").show();
                    $(this).find(".play").show();
                    $(this).find("video").get(0).pause();
                });
            }); 
        /* end TYPE HLS*/
  }
    
    
})(jQuery);