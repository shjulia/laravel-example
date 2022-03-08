<script>
    import {ServerErrors} from '../../mixins/ServerErrors';
    import Compressor from 'compressorjs';

    export default {
        props: [
            'showforminit',
            'action',
            'oldFileInit',
            'removeAction'
        ],
        data () {
            return {
                image: false,
                showForm: this.showforminit,
                no_policy: false,
                oldFile: this.oldFileInit
            }
        },
        mixins: [ServerErrors],
        methods: {
            showFile() {
                window.open(this.oldFile, 'file of insurance policy', "width=500,height=500,top=40,left=40");
            },
            remove() {
                let loader = this.$loading.show();
                axios({
                    method: 'DELETE',
                    url: this.removeAction,
                })
                    .then(response => {
                        this.oldFile = null;
                        this.image = false;
                        this.showForm = false;
                    })
                    .catch(response => {
                    })
                    .finally(response => {
                        loader.hide()
                    });
            },
            onChange () {
                this.image = true;
                let loader = this.$loading.show();
                axios({
                    method: 'POST',
                    url: this.action,
                    data: new FormData(this.$refs.photoform)
                })
                    .then(response => {
                        this.oldFile = response.data;
                    })
                    .catch(response => {
                    })
                    .finally(response => {
                        this.showForm = true;
                        loader.hide()
                    });
            },
            manualy() {
                this.showForm = true;
            }
        }
    }
</script>
