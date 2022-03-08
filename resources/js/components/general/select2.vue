<template>
    <div>
        <select :multiple="multiple" :name="name" :class="'select2 ' + className">
            <option
                    v-for="item in options"
                    :value="item.id"
                    :selected="values.indexOf(item.id) !== -1 ? true : false"
            >{{ item.title }}</option>
        </select>
    </div>
</template>

<script type="text/javascript">
    export default {
        props: ['options', 'name', 'multiple', 'class-name', 'values'],
        mounted: function () {
            $('.select2')
                .select2();
        }
    }
</script>

<style>
    .select2 {
        width:100%;
    }
</style>