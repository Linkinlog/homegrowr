<template>
    <div class="card">
        <div class="card-header text-center">{{ name }}</div>
        <div class="card-body">
        </div>
        <div :key="name" v-for="(value, name) in info">
            <h3 class="text-center text-capitalize">{{ name }}</h3>
            <area-chart :data="value"></area-chart>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                info : null,
            }
        },
        mounted () {
            Vue.nextTick(() => {
                axios
                .get('/api/readings/' + this.uuid + '/?temperature=true')
                .then(response => (this.info = response.data))
            })
        },
        props : [
            'uuid',
            'name',
        ]
}
</script>

