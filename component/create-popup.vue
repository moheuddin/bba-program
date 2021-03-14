<template>
<h1>{{ greeting }}</h1>
</template>

<script>
export default {
    name: 'SkeletonComponent',
    data: function() {
        return {
            greeting: 'Hello'
        };
    },
    props: [],
    methods: {
    },
    created: function(){
    }
}
</script>

<style scoped>
h1 {
    font-size: 2em;
    text-align: center;
}
</style>