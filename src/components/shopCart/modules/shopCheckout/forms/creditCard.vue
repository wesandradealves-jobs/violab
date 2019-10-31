<template>
	<p v-show="this.currentstep === 4" data-title="Cartão de Crédito" data-step="step-4">
		<span>
			<input
			v-model="value.cartao_nome"
			placeholder="Nome"
			type="text"
			name="cartao_nome"
			>
		</span>
		<span v-show="value.cartao_nome" >
			<input 
			  v-model="value.nascimento_cartao" 
			  placeholder="Data de Nascimento" 
			  v-mask="['##/##/####']"
			  type="tel" 
			  name="nascimento_cartao" 
			>
		</span> 		
		<span v-show="value.nascimento_cartao&&value.cartao_nome">
			<input
			v-model="value.cartao_cpf_cnpj"
			placeholder="CPF"
			v-mask="['###.###.###-##']"
			type="text"
			name="cartao_cpf_cnpj"
			>
		</span>
		<span v-show="value.nascimento_cartao&&value.cartao_nome&&value.cartao_cpf_cnpj">
			<input
			v-model="value.cartao_telefone"
			placeholder="Telefone"
			v-mask="['(##) ####-####', '(##) #####-####']"
			type="tel"
			name="cartao_telefone"
			>
		</span>
		<span class="siders" v-show="value.nascimento_cartao&&value.cartao_nome&&value.cartao_cpf_cnpj&&value.cartao_telefone">
			<input
			v-model="value.numero_cartao"
			placeholder="Número do Cartão"
			type="text"
			v-mask="'#### #### #### ####'"
			name="numero_cartao"
			>
			<input
			v-model="value.cvv"
			placeholder="CVV"
			type="text"
			mask="NNN"
			name="cvv"
			v-show="value.numero_cartao"
			v-mask="'###'"
			>
		</span>
		<span v-show="value.nascimento_cartao&&value.cartao_nome&&value.cartao_cpf_cnpj&&value.cartao_telefone&&value.numero_cartao&&value.cvv">
			<input
			v-model="value.expiracao"
			name="expiracao"
			type="text"
			placeholder="MM/AAAA"
			v-mask="'##/####'"
			>
		</span>
<!-- 		<button class="btn btn-2" v-show="value.expiracao" @click.prevent="confirm" >
			Visualizar Pedido
		</button> -->
		<button :class="'btn btn-1 ' + ((!value.expiracao) ? '-disabled' : '')" @click.prevent="confirm" >
			Visualizar Pedido
		</button>
	</p>
</template>

<script>
import {mask} from 'vue-the-mask';
import store from "@/store";
export default {
	directives: {mask},
	name: 'creditCard',
	methods: {
	    confirm() {
	    	this.$emit('confirm');
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
	  data () {
	    return {
	      cpf: false,
	    }
	  },	
	watch: {
	    value() {
	        this.$emit('input', this.value);
	    }
	}
};
</script>