<template>
  <div>
    <v-snackbar v-model="snackbar" top right timeout="-1">
      <v-card-text v-html="message"></v-card-text>
      <template v-slot:action="{ attrs }">
        <v-btn color="pink" text v-bind="attrs" @click="snackbar = false">
          X
        </v-btn>
      </template>
    </v-snackbar>
    <v-container>
      <v-row>
        <v-col cols="3">
          <div class="status_wrp wrp_date px-3">
            <v-row align="center">
              <v-col>
                <v-menu
                  v-model="dateFrom"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="datetimeFrom"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      label="From"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    locale="en"
                    v-model="datetimeFrom"
                    @input="
                      dateFrom = false;
                      getProvUserDate();
                    "
                  ></v-date-picker>
                </v-menu>
              </v-col>
              <v-col>
                <v-menu
                  v-model="dateTo"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="datetimeTo"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      label="To"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    locale="en"
                    v-model="datetimeTo"
                    @input="
                      dateTo = false;
                      getProvUserDate();
                    "
                  ></v-date-picker>
                </v-menu>
              </v-col>
              <v-col>
                <v-menu
                  v-model="dateUpdated"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                  outlined
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="datetimeUpdated"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      label="Updated"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    locale="en"
                    v-model="datetimeUpdated"
                    @input="dateUpdated = false"
                  ></v-date-picker>
                </v-menu>
              </v-col>
            </v-row>
          </div>
        </v-col>

        <v-col cols="2">
          <v-select
            v-model="selectedProvider"
            :items="providers"
            label="Suppliers"
            item-text="name"
            item-value="id"
            outlined
            multiple
          >
            <template v-slot:selection="{ item, index }">
              <span v-if="index === 0">{{ item.name }} </span>
              <span v-if="index === 1" class="grey--text text-caption">
                (+{{ selectedProvider.length - 1 }} )
              </span>
            </template>
            <template v-slot:item="{ item, attrs }">
              <v-badge
                :value="attrs['aria-selected'] == 'true'"
                color="#2196f3"
                dot
                left
              >
                {{ item.name }}
              </v-badge>
            </template>
          </v-select>
        </v-col>
        <v-col cols="2">
          <v-autocomplete
            v-model="selectedUser"
            :items="users"
            label="Users"
            item-text="name"
            item-value="id"
            outlined
            clearable
          ></v-autocomplete>
        </v-col>
        <v-col>
          <v-btn class="btn primary" x-large @click="clearDupLids()"
            >Check duplicates</v-btn
          ></v-col
        >
      </v-row>
    </v-container>
  </div>
</template>
<script>
import axios from "axios";
export default {
  data() {
    return {
      snackbar: false,
      message: "",
      dateFrom: false,
      dateUpdated: false,
      dateTo: false,

      dateProps: { locale: "en", format: "24hr" },
      datetimeFrom: new Date(new Date().setDate(new Date().getDate() - 1))
        .toISOString()
        .substring(0, 10),
      datetimeUpdated: new Date(new Date().setDate(new Date().getDate() - 14))
        .toISOString()
        .substring(0, 10),
      datetimeTo: new Date().toISOString().substring(0, 10),
      users: [],
      selectedUser: 0,
      providers: [],
      selectedProvider: [],
    };
  },
  mounted() {
    this.getProvUserDate();
  },
  methods: {
    clearDupLids() {
      const self = this;
      let data = {};
      data.from = self.datetimeFrom;
      data.to = self.datetimeTo;
      data.updated = self.datetimeUpdated;
      data.providers = self.selectedProvider;
      data.user_id = self.selectedUser;
      axios
        .post("api/clearDupLids", data)
        .then(function (response) {
          console.log(response);
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    getProvUserDate() {
      const self = this;
      let data = {};
      data.from = self.datetimeFrom;
      data.to = self.datetimeTo;

      //   data.providers = self.selectedProvider;
      //   data.user_id = self.selectedUser;
      axios
        .post("api/getProvUserDate", data)
        .then(function (response) {
          console.log(response);
          self.users = response.data.users;
          self.providers = response.data.providers;
        })
        .catch(function (error) {
          console.log(error);
        });
    },
  },
};
</script>
