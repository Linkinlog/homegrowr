<template>
    <div class="card">
        <div class="card-header text-center">{{ name }}</div>
        <div class="card-body">
            <div :key="name" v-for="(value, name) in info">
                <h3 class="text-center text-capitalize">{{ name }}</h3>
                <area-chart :data="value"></area-chart>
                <h5>Current value : {{ value[0][1] }} / Time : {{ value[0][0] }}</h5>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  data() {
    return {
      info: null,
    };
  },
  mounted() {
    Vue.nextTick(() => {
      axios
        .get("/api/readings/" + this.uuid + "/?temperature=true")
        .then((response) => (this.info = response.data));
    });
  },
  props: ["uuid", "name"],
};
</script>

