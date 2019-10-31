<template>
  <ul class="social-networks">
    <li v-for="item in $store.state.header.social_networks" :key="item.slug">
      <a target="_blank" :href="item.url" :title="item.label">
        <i :class="'socicon-' + item.slug"></i>
      </a>
    </li>
  </ul>
</template>
<script>
export default {
  name: "socialNetworks"
};
</script>
<style lang="sass">
@import './sass/_socicalNetworks'
</style>
