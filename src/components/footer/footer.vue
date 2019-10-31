<template>
  <footer id="contato" class="footer" data-anchor="contato">
    <div class="container">
      <div>
        <img
          v-if="$store.state.header.bloginfo.favico"
          :src="$store.state.header.bloginfo.favico"
          alt="&#169; ViolaB. Todos os direitos reservados."
        />
        <p class="copyright">&#169; Violab. Todos os direitos reservados.</p>
      </div>
      <div>
        <form id="form" v-on:submit="sendForm" method="POST">
          <div>
            <input
              ref="email"
              v-model="email"
              name="email"
              type="email"
              placeholder="E-mail"
            />
          </div>
          <div>
            <textarea
              ref="mensagem"
              v-model="mensagem"
              name="mensagem"
              placeholder="Mensagem"
            >
            </textarea>
          </div>
          <button>&#10230;</button>
          <p class="alert" v-if="this.alert">{{ this.alert }}</p>
        </form>
      </div>
    </div>
  </footer>
</template>
<script>
import Vue from "vue";
import axios from 'axios';
import VueAxios from 'vue-axios';
Vue.use(VueAxios, axios);
export default {
  name: "AppFooter",
  watch: {
    _email(data) {
      this.alert =
        data && !this.$refs.mensagem.value
          ? "Preencha o campo de mensagem."
          : "";
    },
    _mensagem(data) {
      this.alert =
        data && !this.$refs.email.value ? "Preencha o campo de e-mail." : "";
    }
  },
  computed: {
    _email() {
      return this.email;
    },
    _mensagem() {
      return this.mensagem;
    }
  },
  methods: {
    clearForm(s) {
      this.$store.state.isLoading = false;
      var self = this;
      this.alert =
        s instanceof Error
          ? "Ocorreram erros ao enviar."
          : "Enviado com sucesso.";
      setTimeout(function() {
        self.alert = "";
        self.email = "";
        self.mensagem = "";
      }, 1000);
      // - Debug
      if(s) console.log(s);
    },
    sendForm: function(event) {
      event.preventDefault();
      var self = this;
      self.$store.state.isLoading = true;
      if (!this.email || !this.mensagem) {
        this.clearForm();
        if (!this.email && this.mensagem) {
          this.alert = "Preencha o campo de e-mail.";
        } else if (!this.mensagem && this.email) {
          this.alert = "Preencha o campo de mensagem.";
        } else if (!this.mensagem && !this.email) {
          this.alert = "Ambos os campos são obrigatórios.";
        }
      } else {
          axios.get(`http://dev.uppercreative.com.br/phpmailer/send.php?email=${this.email}&msg=${this.mensagem}`)
          .then(function(res) {
            self.$store.state.isLoading = false; 

            self.clearForm(res);
          })
          .catch(function(err) {
            self.$store.state.isLoading = false; 
          }); 
      }
    }
  },
  data() {
    return {
      email: "",
      mensagem: "",
      alert: ""
    };
  }
};
</script>
<style lang="sass">
@import './sass/_footer'
</style>
