<template>
    <div>
        <div class="bubbles">
            <span
                v-for="bubble in bubbles"
                @click="selectBuble(bubble.id)"
                class="bubble"
                :class="{'active' : isFieldExists(bubble.id)}"
            >{{ bubble.title }}</span>

            <input type="hidden" v-model="marks" :name="name" />
        </div>
        <span class="invalid-feedback" role="alert" v-if="error">
            <strong>{{ error }}</strong>
        </span>
    </div>

</template>

<script>
    import StarRating from "vue-star-rating";

    export default {
        props: [
            'bubbles',
            'name',
            'marksInit',
            'error'
        ],
        data() {
            return {
                marks: this.marksInit
            }
        },
        computed: {
        },
        methods: {
            selectBuble(field) {
                if (!this.isFieldExists(field)) {
                    this.marks.push(field);
                } else {
                    this.marks = _.remove(this.marks, (n) => {
                        return n !== field;
                    });
                }
            },
            isFieldExists(field) {
                return this.marks.indexOf(field) !== -1;
            }
        }
    }
</script>
