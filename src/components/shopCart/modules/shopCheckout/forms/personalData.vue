<template>
    <p v-show="this.currentstep === 1" data-title="Dados Pessoais" data-step="step-1">
      <span>
        <input 
          v-model="value.nome" 
          placeholder="Nome" 
          type="text" 
          name="nome"
        >
      </span>
      <span v-show="value.nome" >
        <input 
          v-model="value.email" 
          placeholder="E-mail" 
          type="text" 
          name="email" 
        >
      </span>
      <span v-show="value.email&&value.nome" >
        <input 
          v-model="value.nascimento" 
          placeholder="Data de Nascimento" 
          v-mask="['##/##/####']"
          type="tel" 
          name="nascimento" 
        >
      </span>      
      <span v-show="value.nascimento&&value.email&&value.nome" >
        <input 
          v-model="value.telefone" 
          placeholder="Telefone" 
          v-mask="['(##) ####-####', '(##) #####-####']"
          type="tel" 
          name="telefone" 
        >
      </span>
      <span v-show="value.nascimento&&value.telefone&&value.email&&value.nome">
        <input
          v-model="value.cpfcnpj" 
          placeholder="CPF" 
          v-mask="['###.###.###-##']"
          type="text" 
          name="cpf_cnpj"
        >
      </span>
<!--       <button class="btn btn-1" v-show="value.cpfcnpj" @click.prevent="next(false)" >
        Próximo Passo
      </button> -->
      <button :class="'btn btn-1 ' + ((!value.cpfcnpj) ? '-disabled' : '')" @click.prevent="next(false)" >
        Próximo Passo
      </button>
    </p>
</template>

<script>
import {mask} from 'vue-the-mask';

export default {
  directives: {mask},
  name: 'personalData',
  methods: {
    next(s) {
      this.$emit('nextStep', s);
    }      
  },
  props: {
    currentstep: {
      type: Number
    },
      value: {
          type: Object,
          required: true
      }
  },
  watch: {
      value() {
          this.$emit('input', this.value);
      }
  }
}
</script>