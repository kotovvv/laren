<template>
  <v-app id="inspire">
    <v-main>
      <v-container fluid fill-height>
        <v-layout align-center justify-center>
          <v-flex xs12 sm8 md4>
            <v-card class="elevation-12">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>Connect to the system</v-toolbar-title>
              </v-toolbar>
              <form>
                <v-card-text>
                  <v-form ref="form">
                    <v-text-field
<<<<<<< HEAD
                      label="Логин"
=======
                      label="Login"
>>>>>>> b71e0b8ed3a67110e14c90f9c973ed805ea1835b
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
<<<<<<< HEAD
                  <v-btn color="primary" @click="onSubmit">Войти</v-btn>
=======
                  <v-btn width="100%" @click="onSubmit">Login</v-btn>
>>>>>>> b71e0b8ed3a67110e14c90f9c973ed805ea1835b
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
export default {
  props: ["login"],
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
    userNameRequired: [(v) => !!v || "без логина?"],
    passwordRequired: [(v) => !!v || "Password?"],
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
</style>
