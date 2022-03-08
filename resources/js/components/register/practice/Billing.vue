<template>
    <div class="stripe-card-div">
        <card class="stripe-card"
              :class="{ complete }"
              :stripe="pk_stripe"
              :options="stripeOptions"
              @change='complete = $event.complete'
        />
        <div class="invalid-feedback" v-if="error">{{ error }}</div>
        <div class="form-group">
            <button type="submit" class="btn form-button" @click.stop.prevent="createToken()" :disabled="!complete">Add card</button>
        </div>
    </div>
</template>

<script>
    import { Card, createToken } from 'vue-stripe-elements-plus'

    export default {
        props: ['action','pk_stripe'],
        data () {
            return {
                complete: false,
                error: null,
                stripeOptions: {
                    // see https://stripe.com/docs/stripe.js#element-options for details
                }
            }
        },
        components: {Card},
        methods: {
            createToken () {
                this.error = null;
                // createToken returns a Promise which resolves in a result object with
                // either a token or an error key.
                // See https://stripe.com/docs/api#tokens for the token object.
                // See https://stripe.com/docs/api#errors for the error object.
                // More general https://stripe.com/docs/stripe.js#stripe-create-token.
                createToken().then(data => {
                    this.saveToken(data.token.id);
                });
            },
            saveToken(token) {
                let loader = this.$loading.show();
                axios({
                    method: 'POST',
                    url: this.action,
                    data: {
                        token: token
                    }
                })
                    .then(response => {
                        window.location.href = response.data.route;
                    })
                    .catch(error => {
                        this.error = error.response.data.error;
                    })
                    .finally(response => {
                        loader.hide()
                    });
            }
        }
    }
</script>

<style>
    .stripe-card {
        margin:10px auto;
    }
    .stripe-card {
        width: 300px;
        border: 1px solid grey;
    }
    .stripe-card.complete {
        border-color: green;
    }
</style>