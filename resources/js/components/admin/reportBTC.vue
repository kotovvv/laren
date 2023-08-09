<template>
  <div>
    <v-container fluid>
      <v-row>
        <v-col cols="3">
          <v-row class="px-4">
            <v-col><p>From Date</p></v-col>
            <v-col><p>By Date</p></v-col>
            <v-col><p class="text-right">New</p></v-col>
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
                      getBTCotherOnDate();
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
                      getBTCotherOnDate();
                    "
                  ></v-date-picker>
                </v-menu>
              </v-col>

              <v-checkbox
                class="mr-2"
                v-model="onlynew"
                @change="getBTCotherOnDate()"
              ></v-checkbox>
            </v-row>
          </div>
        </v-col>
        <v-col cols="2">
          <p>Filter by office</p>
          <v-select
            :items="offices"
            v-model="selected_office_ids"
            item-text="name"
            item-value="id"
            multiple
            class="border px-5"
          ></v-select>
          <!-- @change="" -->
        </v-col>
        <v-col cols="2">
          <p>Filter by supplier</p>
          <v-select
            v-model="filterProviders"
            :items="providers"
            item-text="name"
            item-value="id"
            outlined
            rounded
            multiple
          >
            <template v-slot:selection="{ item, index }">
              <span v-if="index === 0">{{ item.name }} </span>
              <span v-if="index === 1" class="grey--text text-caption">
                (+{{ filterProviders.length - 1 }} )
              </span>
            </template>
            <template v-slot:item="{ item, attrs }">
              <v-badge
                :value="attrs['aria-selected'] == 'true'"
                color="#7620df"
                dot
                left
              >
                {{ item.name }}
              </v-badge>
            </template>
          </v-select>
        </v-col>
        <v-col cols="2">
          <!-- statuses_lids -->
          <p>Filter by status</p>
          <v-select
            ref="filterStatus"
            color="red"
            v-model="filterStatus"
            :items="statuses"
            item-text="name"
            item-value="id"
            outlined
            rounded
            :multiple="true"
          >
            <template v-slot:selection="{ item, index }">
              <span v-if="index === 0">{{ item.name }} </span>
              <span v-if="index === 1" class="grey--text text-caption">
                (+{{ filterStatus.length - 1 }} )
              </span>
            </template>
            <template v-slot:item="{ item, attrs }">
              <v-badge
                :value="attrs['aria-selected'] == 'true'"
                color="#7620df"
                dot
                left
              >
                <i
                  :style="{
                    background: item.color,
                    outline: '1px solid grey',
                  }"
                  class="sel_stat mr-4"
                ></i>
              </v-badge>
              {{ item.name }}
            </template>
          </v-select>
        </v-col>
      </v-row>
    </v-container>
    <v-row>
      <v-col
        ><h3>Total leads: {{ filteredItems.length }}</h3></v-col
      >
      <v-col
        ><h3 class="green--text">Sum liads: {{ sum_depozit }}</h3></v-col
      >
      <v-col
        ><h3 class="blue--text">Sum BTC: {{ summ }}</h3></v-col
      >
      <v-col
        ><h3 class="red--text">Sum dates: {{ sum_dates }}</h3></v-col
      >
      <v-col></v-col>
    </v-row>
    <v-row>
      <v-col cols="12">
        <div class="border pa-4">
          <!-- show-select -->
          <v-data-table
            v-model.lazy.trim="selected"
            id="tabbtc"
            :headers="headers"
            :search="search"
            :single-select="false"
            item-key="id"
            show-expand
            @click:row="clickrow"
            :items="filteredItems"
            :expanded="expanded"
            ref="btctable"
            :loading="loading"
            loading-text="Uploading... Stand by."
          >
            <template
              v-slot:top="{ pagination, options, updateOptions }"
              :footer-props="{
                'items-per-page-options': [50, 10, 100, 250, 500, -1],
                'items-per-page-text': '',
              }"
            >
              <v-row>
                <v-spacer></v-spacer>
                <v-col cols="3" class="mt-3">
                  <v-data-footer
                    :pagination="pagination"
                    :options="options"
                    @update:options="updateOptions"
                    :items-per-page-options="[50, 10, 100, 250, 500, -1]"
                    :items-per-page-text="''"
                  />
                </v-col>
              </v-row>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="blackborder">
                <logtel :lid_id="item.lid_id" :key="item.id" />
              </td>
            </template>
          </v-data-table>
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import axios from "axios";
import _ from "lodash";
import logtel from "../manager/logtel";
export default {
  components: {
    logtel,
  },
  mounted: function () {
    this.getOffices();
    this.getBTCotherOnDate();
  },
  data: () => ({
    onlynew: true,
    loading: false,
    dateFrom: false,
    dateTo: false,
    dateTimeFrom: new Date(new Date().setDate(new Date().getDate() - 7))
      .toISOString()
      .substring(0, 10),
    dateTimeTo: new Date().toISOString().substring(0, 10),
    providers: [],
    filterProviders: [],
    statuses: [],
    filterStatus: [],
    offices: [],
    selected_office_ids: [],
    headers: [
      { text: "Name", value: "name" },
      { text: "Email", value: "email" },
      { text: "Phone.", align: "start", value: "tel" },
      { text: "Supplier", value: "p_name" },
      { text: "Created", value: "created_at" },
      { text: "Office", value: "o_name" },
      { text: "Status", value: "s_name" },
      { text: "Sum liads", value: "depozit", class: "green--text" },
      { text: "BTC", value: "summ", class: "blue--text" },
      { text: "Sum dates", value: "sum_dat", class: "red--text" },
    ],
    lids: [],
    expanded: [],
    selected: [],
    search: "",
    searchAll: "",
  }),
  computed: {
    filteredItems() {
      return this.lids.filter((i) => {
        return (
          (!this.filterProviders.length ||
            this.filterProviders.includes(i.provider_id)) &&
          (!this.selected_office_ids.length ||
            this.selected_office_ids.includes(i.office_id)) &&
          (!this.filterStatus.length || this.filterStatus.includes(i.status_id))
        );
      });
    },
    summ() {
      return _.sumBy(this.filteredItems, "summ");
    },
    sum_dates() {
      return _.sumBy(this.filteredItems, "sum_dat");
    },
    sum_depozit() {
      return _.sumBy(this.filteredItems, (i) => Number(i.depozit));
    },
  },
  methods: {
    getOffices() {
      let self = this;
      axios
        .get("/api/getOffices")
        .then((res) => {
          self.offices = res.data;
        })
        .catch((error) => console.log(error));
    },
    getBTCotherOnDate() {
      let self = this;
      this.loading = true;
      let data = {};
      if (this.datetimeFrom == "")
        this.datetimeFrom = new Date(
          new Date().setDate(new Date().getDate() - 7)
        )
          .toISOString()
          .substring(0, 10);
      if (this.datetimeTo == "")
        this.datetimeTo = new Date().toISOString().substring(0, 10);
      data.datefrom = this.dateTimeFrom;
      data.dateto = this.dateTimeTo;
      data.onlynew = this.onlynew;
      axios
        .post("/api/getBTCotherOnDate", data)
        .then((res) => {
          self.loading = false;
          self.lids = res.data.data;
          self.lids = self.lids.map((l) => {
            l.o_name = self.offices.find((o) => o.id == l.office_id).name;
            return l;
          });
          self.providers = res.data.providers;
          self.statuses = res.data.statuses;
        })

        .catch((error) => console.log(error));
    },

    clickrow(item, row) {
      if (!row.isSelected) {
        this.tel = item.tel;
        this.lid_id = item.lid_id;
        this.expanded = [item];
      } else this.tel = "";
      row.select(!row.isSelected);
    },
  },
};
</script>

