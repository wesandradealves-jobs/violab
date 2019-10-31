import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    BASE_API: location.protocol + "//violab.hottank.com.br/sistema",
    wc: {
      _consumer: "ck_01dc479945b9d9e131eb7995fba00665f92d7dd2",
      _secret: "cs_f3044355e80f53eaf1cb35bba34ae7abbc82f239",
      _username: "violab",
      _password: "violab"
    },
    shop: {
      categories: null,
      app_cart: [],
      paypal_cart: [],
      products: [],
      mailingFiles: [],
      cart_total: 0,
      pagseguro: {
        _public_path: "https://stc.pagseguro.uol.com.br",
        _api: {
          _checkout: "https://ws.sandbox.pagseguro.uol.com.br/v2/checkout",
          _base_url: "https://sandbox.pagseguro.uol.com.br"
        },
        _app: {
          _email: "v96733010984511489079@sandbox.pagseguro.com.br",
          _password: "15N6B859375N6632",
          _public_key: "PUB888907497259486DBCD50E926062FABC"
        },
        _credentials: {
          // _email: "wesandradealves@outlook.com",
          _email: "violab.music@gmail.com",
          // _access_token: "F66E1AE1FB794ADC9F41F7404BEF4F36",
          // _access_token: "93b57281-3a6c-4927-a07a-95c01d575bec155c16db44a68f3676ddb7d3bfda688e0fef-8703-403c-9c7a-18ffb96b0888",
          _access_token: "844cf41a-d1bd-4f97-a04b-1a4c0406d7a624b47e2d402aaf57650ef77605b0f673bf50-745e-48ea-80bc-3e6e6803054a",
        }
      }
    },
    app: {
      isStarted: false
    },
    status_message: {
      visible: false,
      message: null,
      code: null,
      timeout: 0
    },
    isInit: true,
    isLoading: null,
    pages: null,
    blog: {
      posts: null,
      categories: null
    },
    home: null,
    header: {
      social_networks: null,
      bloginfo: "",
      navigation: null
    }
  },
  mutations: {},
  actions: {}
});
