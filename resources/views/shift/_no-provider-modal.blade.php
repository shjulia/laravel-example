<div class="modal fade" id="noProviderModal" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="noProviderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title m-auto" id="reasonModalLabel">Provider didn’t show up</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <a href="#" @click="cancel()" class="btn btn-danger" data-dismiss="modal">Cancel Shift</a>
                <a href="#" @click="findNewProvider()" class="btn btn-primary" data-dismiss="modal">Find Another Provider</a>
            </div>
        </div>
    </div>
</div>
