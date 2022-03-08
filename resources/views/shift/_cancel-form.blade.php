<cancelshift
    inline-template
    cancel-action="{{ route('shifts.cancel', $shift) }}"
    index-url="{{ route('shifts.index') }}"
>
    <div>
        <button
            data-container="body"
            data-toggle="popover"
            data-placement="top"
            data-content="Cancel shift"
            data-trigger="hover"
            class="btn back-x wtext"
            @click.prevent.stop="cancel()"
        >
            <i class="fa fa-times" aria-hidden="true"></i> Cancel
        </button>
        @include('shift._reason-modal')
    </div>
</cancelshift>

