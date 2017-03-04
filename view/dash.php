
<div class="container">
<div class="row">

    <nav class="navbar navbar-default">
        <div class="container-fluid">

            <div class="navbar-header">
                <a class="navbar-brand" href="#">
                    Brand
                </a>
            </div>

            <button type="button" class="btn btn-default navbar-btn">Upload</button>
            <button id="btnDownload" type="button" class="btn btn-default navbar-btn">Download</button>
            <button id="btnDelete" type="button" class="btn btn-default navbar-btn">Delete</button>

           <div class="ibox pull-right "><input class="" id="showShared" type="checkbox"> Show all files</div>



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
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button id="OKDelete" type="button" class="btn btn-primary">Yes, Just Do It</button>
            </div>
        </div>
    </div>
</div>