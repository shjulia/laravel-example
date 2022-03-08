export const ServerErrors = {
    data () {
        return {
            server_errors: {}
        }
    },
    methods: {
        inpChanged(field) {
            this.$delete(this.server_errors, field);
        }
    },
    mounted () {
        axios.interceptors.response.use((response) => {
            return response;
        }, (error) => {
            if (error.response && error.response.status === 422) {
                _.forEach(error.response.data.errors, (value, key) => {
                    this.$set(this.server_errors, key, value[0]);
                });
            }
            return Promise.reject(error);
        });
    }
};