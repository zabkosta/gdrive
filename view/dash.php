
<div class="container">
<div class="row">

    <nav class="navbar navbar-default">
        <div class="container-fluid">

            <div class="navbar-header">
                <a href="/">
                   <img src="/view/css/logof.png" width="42px" height="42px">
                </a>
            </div>


            <div class="btn btn-default btn-file"><i class="glyphicon glyphicon-cloud-upload"></i> Upload
                <input type="file"  name="attachments" id="fileinput">
            </div>



            <button id="btnDownload" type="button" class="btn btn-default navbar-btn"><i class="glyphicon glyphicon-cloud-download"></i> Download</button>
            <button id="btnDelete" type="button" class="btn btn-default navbar-btn"><i class=" glyphicon glyphicon-remove"></i> Delete</button>
            <button id="btnPreview" type="button" class="btn btn-default navbar-btn"><i class="glyphicon glyphicon-search"></i> Preview</button>

           <div class="ibox pull-right "><input class="" id="showShared" type="checkbox"> Include Shared files</div>


            <div class="progress hidden">
                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                    0%
                </div>
            </div>

            <div id="appstatus" class="alert alert-dismissible  hidden" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

            </div>


        </div>
    </nav>

<div id="treebox" class=" col-xs-12 table-responsive">



    <table id="treetable" class="table table-condensed table-hover table-striped fancytree-fade-expander">
        <colgroup>
            <col width="80px"></col>
            <col width="30px"></col>
            <col width="*"></col>
            <col width="100px"></col>
            <col width="200px"></col>

        </colgroup>
        <thead>
        <tr> <th></th> <th></th> <th>File</th> <th>Size,Kb</th> <th>Owner</th>  </tr>
        </thead>
        <tbody>
        <tr> <td></td> <td></td> <td></td> <td></td> <td></td>  </tr>
        </tbody>
    </table>



</div>
</div>
</div>


<!-- Modal -->
<div class="modal fade" id="alertModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-danger">WARNING!!!</h4>
            </div>
            <div class="modal-body">
                <b>Are you sure you want to delete this file ?<b?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button id="OKDelete" type="button" class="btn btn-primary">Yes, Just Do It</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for download files -->

<form id="dform" target="_blank" method="POST" action="/download" hidden>
    <input name="key" type="text" />
    <input name="mime" type="text" />

</form>

<!-- Hidden form for upload  files -->

<form id="upform" method="POST" action="" hidden>
    <input name="key" type="text" />
    <input name="mime" type="text" />

</form>