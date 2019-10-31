import Vue from 'vue';
import Router from 'vue-router';

const Home = resolve => require(["@/views/Home/Home.vue"], resolve);
const PageNotFound = resolve =>
  require(["@/views/PageNotFound/PageNotFound.vue"], resolve);
const Artigos = resolve => require(["@/views/Artigos/Artigos.vue"], resolve);
const Single = resolve => require(["@/views/Single/Single.vue"], resolve);
const Loja = resolve => require(["@/views/Loja/Loja.vue"], resolve);

Vue.use(Router);

const router = new Router({
  mode: "history",
  base: process.env.BASE_URL,
  linkActiveClass: "current",
  routes: [
    {
      path: "*",
      name: "PageNotFound",
      component: PageNotFound,
      meta: {
        slug: "page-not-found",
        title: "Violab | 404"
      }
    },
    {
      path: "/",
      name: "Home",
      component: Home,
      meta: {
        slug: "home",
        title: "Violab"
      },
      props: true
    },
    {
      path: "/loja",
      name: "Loja",
      component: Loja,
      meta: {
        slug: "loja",
        title: "Violab | Loja"
      },
      props: true 
    },
    {
      path: "/conteudo",
      name: "Conteúdo",
      component: Artigos,
      meta: {
        slug: "artigos",
        title: "Violab | Conteúdo"
      },
      props: true
    },
    {
      path: "/conteudo/:id",
      component: Single,
      meta: {
        slug: "single",
        title: "Violab",
        post_type: "posts"
      },
      props: true
    },
    {
      path: "/loja/:id",
      component: Single,
      meta: {
        slug: "single-loja",
        title: "Violab",
        post_type: "produtos"
      },
      props: true
    }
  ]
});

router.beforeResolve((to, from, next) => {
  const nearestWithTitle = to.matched
    .slice()
    .reverse()
    .find(r => r.meta && r.meta.title);

  const nearestWithMeta = to.matched
    .slice()
    .reverse()
    .find(r => r.meta && r.meta.metaTags);

  if (nearestWithTitle) document.title = nearestWithTitle.meta.title;
  Array.from(document.querySelectorAll("[data-vue-router-controlled]")).map(
    el => el.parentNode.removeChild(el)
  );

  if (!nearestWithMeta) return next();
  nearestWithMeta.meta.metaTags
    .map(tagDef => {
      const tag = document.createElement("meta");

      Object.keys(tagDef).forEach(key => {
        tag.setAttribute(key, tagDef[key]);
      });
      tag.setAttribute("data-vue-router-controlled", "");
      return tag;
    })
    .forEach(tag => document.head.appendChild(tag));

    next();
});

router.afterEach((to, from) => {
  var s = document.querySelectorAll('.nav-anchor');

  if(from.name) {
    for (var i = 0; i < s.length; i++) {
      s[i].classList.remove('active');
    }
  }

  setTimeout(function () {
    for (var i = 0; i < s.length; i++) {
      if(s[i].dataset.anchor === to.meta.slug) {
        s[i].classList.add('active');
      }
    }
  }, 1000);    
});

export default router;