<template>
  <div>
    <v-container fluid>
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
                      v-model="dateTimeFrom"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      outlined
                      label="From Date"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="dateTimeFrom"
                    @input="
                      dateFrom = false;
                      reportCalDep();
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
                      v-model="dateTimeTo"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      outlined
                      label="By Date"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    locale="en"
                    v-model="dateTimeTo"
                    @input="
                      dateTo = false;
                      reportCalDep();
                    "
                  ></v-date-picker>
                </v-menu>
              </v-col>
            </v-row>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
export default {
  name: "PeriodTier",

  data() {
    return {
      dateFrom: false,
      dateTo: false,
      dateTimeFrom: new Date(new Date().getFullYear(), new Date().getMonth(), 1)
        .toISOString()
        .substring(0, 10),
      dateTimeTo: new Date().toISOString().substring(0, 10),
    };
  },

  mounted() {},

  methods: {},
};
</script>
