<template>
  <a
    id="back-to-top"
    @click="backToTop"
    :class="[this.scrollPosition > 0 ? 'is-active' : '']"
  >
    &#8593;
  </a>
</template>

<script>
import Vue from "vue";
  var vm = new Vue({
  data: {
  }
});
export default {
  name: "backToTop",
  methods: {
    backToTop: function() {
      this.$emit('backToTop');
    }
  },
  props: ["scrollPosition"],
  computed: {
    scrollY() {
      return this.$store.state.app.window.scrollY;
    },
    vm() {
      return vm;
    }
  }
};
</script>
<style lang="sass">
@import './sass/_backToTop'
</style>
