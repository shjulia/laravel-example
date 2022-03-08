<template>
    <div>
        <div class="form-group">
            <input type="text" placeholder="Template title" v-model="form.title" class="form-control" />
            <div class="invalid-feedback" v-if="server_errors.title" v-text="server_errors.title"></div>
        </div>
        <EmailEditor
            ref="emailEditor"
            v-on:load="editorLoaded"
        />
        <input type="hidden" v-model="form.html_content">
        <input type="hidden" v-model="form.json_content">
        <div class="text-center">
            <button class="btn btn-success" @click="exportJson()">Save</button>
        </div>
    </div>
</template>

<script>
import { EmailEditor } from 'vue-email-editor'
import {ServerErrors} from "../../mixins/ServerErrors";

export default {
    props: ['saveUrl', 'indexUrl', 'content', 'title', 'id'],
    components: {
        EmailEditor
    },
    data () {
        return {
            form: {
                title: '',
                html_content: '',
                json_content: ''
            },
        }
    },
    mixins: [ServerErrors],
    methods: {
        editorLoaded() {
            if (this.content) {
                this.$refs.emailEditor.editor.loadDesign(this.content);
            }
            if (this.title) {
                this.form.title = this.title;
            }
        },
        exportJson() {
            this.$refs.emailEditor.editor.saveDesign(
                (design) => {
                    this.form.json_content = JSON.stringify(design);
                    this.exportHtml();
                }
            )
        },
        exportHtml() {
            this.$refs.emailEditor.editor.exportHtml(
                (data) => {
                    this.form.html_content = data.html;
                    this.createTemplate();
                }
            )
        },
        createTemplate() {
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
                    console.log(error)
                })
                .finally(() => {
                    loader.hide();
                });
        }
    }
}
</script>
