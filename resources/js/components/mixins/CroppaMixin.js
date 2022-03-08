export const CroppaMixin = {
    data () {
        return {
            myCroppa: {},
            image: null,
            imageChanged: false
        }
    },
    methods: {
        uploadCroppedImage(callback) {
            this.myCroppa.generateBlob(
                blob => {
                    this.blobToBase64(blob, (res) => {
                        this.image = res;
                        this.$nextTick(() => {
                            callback();
                        });
                    });
                },
                'image/jpeg',
                1
            );
        },
        blobToBase64(blob, callback) {
            let reader = new FileReader();
            reader.readAsDataURL(blob);
            reader.onloadend = function() {
                callback(reader.result);
            }
        },
        changed() {
            this.imageChanged = true;
        },
        submit() {
            this.$loading.show();
            if (this.imageChanged) {
                this.uploadCroppedImage(() => {
                    $(this.$refs.baseform).submit();
                });
            } else {
                $(this.$refs.baseform).submit();
            }
        }
    }
};