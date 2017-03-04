$(function() {


    glyph_opts = {
        map: {
            doc: "glyphicon glyphicon-file",
            docOpen: "glyphicon glyphicon-file",
            checkbox: "glyphicon glyphicon-unchecked",
            checkboxSelected: "glyphicon glyphicon-check",
            checkboxUnknown: "glyphicon glyphicon-share",
            dragHelper: "glyphicon glyphicon-play",
            dropMarker: "glyphicon glyphicon-arrow-right",
            error: "glyphicon glyphicon-warning-sign",
            expanderClosed: "glyphicon glyphicon-menu-right",
            expanderLazy: "glyphicon glyphicon-menu-right",  // glyphicon-plus-sign
            expanderOpen: "glyphicon glyphicon-menu-down",  // glyphicon-collapse-down
            folder: "glyphicon glyphicon-folder-close",
            folderOpen: "glyphicon glyphicon-folder-open",
            loading: "glyphicon glyphicon-refresh glyphicon-spin"
        }
    };


    $("#treetable").fancytree({
        extensions: ["glyph", "table","wide"],
        checkbox: true,
        selectMode: 3,

        glyph: glyph_opts,
        source: {
            url: "/loadsource",
            method: 'POST',
            data: {key:'root'}
        },
        table: {
            checkboxColumnIdx: 1,
            nodeColumnIdx: 2
        },

        activate: function (event, data) {
        },
        lazyLoad: function (event, data) {
            var node = data.node;
            // ajax request to load child nodes
            data.result = {
                type: "POST",
                url: "/loadsource",
                data: {key: node.key},
                cache: false,
                global: true,
                statusCode: {
                    401: function () {
                        document.location.reload(true);
                    }
                },
            }
        },
        renderColumns: function (event, data) {
            var node = data.node,
                $tdList = $(node.tr).find(">td");
            $tdList.eq(0).text(node.getIndexHier());
            $tdList.eq(3).text(node.data.size);
            $tdList.eq(4).text(node.data.owner);
        }
    });


    // handlers

    $("#btnDelete").click(function(){
        $("#treetable").fancytree("getTree").visit(function(node){

            //node.setSelected(false);

        });
        return false;
    });




});