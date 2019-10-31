<template>
	<div @click="close" v-if="isVisible" :class="`status-message -${this.status}`">
		<p>{{ statusMessage }}</p>
	</div>
</template>
<script>
export default {
  name: 'statusMessage',
  data () {
    return {
    	status: null
    }
  },
  methods: {
  	close() {
  	    this.$store.state.status_message.visible = false;
  	    this.$store.state.status_message.code = this.$store.state.status_message.message = null;
  	}
  },
  watch: {
    code(data) {
    	switch(data) {
    		case 0:
    			this.status = 'err';
    		break;
    		case 1:
    			this.status = 'success';
    		break;
    	}

      setTimeout(()=>{ this.close(); }, 6000);
    }, 
    statusMessage(newData) {
      this.$store.state.status_message.message = newData;

      this.$store.state.status_message.visible = newData ? true : false;

      var self = this;
    }
  },
  computed: {
    code() {
      return this.$store.state.status_message.code;
    },  
    isVisible() {
      return this.$store.state.status_message.visible;
    },  	
    statusMessage() {
      return this.$store.state.status_message.message;
    }
  }
};
</script>
<style lang="sass">
@import "./sass/_statusMessage"
</style>