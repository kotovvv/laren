<template>
  <v-app id="inspire">
    <Visual
      id="videobg"
      ref="visual"
      object-fit="cover"
      video="/img/bg.mp4"
      :autoload="true"
      :autoplay="true"
      :loop="true"
      muted
      width="100%"
      height="100vh"
    />
    <v-main>
      <v-container fluid fill-height>
        <v-layout align-center justify-center>
          <v-flex xs12 sm8 md4>
            <v-card class="elevation-12">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>Connect to the system</v-toolbar-title>
              </v-toolbar>
              <div class="red">{{ message }}</div>
              <form>
                <v-card-text>
                  <v-form ref="form">
                    <v-text-field
                      label="Login"
                      name="name"
                      type="text"
                      v-model="fields.name"
                      :rules="userNameRequired"
                      required
                      @keyup.enter.native="onSubmit"
                    >
                      <v-icon slot="prepend" color="blue">
                        mdi-account-outline
                      </v-icon>
                    </v-text-field>

                    <v-text-field
                      id="password"
                      label="Password"
                      name="password"
                      :type="showPassword ? 'text' : 'password'"
                      v-model="fields.password"
                      :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                      @click:append="showPassword = !showPassword"
                      :rules="passwordRequired"
                      required
                      @keyup.enter.native="onSubmit"
                    >
                      <v-icon slot="prepend" color="blue">
                        mdi-textbox-password
                      </v-icon>
                    </v-text-field>
                  </v-form>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn width="100%" @click="onSubmit">Login</v-btn>
                </v-card-actions>
              </form>
            </v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import axios from "axios";
import Visual from "vue-visual";
import "vue-visual/index.css";
export default {
  props: ["login"],
  components: {
    Visual,
  },
  data: () => ({
    drawer: null,
    options: {
      isLoggingIn: true,
      shouldStayLoggedIn: true,
    },
    fields: {
      name: "",
      password: "",
    },
    errors: {},
    showPassword: false,
    userNameRequired: [(v) => !!v || "without login?"],
    passwordRequired: [(v) => !!v || "Password?"],
    message: "",
  }),
  methods: {
    onSubmit() {
      const self = this;
      this.errors = {};
      axios
        .post("/api/login", this.fields)
        .then((response) => {
          self.$emit("login", response.data.user);
          localStorage.user = JSON.stringify(response.data.user);
          if (response.data.status == "error") {
            console.log(response.data.status);
            this.message = "Incorrect login or password";
          }
        })
        .catch((error) => {
          if (error.response.status === 422) {
            self.errors = error.response.data.errors || {};
          }
        });
    },
  },
};
</script>

<style scoped>
#videobg {
  position: absolute;
}
</style>
