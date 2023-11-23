<template>
  <div>
    <v-container fluid>
      <v-row>
        <v-col cols="3">
          <v-row class="px-4">
            <v-col><p>From Date</p></v-col>
            <v-col><p>By Date</p></v-col>
          </v-row>
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
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    locale="ru-ru"
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
      <v-row>
        <v-col>
          <table width="100%">
            <tr>
              <th>Date</th>
              <th>Provider</th>
              <th>Leads</th>
              <th style="width: 70vw">Data</th>
            </tr>
            <tr v-for="item in dates" :key="item.id">
              <td>{{ item.date }}</td>
              <td>{{ item.provider }}</td>
              <td>{{ item.hm }}</td>
              <td>{{ item.percent }}%</td>
            </tr>
          </table>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import axios from "axios";
import _ from "lodash";

export default {
  data: () => ({
    loading: false,
    dateFrom: false,
    dateTo: false,
    dateTimeFrom: new Date(new Date().setDate(new Date().getDate() - 7))
      .toISOString()
      .substring(0, 10),
    dateTimeTo: new Date().toISOString().substring(0, 10),
    dates: {},
  }),
  mounted: function () {
    this.reportCalDep();
  },
  methods: {
    reportCalDep() {
      let self = this;
      this.loading = true;
      if (this.datetimeFrom == "")
        this.datetimeFrom = new Date(
          new Date().setDate(new Date().getDate() - 7)
        )
          .toISOString()
          .substring(0, 10);
      if (this.datetimeTo == "")
        this.datetimeTo = new Date().toISOString().substring(0, 10);

      axios
        .get(
          `/api/reportCalDep?dateFrom=${this.dateTimeFrom}&dateTo=${this.dateTimeTo}`
        )
        .then((res) => {
          self.loading = false;
          self.dates = res.data.dates;
        })

        .catch((error) => console.log(error));
    },
  },
};
</script>
