$(function () {


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
        extensions: ["glyph", "table", "wide"],
        checkbox: true,
        selectMode: 1,

        glyph: glyph_opts,
        source: {
            url: "/loadsource",
            method: 'POST',
            data: {key: 'root', shared: $('#showShared').is(':checked')}
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
                data: {key: node.key, shared: $('#showShared').is(':checked')},
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

    // show all files

    $('#showShared').on('click', function () {

        var tree = $("#treetable").fancytree("getTree");
        tree.reload({
            url: "/loadsource",
            method: 'POST',
            data: {key: 'root', shared: $('#showShared').is(':checked')}
        });
    });


    // delete block

    $('#alertModal').modal({
        show: false
    })


    $("#btnDelete").click(function () {

        $('#alertModal').modal('show');
        return false;
    });

    $("#OKDelete").click(function () {

        $('#alertModal').modal('hide');

        var tree = $("#treetable").fancytree("getTree");
        var nodes = tree.getSelectedNodes();

        // now supports only single node action.
        var keydata = nodes[0].key;



        $.ajax({
            method: "POST",
            url: '/delete',
            data: {key: keydata},
            cache: false,
            error: function (event, request) {


            },
            success: function (data) {
                // finally delete item from tree

                    var nd = tree.getNodeByKey(nodes[0].key);
                    nd.remove();
               // rebuild tree for adjust numbers col
                    tree.reload();

            }
        });
    });


    // upload


    // download


    $("#btnDownload").click(function () {

        var tree = $("#treetable").fancytree("getTree");
        var nodes = tree.getSelectedNodes();

        if ( nodes[0].data.mimeType.match(/application\/vnd.google-apps.folder/) !== null )  return;

        // now supports only single node action.
        var durl = nodes[0].data.dlink;

        if (!durl) {

            if ( nodes[0].data.mimeType.match(/application\/vnd.google-apps/) !== null ) {

                  $('#dform input:first-child').val(nodes[0].key);
                  $('#dform input:last-child').val(nodes[0].data.mimeType);
                  $('#dform').submit();
            } else
                window.open('https://www.googleapis.com/drive/v3/files/' +  nodes[0].key + '?alt=media','_blank');

        } else {

            window.open(durl, '_blank');
        }


    });
    $("#btnPreview").click(function () {

        var tree = $("#treetable").fancytree("getTree");
        var nodes = tree.getSelectedNodes();
        if ( nodes[0].data.mimeType.match(/application\/vnd.google-apps.folder/) !== null )  return;
        window.open(nodes[0].data.webViewLink, '_blank');
    });




});