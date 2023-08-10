import Vue from "vue";
import Vuetify from "vuetify";
import "vuetify/dist/vuetify.min.css";
Vue.use(Vuetify);

export default new Vuetify({
<<<<<<< HEAD
  theme: {
    themes: {
      light: {
        primary: "#7620df",
        secondary: "#696969",
        accent: "#8c9eff",
        error: "#b71c1c",
      },
      dark: {
        primary: "#7620df",
      },
    },
  },
  icons: {
    iconfont: "mdi", // 'mdi' || 'mdiSvg' || 'md' || 'fa' || 'fa4' || 'faSvg'
  },
=======
    theme: {
        themes: {
            light: {
                primary: "#2196F3",
                secondary: "#696969",
                accent: "#8c9eff",
                error: "#b71c1c",
            },
            dark: {
                primary: "#2196F3",
            },
        },
    },
    icons: {
        iconfont: "mdi", // 'mdi' || 'mdiSvg' || 'md' || 'fa' || 'fa4' || 'faSvg'
    },
>>>>>>> b71e0b8ed3a67110e14c90f9c973ed805ea1835b
});
