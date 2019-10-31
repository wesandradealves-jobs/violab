<template>
  <div class="stepsNavigator">
    <div 
    	:class="{'current' : nav.step === _current || nav.step < _current}"
      	@click="setNavigation(nav.step)"
      	v-for="nav in this.navigator.steps">
      <small>{{ nav.step }}.</small>
      <span>{{ nav.title }}</span>
    </div>
  </div>
</template>
<script>
export default {
  name: 'stepsNavigator',
  mounted () {
    const steps = document.querySelectorAll("[data-step]");

    for (var i = 0; i < steps.length; i++) {
      this.navigator.steps.push({
        step: parseInt(steps[i].dataset.step.replace("step-","")),
        title: steps[i].dataset.title
      });
    }
    this.$emit('steps', this.navigator.steps);
    this.$emit('current', this.navigator.current);
  },
  methods: {
    setNavigation(v) {
      this.navigator.current = v;
      this.$emit('current', v);
    },
    prev() {
      this.navigator.current--;
      this.$emit('current', this.navigator.current--);
    },
    next() {
      this.navigator.current++;
      this.$emit('current', this.navigator.current++);
    },  	
  },
  watch: {
    _switchCurrent(data) {
      this.setNavigation(data);
    }
  },
  computed: {
    _switchCurrent() {
      return this.switchCurrent;
    },
  	_current() {
  		return this.navigator.current;
  	}
  },
  props: {
    switchCurrent: {
      type: Number
    }
  },
  data() {
    return {
      navigator: {
    		steps: [],
    		current: 1
      }
    };
  },
};
</script>
<style lang="sass">
@import "./sass/_stepsNavigator"
</style>
