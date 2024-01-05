<template>
  <v-container fluid>
    <v-row>
      <v-col>
        <div class="wrp__statuses">
          <template>
            <div v-for="i in user_statuses" :key="i.id" class="status_wrp">
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
      <v-col class="table-container">
        <v-data-table
          :headers="headers"
          :items="logsdates"
          fixed-header
          hide-default-footer
          disable-pagination
          class="flex-table"
        />
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
import axios from "axios";
import _ from "lodash";
export default {
  name: "ReportUserTier",
  props: ["user_id", "dates", "statuses", "docs"],
  data() {
    return {
      logs: [],
      logsdates: [],
      headers: [{ text: "Date", value: "created" }],
      user_statuses: [],
    };
  },

  mounted() {
    this.getLogUserDates();
  },

  methods: {
    selLogsDates() {
      const dates = _.groupBy(this.logs, "created");
      let rows = [];
      _.map(dates, (r, k) => {
        let grsta = _.groupBy(r, "status_id");
        let row = {};
        row.created = k;
        this.user_statuses.map((s) => {
          row[s.id.toString()] = grsta[s.id].length;
        });
        row.docs = this.$props.docs.filter((d) => d.created == k).length ?? "";
        rows.push(row);
      });

      this.logsdates = rows;
    },
    setHeader() {
      let header = [];
      header.push({ text: "Date", value: "created" });
      this.user_statuses.map((s) => {
        header.push({ text: s.name, value: s.id.toString() });
      });
      header.push({ text: "Docs", value: "docs" });
      this.headers = header;
    },
    getLogUserDates() {
      const self = this;
      let data = {};
      data.user_id = self.$props.user_id;
      data.datefrom = self.$props.dates.datefrom;
      data.dateto = self.$props.dates.dateto;
      axios
        .post("/api/getLogUserDates", data)
        .then((res) => {
          self.logs = res.data.logs;
          self.user_statuses = _.orderBy(
            _.map(_.groupBy(res.data.logs, "status_id"), function (v, k) {
              let st = self.$props.statuses.find((e) => e.id == k);
              return { ...st, hm: v.length };
            }),
            "order"
          );
          self.setHeader();
          self.selLogsDates();
        })
        .catch((error) => console.log(error));
    },
  },
};
</script>

<style>
.table-container {
  display: flex;
  flex-grow: 1;
  overflow: hidden;
}

.flex-table {
  display: flex;
  flex-grow: 1;
  width: 100%;
}

.flex-table > div {
  width: 100%;
}
</style>
