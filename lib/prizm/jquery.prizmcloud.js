/*
 * jQuery plugin to display Prizm Cloud Document Viewer
 * with clickable thumbnails that dynamically update viewer
 * with new document, with a slideshow feature
 *
 * Plugin url: www.prizmcloud.com
 * Author: Accusoft
 * Author url: www.accusoft.com
 * Documentaion: http://www.prizmcloud.com/plugins/documentation/jquery.html
 * 
 * Structure example
 * <div with "your container id">
 *     <div "container for document links">
 *        <a href="http://path/to/document" class="doc-links"></a>
 *    </div>
 *    <div "container for viewer"></div>
 * </div>
 * script jQuery("your container id").prizmcloud({ key: "11111111", viewerwidth: 650, viewerheight: 700 });
 */
(function ($) {
    $.fn.prizmcloud = function (options) {

        // extend default options.
        var opts = $.extend(true,{}, $.fn.prizmcloud.defaults, options);
        var base_url = "//connect.ajaxdocumentviewer.com/?key=";

        bindClicks(this);
        
        function bindClicks(obj) {
            var $docs_container = $(opts.documents_container);
            obj.children($docs_container).addClass('prizm-cloud-viewer-active');
            obj.children($docs_container).children('.doc-link').each(function () {

                $(this).on('click', function (e) {
                    e.preventDefault();
                    var $link = $(this),
                        docurl = "&document=",
                        rand_num = Math.floor(Math.random() * 11),
                        src_url = base_url + opts.key;
                    // don't reload active document
                    if(!$link.hasClass('active'))
                    {
                        $link.parent().find('.doc-link').removeClass('active');
                        $link.addClass('active');

                        if($link.data('doc-link') !== undefined) 
                        {
                            docurl += $link.data('doc-link');
                        }
                        else if($link.attr('href') !== undefined) 
                        {
                            // test for href as backup
                            docurl += $link.attr('href');
                        }
                        src_url += docurl + "&viewerheight=" + opts.viewerheight + "&viewerwidth=" + opts.viewerwidth + "&viewertype=" + opts.type;
                        if(opts.type.toLowerCase() == "slideshow") 
                        {
                            for(var skey in opts.slideshow)
                                src_url += "&" + skey + "=" + opts.slideshow[skey];
                        } 
                        else 
                        {
                            src_url += "&printButton=" + opts.print_button + "&toolbarColor=" + opts.toolbar_color;
                        }
                        var viewer_iframe = $('<iframe></iframe>', {
                            'id': "prizmcloud-iframe-" + rand_num,
                            'width': (opts.viewerwidth + 20),
                            'height': (opts.viewerheight + 30),
                            'src': src_url,
                            'frameborder': 0,
                            'seamless': 'seamless'
                        });
                        
                        obj.children(opts.viewer_container).html(viewer_iframe);
                    }
                    return false;
                });
            });
        }
    };
    $.fn.prizmcloud.defaults = {
        // These are the defaults.
        key: "03232898832",         // your Prizm Cloud Key, this is required
        type: "html5",              // html5, flash or slideshow
        viewerwidth: 500,           // viewer width
        viewerheight: 650,          // viewer height
        print_button: "Yes",        // Yes or No
        toolbar_color: "CCCCCC",    // hex color
        documents_container: "#documents-for-switching", // can be a class or id
        viewer_container: "#prizm-cloud-viewer",         // can be a class or id
        // slideshow options below
        slideshow: {
            animtype: "slide",      // animation type: slide or fade
            animduration: 450,      // # in milliseconds 
            animspeed: 4000,        // # in milliseconds
            automatic: "yes",       // Yes or No, automatically start the slideshow?
            showcontrols: "yes",    // Yes or no, show the slideshow navigation controls?
            centercontrols: "yes",  // Yes or No, center the slideshow controls?
            keyboardnav: "yes",     // Yes or No, allow keyboard navigation?
            hoverpause: "yes"       // Yes or No, Pause slideshow on mouse hover?
        }
    };
}(jQuery));