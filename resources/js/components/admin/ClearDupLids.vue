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
                    @input="dateFrom = false"
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
                    @input="dateTo = false"
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
            label="Providers"
            item-text="name"
            item-value="id"
            outlined
            multiple
          ></v-select>
        </v-col>
        <v-col cols="2">
          <v-select
            v-model="selectedUser"
            :items="users"
            label="Users"
            item-text="name"
            item-value="id"
            outlined
          ></v-select>
        </v-col>
        <v-col>
          <v-btn class="btn primary" x-large>Check duplicates</v-btn></v-col
        >
      </v-row>
    </v-container>
  </div>
</template>
<script>
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
      selectedUser: [],
      providers: [],
      selectedProvider: [],
    };
  },
  mounted() {},
  methods: {
    clearDupLids() {},
  },
};
</script>
