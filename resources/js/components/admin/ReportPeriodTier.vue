<template>
  <div>
    <v-container fluid>
      <!-- dates -->
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
                      label="TO Date"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="dateTimeFrom"
                    @input="
                      dateFrom = false;
                      getTiersDates();
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
                      getTiersDates();
                    "
                  ></v-date-picker>
                </v-menu>
              </v-col>
            </v-row>
          </div>
        </v-col>
      </v-row>
      <v-progress-linear
        :active="loading"
        indeterminate
        color="blue"
      ></v-progress-linear>
      <v-row v-if="users.length" class="mt-5">
        <v-col>
          Managers: {{ users.length }} / Leads: {{ hm_docs_compl.length }} /
          Docs: {{ hm_docs }}
        </v-col>
      </v-row>
      <span v-if="hmtierstatuses.length" class="font-weight-thin">Tier</span>
      <v-row>
        <v-col>
          <div class="wrp__statuses">
            <template>
              <div v-for="i in hmtierstatuses" :key="i.id" class="status_wrp">
                <b
                  :style="{
                    background: i.color,
                    outline: '1px solid' + i.color,
                  }"
                  >{{ i.hm }}</b
                >
                <span>{{ i.name }}</span>
              </div>
            </template>
          </div>
        </v-col>
      </v-row>
      <span v-if="hmstatuses.length" class="font-weight-thin">All</span>
      <v-row>
        <v-col>
          <div class="wrp__statuses">
            <template>
              <div v-for="i in hmstatuses" :key="i.id" class="status_wrp">
                <b
                  :style="{
                    background: i.color,
                    outline: '1px solid' + i.color,
                  }"
                  >{{ i.hm }}</b
                >
                <span>{{ i.name }}</span>
              </div>
            </template>
          </div>
        </v-col>
      </v-row>

      <v-row>
        <v-col> </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import axios from "axios";
import _ from "lodash";
export default {
  name: "PeriodTier",

  data() {
    return {
      loading: false,
      dateFrom: false,
      dateTo: false,
      dateTimeFrom: new Date().toISOString().slice(0, 8) + "01",
      dateTimeTo: new Date().toISOString().substring(0, 10),
      users: [],
      statuses: [],
      hmstatuses: [],
      hmtierstatuses: [],
      hm_docs: 0,
      hm_docs_compl: 0,
      liads: [],
    };
  },

  mounted() {},

  methods: {
    getTiersDates() {
      const self = this;
      let data = {};
      self.loading = true;
      data.dateFrom = self.dateTimeFrom;
      data.dateTo = self.dateTimeTo;
      axios
        .post("/api/getTiersDates", data)
        .then((res) => {
          self.statuses = res.data.statuses = res.data.statuses;
          self.users = res.data.users;
          self.hm_docs = res.data.hm_docs[0].hm_docs;
          self.liads = res.data.liads;
          self.hm_docs_compl = _.filter(self.liads, { docs_compl: 1 });
          self.hmstatuses = _.orderBy(
            _.map(_.groupBy(self.liads, "status_id"), function (v, k) {
              let st = self.statuses.find((e) => e.id == k);
              return { ...st, hm: v.length };
            }),
            "order"
          );
          self.hmtierstatuses = _.orderBy(
            _.map(_.groupBy(self.hm_docs_compl, "status_id"), function (v, k) {
              let st = self.statuses.find((e) => e.id == k);
              return { ...st, hm: v.length };
            }),
            "order"
          );
          self.loading = false;
        })
        .catch((error) => console.log(error));
    },
  },
};
</script>
