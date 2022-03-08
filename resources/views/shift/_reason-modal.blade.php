<div class="modal fade" id="reasonModal" tabindex="-1" role="dialog" aria-labelledby="reasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title m-auto" id="reasonModalLabel">Cancellation reason</h5>
            </div>
            <div class="modal-body">
                <div class="custom-control custom-radio">
                    <input class="custom-control-input" type="radio" v-model="cancel_reason" name="exampleRadios" id="r1" value="changed my mind" checked>
                    <label class="custom-control-label" for="r1">
                        changed my mind
                    </label>
                </div>
                <div class="custom-control custom-radio">
                    <input class="custom-control-input" type="radio" v-model="cancel_reason" name="exampleRadios" id="r2" value="found a temp elsewhere">
                    <label class="custom-control-label" for="r2">
                        found a temp elsewhere
                    </label>
                </div>
                <div class="custom-control custom-radio">
                    <input class="custom-control-input" type="radio" v-model="cancel_reason" name="exampleRadios" id="r3" value="finding a match took too long">
                    <label class="custom-control-label" for="r3">
                        finding a match took too long
                    </label>
                </div>
                <div class="custom-control custom-radio">
                    <input class="custom-control-input" type="radio" v-model="cancel_reason" name="exampleRadios" id="r4" value="other">
                    <label class="custom-control-label" for="r4">
                        other
                    </label>
                </div>
                <div class="form-group" v-if="cancel_reason == 'other'">
                    <textarea v-model="reason_text" class="form-control bordered" rows="3"></textarea>
                </div>
                <span class="invalid-feedback" role="alert" v-if="reason_error">
                    <strong>@{{ reason_error }}</strong>
                </span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn form-button" @click="reasonSubmit()">Submit</button>
            </div>
        </div>
    </div>
</div>
