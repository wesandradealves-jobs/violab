<template>
  <div class="page-header">
    <h2 class="post-header">
      <span v-html="this.titulo" class="post-header--title" />
      <router-link v-if="this.prevRoute" :to="{ path: this.prevRoute }">
        &#8592; Voltar
      </router-link>
    </h2>
  </div>
</template>
<script>
export default {
  name: "pageHeader",
  props: ["titulo", "prevRoute", "postType"]
};
</script>

<style lang="sass">
@import './sass/_pageHeader'
</style>
