<template>
    <div>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label>Template</label>
                    <select class="form-control" v-model="form.template">
                        <option value=""></option>
                        <option v-for="template in templates" :value="template.id">{{ template.title }}</option>
                    </select>
                </div>
                <div class="invalid-feedback" v-if="server_errors.template" v-text="server_errors.template"></div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Start Datetime</label>
                    <input type="datetime-local" v-model="form.start_date" :min="minStart" class="form-control" />
                    <div class="invalid-feedback" v-if="server_errors.start_date" v-text="server_errors.start_date"></div>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" v-model="form.subject" class="form-control" />
                    <div class="invalid-feedback" v-if="server_errors.subject" v-text="server_errors.subject"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label>Emails</label>
                    <v-select taggable multiple push-tags v-model="form.emails" />
                    <div class="invalid-feedback" v-if="server_errors.emails" v-text="server_errors.emails"></div>
                    <div class="invalid-feedback" v-for="i in form.emails.length" v-if="server_errors['emails.' + i]" v-text="server_errors['emails.' + i]"></div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>Role</label>
                    <select class="form-control" v-model="form.role">
                        <option value=""></option>
                        <option v-for="role in roles" :value="role.id">{{ role.title }}</option>
                    </select>
                    <div class="invalid-feedback" v-if="server_errors.role" v-text="server_errors.role"></div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <button class="btn btn-success" @click="createNewsLetter()">Save</button>
        </div>
    </div>
</template>

<script>
import {ServerErrors} from "../../mixins/ServerErrors";
import Swal from 'sweetalert2';

export default {
    props: ['id', 'saveUrl', 'indexUrl', 'templates', 'newsLetter', 'roles'],
    data () {
        return {
            form: {
                template: '',
                subject: '',
                start_date: null,
                role: '',
                emails: []
            },
        }
    },
    mixins: [ServerErrors],
    methods: {
        createNewsLetter() {
            let loader = this.$loading.show();
            this.server_errors = {};
            axios({
                method: this.id ? 'PUT' : 'POST',
                url: this.saveUrl,
                data: this.form
            })
                .then(response => {
                    location.href = this.indexUrl;
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: error.response.data.error ? error.response.data.error : 'Something went wrong'
                    });
                })
                .finally(() => {
                    loader.hide();
                });
        }
    },
    computed: {
        minStart() {
            return moment().tz('America/New_York').format('YYYY-MM-DD\Thh:mm');
        }
    },
    mounted() {
        if (this.newsLetter) {
            this.form.emails = JSON.parse(this.newsLetter.emails);
            this.form.template = this.newsLetter.template_id;
            this.form.start_date = moment(this.newsLetter.start_date).format('YYYY-MM-DD\Thh:mm');
            this.form.role = this.newsLetter.role_id;
            this.form.subject = this.newsLetter.subject;
        }
    }
}
</script>
