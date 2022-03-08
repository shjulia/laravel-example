<form action="{{ $action }}" method="POST" class="mt-3">
    @csrf
    <div class="row">
        <div class="col-sm-3">
            <Cinput
                label="Start Date"
                id="start_date"
                type="date"
                name="from"
                :required="false"
                :is-mat="true"
                max="{{ date('Y-m-d') }}"
            ></Cinput>
        </div>
        <div class="col-sm-3">
            <Cinput
                label="End Date"
                id="end_date"
                type="date"
                name="to"
                :required="false"
                :is-mat="true"
                max="{{ date('Y-m-d') }}"
            ></Cinput>
        </div>
        <div class="col-sm-3">
            <button type="submit" class="btn btn-primary">Export</button>
        </div>
    </div>
</form>
