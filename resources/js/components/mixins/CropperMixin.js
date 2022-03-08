export const CropperMixin = {
    props: [
        'photoInit'
    ],
    data () {
        return {
            userAvatar: this.photoInit,
            loader: null,
            uploadError: null
        }
    },
    methods: {
        handleUploading(form, xhr) {
            this.loader = this.$loading.show();
        },
        handleUploaded(response, form, xhr) {
            this.userAvatar = response;
            this.loader.hide();
        },
        handlerError(message, type, xhr) {
            this.uploadError = message;
            this.loader.hide();
        },
    },
    computed: {
        headers() {
            return {'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content};
        },
        cropperOptions() {
            return {
                minCropBoxWidth: 200,
                aspectRatio: 1,
                movable: true,
                zoomable: true
            }
        },
        outputOptions() {
            return {width: 200, height: 200};
        },
        buttonsLabels() {
            return { 'submit': 'submit', 'cancel': 'cancel'};
        }
    }

};