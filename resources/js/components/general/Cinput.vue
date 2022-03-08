<template>
    <div class="form-group" :class="{'mat': isMat}">
        <div :class="{'input-group': prepend}">
            <label
                    :for="id"
                    :class="{'label_up': labelUp || (type == 'date'), 'with-prep': prepend}"

            >{{ label }}</label>
            <div v-if="prepend" class="input-group-prepend">
                <span class="input-group-text"><i class="fa" :class="'fa-' + prependIcon"></i></span>
            </div>
            <input
                    v-if="mask"
                    :id="id"
                    v-model="model"
                    :type="type"
                    class="form-control"
                    :class="{'is-invalid': hasErrors}"
                    :name="name"
                    :value="value"
                    :required="required"
                    v-mask="mask"
                    :inputmode="numberInput ? 'numeric' : false"
                    :pattern="numberInput ? '[0-9\-\(\) ]*' : false"
                    @focus="focus()"
                    @blur="blur()"
                    :autocomplete="autocomplete ? autocomplete : false"
            />
            <input
                    v-else
                    :id="id"
                    v-model="model"
                    :type="type"
                    class="form-control"
                    :class="{'is-invalid': hasErrors}"
                    :name="name"
                    :value="value"
                    :required="required"
                    :inputmode="numberInput ? 'numeric' : false"
                    :pattern="numberInput ? '[0-9\-\(\) ]*' : false"
                    :max="max ? max : false"
                    @focus="focus()"
                    @blur="blur()"
                    @input="inputEvent()"
                    :autocomplete="autocomplete ? autocomplete : false"
            />

            <span class="invalid-feedback" role="alert" v-if="hasErrors">
            <strong><i class="fa fa-exclamation-circle"></i> {{ firstError }}</strong>
        </span>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'label',
            'id',
            'type',
            'name',
            'value',
            'hasErrors',
            'firstError',
            'required',
            'isMat',
            'initModel',
            'initModelAttr',
            'prepend',
            'prependIcon',
            'autocomplete',
            'mask',
            'numberInput',
            'max'
        ],
        data () {
            return {
                labelUp: false,
                model: this.value ? this.value : this.initModel
            }
        },
        methods: {
            focus() {
                this.labelUp = true;
            },
            blur() {
                if (!this.model) {
                    this.labelUp = false;
                }
                if (this.initModelAttr) {
                    this.$parent[this.initModelAttr] = this.model;
                }
                this.$emit('blur-input');
            },
            inputEvent() {
                /*if (!this.model) {
                    this.labelUp = false;
                } else {
                    this.labelUp = true;
                }*/
                if (this.initModelAttr) {
                    this.$parent[this.initModelAttr] = this.model;
                }
            }
        },
        watch: {
            initModel() {
                this.model = this.initModel;
                this.labelUp = !!this.model
            }
        },
        mounted () {
            if (this.value || this.initModel) {
                this.labelUp = true;
            }
        }
    }
</script>