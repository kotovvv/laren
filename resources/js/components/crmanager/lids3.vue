<template>
  <div>
    <v-container fluid>
      <v-row>
        <!-- <v-col cols="1">
          <p>Reset</p>
          <v-btn @click="clearFilter" class="border" outlined
            ><v-icon>close</v-icon></v-btn
          >
        </v-col> -->
        <v-col cols="3">
          <v-row class="px-4">
            <v-col><p>From Date</p></v-col>
            <v-col><p>By Date</p></v-col>
          </v-row>

          <div class="status_wrp wrp_date">
            <div class="d-flex align-center">
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
                    outlined
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="datetimeFrom"
                  @input="
                    dateFrom = false;
                    savedates == true ? getLids3(0) : null;
                  "
                ></v-date-picker>
              </v-menu>

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
                    outlined
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="datetimeTo"
                  @input="
                    dateTo = false;
                    getLids3(0);
                  "
                ></v-date-picker>
              </v-menu>

              <v-checkbox
                hide-details
                class="mt-0 pt-0"
                v-model="savedates"
              ></v-checkbox>
              <!-- <v-btn @click="clearuser" small text
                ><v-icon>refresh</v-icon></v-btn
              > -->
            </div>
          </div>
        </v-col>

        <v-col>
          <!-- statuses_lids -->
          <p>Selection by status</p>
          <v-select
            ref="filterStatus"
            color="red"
            v-model="filterStatus"
            @change="getPage(0)"
            :items="statuses"
            item-text="name"
            item-value="id"
            outlined
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
                color="#2196f3"
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

        <v-col>
          <p>Selection by supplier</p>
          <v-select
            v-model="filterProviders"
            :items="providers"
            item-text="name"
            item-value="id"
            @change="getPage(0)"
            outlined
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
                color="#2196f3"
                dot
                left
              >
                {{ item.name }}
              </v-badge>
            </template>
          </v-select>
        </v-col>

        <v-col cols="3">
          <p>
            <v-radio-group
              row
              v-model="searchradio"
              class="searchradio"
              hide-details
              change="getPage"
            >
              <v-radio label="name" value="name"></v-radio>
              <v-radio label="email" value="email"></v-radio>
              <v-radio label="phone" value="phone"></v-radio>
              <v-radio label="comment" value="comment"></v-radio>
            </v-radio-group>
          </p>
          <v-text-field
            v-model.lazy.trim="filtertel"
            append-icon="mdi-magnify"
            @input="getPage"
            outlined
          ></v-text-field>
        </v-col>
        <!-- v-if="$props.user.role_id == 1" -->
        <v-col>
          <p>Search</p>
          <v-text-field
            v-model="searchAll"
            append-icon="mdi-magnify"
            @click:append="searchlids3"
            outlined
          ></v-text-field>
        </v-col>
        <v-col v-if="$props.user.role_id == 1 && $props.user.office_id == 0">
          <p>Office filter</p>
          <v-select
            v-model="filterOffices"
            :items="offices"
            item-text="name"
            item-value="id"
            outlined
            @change="
              getUsers();
              getPage(0);
            "
          >
          </v-select>
        </v-col>
      </v-row>
    </v-container>
    <v-progress-linear
      :active="loading"
      indeterminate
      color="blue"
    ></v-progress-linear>
    <v-row>
      <v-col>
        <div class="wrp__statuses">
          <template v-for="(i, x) in Statuses">
            <div class="status_wrp" :key="x">
              <b
                :style="{
                  background: i.color,
                  outline: '1px solid' + i.color,
                }"
                >{{ i.hm }}</b
              >
              <span>{{ i.name }}</span>
              <v-btn
                v-if="filterStatus.length > 0"
                icon
                x-small
                @click="changeFilterStatus(i.id)"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>
          </template>
        </div>
      </v-col>
    </v-row>
    <v-row v-if="filterProviders.length > 0">
      <v-col>
        <div class="wrp__providers">
          <template v-for="(i, x) in filterProviders">
            <div class="provider_wrp" :key="x">
              {{ getProviderName(i) }}
              {{ i.name }}
              <v-btn icon x-small @click="changeFilterProviders(i)">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </div>
          </template>
        </div>
      </v-col>
    </v-row>
    <v-snackbar
      v-model="snackbar"
      top
      rigth
      timeout="6000"
      color="success"
      dark
    >
      {{ message }}
      <template v-slot:action="{ attrs }">
        <v-btn color="white" text v-bind="attrs" @click="snackbar = false">
          Х
        </v-btn>
      </template>
    </v-snackbar>
    <v-row class="align-center">
      <!-- <v-col cols="2" v-if="$props.user.role_id == 1">
        <v-btn outlined @click="exportXlsx">
          <v-icon left> mdi-file-excel </v-icon>
          Download the table
        </v-btn>
      </v-col> -->
      <!-- <v-spacer></v-spacer> -->
      <v-col cols="4">
        <div class="d-flex align-center">
          <v-select
            v-model="selectedStatus"
            :items="[{ name: 'Default', id: 0 }, ...filterstatuses]"
            item-text="name"
            item-value="id"
            outlined
            label="Status assignment"
          >
            <template v-slot:selection="{ item }">
              <i
                :style="{
                  background: item.color,
                  outline: '1px solid grey',
                }"
                class="sel_stat mr-4"
              ></i
              >{{ item.name }}
            </template>
            <template v-slot:item="{ item }">
              <i
                :style="{
                  background: item.color,
                  outline: '1px solid grey',
                }"
                class="sel_stat mr-4"
              ></i
              >{{ item.name }}
            </template>
          </v-select>
          <v-btn
            :disable="!selected.length && selectedStatus == 0"
            class="border ma-2"
            outlined
            @click="changeStatus"
          >
            Change status
          </v-btn>
          <v-btn @click="dialogDelete = true" class="red accent-2"
            >CLEER ALL LOGS</v-btn
          >
          <v-dialog v-model="dialogDelete" max-width="700px">
            <v-card class="py-10">
              <v-card-title class="headline justify-center my-10 py-10"
                >Logs will be deleted. Do you confirm?</v-card-title
              >
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1 dark " @click="dialogDelete = false"
                  >No</v-btn
                >
                <v-btn color="blue darken-1 dark" @click="clearLeads()"
                  >Yes</v-btn
                >
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </div>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="9">
        <div>
          <!-- @update:sort-by="makeSort"
          @update:sort-desc="makeSort" -->
          <v-data-table
            v-model.lazy.trim="selected"
            id="tablids"
            :headers="headers"
            :search="search"
            :single-select="false"
            item-key="id"
            show-select
            show-expand
            @click:row="clickrow"
            :items="lids"
            :disable-pagination="true"
            hide-default-footer
            :footer-props="{
              disablePagination: true,
              disableItemsPerPage: true,
            }"
            :expanded.sync="expanded"
            ref="datatable"
            :loading="loading"
            loading-text="Uploading... Stand by."
            class="elevation-1"
          >
            <!-- <template v-slot:top="{}">
              <v-row> -->
            <!-- <v-col cols="2">
                  <div class="d-flex pl-2 align-center border">
                    Selection
                    <v-text-field
                      class="mx-2 mt-0 pt-0 talign-center nn"
                      @input="selectRow"
                      :max="limit"
                      v-model.number="hmrow"
                      hide-details="auto"
                      color="#004D40"
                    ></v-text-field>
                  </div>

                  <v-text-field
                    v-model="search"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                    class="border px-2"
                  ></v-text-field>
                </v-col> -->
            <!--                 <v-col class="wrp_group">
                  <v-row>
                    <v-checkbox
                      v-model="filterGroups"
                      v-for="groupa in group"
                      :key="groupa.id"
                      :value="groupa.id"
                      :hide-details="true"
                      @change="getLids3"
                    >
                      <template v-slot:label>
                        <div class="img">{{ groupa.fio.slice(0, 3) }}</div>
                      </template>
                    </v-checkbox>
                  </v-row>
                  <v-row class="align-center">
                    <h5 class="mb-0">All:{{ hm }}</h5>
                    <v-pagination
                      v-model="page"
                      class="my--4"
                      :length="parseInt(hm / limit) + 1"
                      @input="getPage()"
                      total-visible="10"
                    ></v-pagination>
                  </v-row>
                </v-col> -->
            <!-- <v-col cols="3" class="mt--3">
                  <v-row>
                    <v-select
                      v-model="limit"
                      class="mt-2 border"
                      :items="[10, 50, 100, 250, 500, 'all']"
                      @change="getPage(0)"
                    ></v-select
                  ></v-row>
                </v-col> -->
            <!-- </v-row>
            </template> -->
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="blackborder">
                <logtel :lid_id="item.id" :key="item.id" />
              </td>
            </template>
          </v-data-table>
          <v-row class="align-center">
            <v-col
              ><h5 class="mb-0">All:{{ hm }}</h5></v-col
            >
            <v-col>
              <v-pagination
                v-model="page"
                class="my--4"
                :length="parseInt(hm / limit) + 1"
                @input="getPage()"
                total-visible="20"
              ></v-pagination
            ></v-col>
            <v-col cols="1">
              <v-select
                v-model="limit"
                class="mt-2"
                :items="[10, 50, 100, 250, 500, 'all']"
                @change="getPage(0)"
                outlined
              ></v-select>
            </v-col>
          </v-row>
        </div>
      </v-col>
      <v-col cols="3">
        <div class="w-100 wrp_users">
          <!-- <div class="my-3">User Search</div>
          <v-autocomplete
            v-model="selectedUser"
            :items="users"
            label="Select"
            item-text="fio"
            item-value="id"
            :return-object="true"
            append-icon="mdi-close"
            outlined
            @click:append="clearuser()"
          ></v-autocomplete> -->

          <div class="scroll-y">
            <v-list>
              <v-radio-group
                id="usersradiogroup"
                ref="radiogroup"
                v-model="userid"
                v-bind="users"
                @change="changeLidsUser"
              >
                <v-expansion-panels ref="akk" v-model="akkvalue">
                  <v-expansion-panel v-for="(item, i) in group" :key="i">
                    <v-expansion-panel-header>
                      <div
                        height="60"
                        width="60"
                        class="img v-expansion-panel-header__icon mr-1"
                      >
                        {{ item.fio.slice(0, 3) }}
                      </div>

                      {{ item.fio }}
                      <div></div>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                      <v-row
                        v-for="user in users.filter(function (i) {
                          return i.group_id == item.group_id;
                        })"
                        :key="user.id"
                      >
                        <v-radio
                          :label="user.fio"
                          :value="user.id"
                          :disabled="disableuser == user.id"
                        >
                        </v-radio>

                        <v-btn
                          class="ml-3"
                          small
                          :color="usercolor(user)"
                          @click="
                            disableuser = user.id;
                            filterGroups = [];
                            getPage();
                          "
                          :value="user.hmlids"
                          :disabled="disableuser == user.id"
                          >{{ user.hmlids }}</v-btn
                        >
                        <v-btn data="new" v-if="user.statnew" label small>
                          {{ user.statnew }}
                        </v-btn>
                        <v-btn data="inp" v-if="user.inp" label small>
                          {{ user.inp }}
                        </v-btn>
                        <v-btn data="cb" v-if="user.cb" label small>
                          {{ user.cb }}
                        </v-btn>
                      </v-row>
                    </v-expansion-panel-content>
                  </v-expansion-panel>
                </v-expansion-panels>
              </v-radio-group>
            </v-list>
          </div>
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import XLSX from "xlsx";
import axios from "axios";
import _ from "lodash";
import logtel from "../manager/logtel";
export default {
  props: ["user"],
  data: () => ({
    searchradio: "phone",
    savedates: true,
    akkvalue: null,
    loading: false,
    selectedUser: {},
    group: null,
    modal: false,
    dateFrom: false,
    dateTo: false,
    callback: false,
    dateProps: { locale: "en", format: "24hr" },
    datetimeFrom: new Date(new Date().setDate(new Date().getDate() - 14))
      .toISOString()
      .substring(0, 10),
    datetimeTo: new Date().toISOString().substring(0, 10),
    userid: null,
    users: [],
    disableuser: 0,
    statuses: [],
    filterstatuses: [],
    selectedStatus: 0,
    filterStatus: [],
    filterGStatus: 0,
    filterProviders: [],
    filterGroups: [],
    selected: [],
    lids: [],
    search: "",
    searchAll: "",
    filtertel: "",
    headers: [
      { text: "Name", value: "name" },
      { text: "Email", value: "email" },
      { text: "Tel.", align: "start", value: "tel" },
      { text: "Afilator", value: "afilyator" },
      { text: "Supplier", value: "provider" },
      { text: "Manager", value: "user" },
      { text: "Created", value: "date_created" },
      { text: "Changed", value: "date_updated" },
      { text: "Status", value: "status" },
      { text: "Depozit", value: "depozit" },
      { text: "Message", value: "text" },
      { text: "Calls", value: "qtytel" },
      { text: "CALLING.", value: "ontime" },
    ],
    parse_header: [],
    sortOrders: {},
    sortKey: "tel",
    providers: [],
    showDuplicates: false,
    telsDuplicates: [],
    clickedItemStatuses: [],
    clickedItemTel: "",
    tel: "",
    lid_id: "",
    expanded: [],
    singleExpand: true,
    componentKey: 0,
    Statuses: [],
    hmrow: "",
    offices: [],
    filterOffices: 1,
    hm: 0,
    snackbar: false,
    message: "",
    page: 0,
    limit: 100,
    archSelected: [],
    sortBy: "",
    sortDesc: true,
    dialogDelete: false,
  }),
  mounted: function () {
    this.getUsers();
    this.getProviders();
    this.getStatuses();
    this.getOffices();
    if (localStorage.filterStatus) {
      this.filterStatus = localStorage.filterStatus
        .split()
        .map((el) => parseInt(el));
    }
    if (localStorage.savedates) {
      this.savedates = localStorage.savedates == "true" ? true : false;
    }
    if (localStorage.callback) {
      this.callback = localStorage.callback == "true" ? true : false;
    }
    if (localStorage.tel) {
      this.filtertel = localStorage.tel;
    }
    if (this.savedates == true) {
      if (localStorage.datetimeFrom) {
        this.datetimeFrom = new Date(localStorage.datetimeFrom)
          .toISOString()
          .substring(0, 10);
      }
      if (localStorage.datetimeTo) {
        this.datetimeTo = new Date(localStorage.datetimeTo)
          .toISOString()
          .substring(0, 10);
      }
    }

    if (localStorage.filterProviders) {
      this.filterProviders = localStorage.filterProviders
        .split()
        .map((el) => parseInt(el));
    }
    this.getLids3();
  },

  watch: {
    selectedUser(user) {
      if (user == {}) {
        this.userid = null;
        return;
      }
      //this.disableuser = user.id;
      this.akkvalue = _.findIndex(this.group, { group_id: user.group_id });
    },
    filterStatus(newName) {
      localStorage.filterStatus = newName.toString();
      this.selectRow();
    },
    callback(newName) {
      localStorage.callback = newName;
      this.getLids3();
    },
    savedates(newName) {
      localStorage.savedates = newName;
      this.getLids3();
    },

    datetimeFrom(newName) {
      if (this.savedates == true) {
        localStorage.datetimeFrom = newName;
      }
    },
    datetimeTo(newName) {
      if (this.savedates == true) {
        localStorage.datetimeTo = newName;
      }
    },
    filtertel(newName) {
      localStorage.tel = newName;
    },
    filterProviders(newName) {
      localStorage.filterProviders = newName.toString();
    },
    filterGStatus: function (newval, oldval) {
      if (newval != 0) {
        this.getStatusLids(newval);
      }
    },
  },
  computed: {},
  methods: {
    getPage(page) {
      if (this.selected.length) {
        this.archSelected = this.archSelected.concat(this.selected);
        this.selected = this.archSelected;
        this.archSelected = [];
      }
      if (this.searchAll != "") {
        this.searchlids3();
      } else {
        if (page == 0) {
          this.page = 0;
        }
        this.getLids3();
      }
    },
    getOffices() {
      let self = this;
      self.filterOffices = this.$props.user.office_id;
      if (self.$props.user.role_id == 1 && self.$props.user.office_id == 0) {
        axios
          .get("/api/getOffices")
          .then((res) => {
            self.offices = res.data;
            self.offices.unshift({ id: 0, name: "--select--" });
            self.filterOffices = self.offices[1].id;
          })
          .catch((error) => console.log(error));
      }
    },
    getProviderName(i) {
      let name = "NA";
      try {
        name = this.providers.find((el) => el.id == i).name;
      } catch (error) {
        console.error(error);
      }
      return name;
    },
    changeFilterProviders(el) {
      this.filterProviders = this.filterProviders.filter((i) => i != el);
      this.getPage(0);
    },
    changeFilterStatus(el_id) {
      this.filterStatus = this.filterStatus.filter((i) => i != el_id);
      this.getPage(0);
    },
    clearuser() {
      this.disableuser = 0;
      this.filterGroups = [];
      if (this.$props.user.role_id == 2) {
        this.disableuser = this.$props.user.id;
        this.filterGroups.push(this.$props.user.id);
      }
      this.getLids3();
      this.selectedUser = {};
    },
    checked(at) {
      //console.log(at["aria-selected"]);
      return at["aria-selected"];
    },
    getLids3() {
      let self = this;
      let data = {};
      self.loading = true;
      if (this.savedates == true) {
        if (this.datetimeFrom == "") {
          this.datetimeFrom = new Date(
            new Date().setDate(new Date().getDate() - 14)
          )
            .toISOString()
            .substring(0, 10);
        }
        if (this.datetimeTo == "") {
          this.datetimeTo = new Date().toISOString().substring(0, 10);
        }

        data.datefrom = this.getLocalDateTime(this.datetimeFrom);
        data.dateto = this.getLocalDateTime(this.datetimeTo);
        data.id = this.disableuser;
        if (this.$props.user.role_id == 2 && this.disableuser == 0) {
          data.id = this.$props.user.id;
        }
      } else {
        let id = this.disableuser > 0 ? this.disableuser : this.$props.user.id;
        this.disableuser = id;
        data.id = id;
      }
      if (this.filterGroups.length) {
        data.group_ids = this.filterGroups;
      }
      data.provider_id = self.filterProviders;
      data.status_id = self.filterStatus;
      data.tel = self.filtertel;
      data.searchradio = self.searchradio;
      data.search = self.search;
      data.limit = self.limit;
      data.page = self.page;
      data.office_id = self.filterOffices;

      if (this.callback === true) {
        data.callback = 1;
      }

      axios
        .post("/api/getLids3", data)
        .then((res) => {
          self.hm = res.data.hm;

          if (self.page == 0) {
            self.Statuses = res.data.statuses;
          }

          self.lids = res.data.lids;

          self.lids.map(function (e) {
            if (e.updated_at) {
              e.date_updated = e.updated_at.substring(0, 10);
            }
            if (e.created_at) {
              e.date_created = e.created_at.substring(0, 10);
            }
            if (e.status_id && self.statuses.length) {
              e.status =
                self.statuses.find((s) => s.id == e.status_id).name || "";
            }
            if (e.user_id) {
              e.user = self.users.find((u) => u.id == e.user_id)?.fio || "";
            }
            e.provider = self.getProviderName(e.provider_id);
          });
          self.loading = false;
        })
        .then(() => {
          self.selectRow();
        })
        .catch((error) => console.log(error));
    },
    selectRow() {
      if (this.hmrow) {
        this.selected = this.$refs.datatable.internalCurrentItems.slice(
          0,
          this.hmrow
        );
      }
    },
    cleardate() {
      this.datetimeFrom = new Date(
        new Date().setDate(new Date().getDate() - 14)
      )
        .toISOString()
        .substring(0, 10);
      this.datetimeTo = new Date().toISOString().substring(0, 10);
      if (this.savedates != true) {
        localStorage.removeItem("datetimeTo");
        localStorage.removeItem("datetimeFrom");
      }
      if (this.disableuser == 0) {
        this.getLids3();
      }
    },
    clearFilter() {
      this.datetimeFrom = new Date(
        new Date().setDate(new Date().getDate() - 14)
      )
        .toISOString()
        .substring(0, 10);
      this.datetimeTo = new Date().toISOString().substring(0, 10);
      this.filterStatus = [];
      this.filterProviders = [];
      this.filtertel = "";
      this.disableuser = 0;
      this.getLids3();
    },
    getGroup() {
      return _.filter(this.users, function (o) {
        return o.group_id == o.id;
      });
    },

    exportXlsx() {
      const self = this;
      const obj = _.groupBy(self.lids, "status");
      const lidsByStatus = Array.from(Object.keys(obj), (k) => [
        `${k}`,
        obj[k],
      ]);

      var wb = XLSX.utils.book_new(); // make Workbook of Excel
      for (let i = 0; i < lidsByStatus.length; i++) {
        // export json to Worksheet of Excel
        // only array possible
        window["list" + i] = XLSX.utils.json_to_sheet(lidsByStatus[i][1]);
        // add Worksheet to Workbook
        // Workbook contains one or more worksheets
        XLSX.utils.book_append_sheet(
          wb,
          window["list" + i],
          lidsByStatus[i][0]
        ); // sheetAName is name of Worksheet
      }
      // export Excel file
      XLSX.writeFile(wb, "book.xlsx"); // name of the file is 'book.xlsx'
    },
    getDuplicates() {
      this.telsDuplicates = this.lids
        .filter(this.duplicatesOnly)
        .map((f) => f.id);
    },
    duplicatesOnly(v1, i1, self) {
      let ndx = self.findIndex(function (v2, i2) {
        // make sure not looking at the same object (using index to verify)
        // use JSON.stringify for object comparison
        return i1 != i2 && v1.tel == v2.tel;
      });
      return i1 != ndx && ndx != -1;
    },
    searchlids3() {
      let self = this;
      const data = {};
      self.loading = true;
      data.group_id = self.$props.user.group_id;
      data.role_id = self.$props.user.role_id;
      data.limit = self.limit;
      data.page = self.page;
      data.search = self.searchAll;
      data.office_id = self.filterOffices;
      axios
        .post("api/Lid/searchlids3", data)
        .then((res) => {
          self.hm = res.data.hm;
          self.lids = Object.entries(res.data.lids).map((e) => e[1]);
          self.lids.map(function (e) {
            e.user = self.users.find((u) => u.id == e.user_id).fio || "";
            e.date_created = e.created_at.substring(0, 10);
            if (e.updated_at) {
              e.date_updated = e.updated_at.substring(0, 10);
            }
            e.provider = self.e.provider_id;
            if (e.status_id)
              e.status = self.statuses.find((s) => s.id == e.status_id).name;
          });
          self.loading = false;
          self.disableuser = 0;
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    deleteItem() {
      const self = this;
      let ids = [];
      this.lids.forEach(function (el) {
        ids.push(el.id);
        self.lids.splice(
          self.lids.indexOf(self.lids.find((l) => l.id == el.id)),
          1
        );
      });

      axios
        .post("api/Lid/deletelids", ids)
        .then(function (response) {
          self.getUsers();
          // self.getLids(send.user_id);
          self.filterStatus = [];
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    clearLeads() {
      const self = this;
      let send = {};
      send.user_id = this.userid;

      if (this.selected.length > 0 && this.$refs.datatable.items.length > 0) {
        send.data = this.selected.map((el) => {
          return el.id;
        });
      } else {
        return;
      }
      axios
        .post("api/clearLeads", send)
        .then(function (response) {
          self.selected = [];
          self.getLids3();
          self.clearLeads();
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    putSelectedLidsDB() {
      const self = this;
      let send = {};
      send.user_id = this.userid;

      if (this.selectedStatus !== 0) {
        send.status_id = this.selectedStatus;
      }
      if (this.selected.length > 0 && this.$refs.datatable.items.length > 0) {
        send.data = this.selected;
      } else if (
        (this.search !== "" || this.filtertel !== "") &&
        this.$refs.datatable.$children[0].lids.length > 0
      ) {
        send.data = this.$refs.datatable.$children[0].lids;
        axios
          .post("api/Lid/newlids", send)
          .then(function (response) {
            // self.getUsers();
            self.search = "";
            self.filtertel = "";
          })
          .catch(function (error) {
            console.log(error);
          });
      }
      if (this.lids.length == 0) {
        this.files = [];
      }
      this.userid = null;
      this.$refs.radiogroup.lazyValue = null;
      this.getUsers();
    },
    usercolor(user) {
      return user.role_id == 2 ? "green" : "blue";
    },
    forceRerender() {
      this.componentKey += 1;
    },
    clickrow(item, row) {
      this.tel = item.tel;
      this.lid_id = item.id;
      if (!row.isExpanded) {
        this.expanded = [item];
      } else {
        this.expanded = [];
      }
      /* if (!row.isSelected) {
        this.tel = item.tel;
        this.lid_id = item.id;
        this.expanded = [item];
      } else this.tel = "";
       row.select(!row.isSelected); */
    },
    changeLidsUser() {
      const self = this;
      let send = {};
      send.data = [];
      send.user_id = this.userid;

      if (this.selectedStatus !== 0) {
        send.status_id = this.selectedStatus;
      }
      if (this.selected.length > 0 && this.$refs.datatable.items.length > 0) {
        send.data = this.selected;
      } else {
        this.userid = null;
        return false;
      }
      if (self.$props.user.role_id == 2) {
        //CallBack user not change
        send.data = send.data.filter((f) => f.status_id != 9);
      }
      axios
        .post("api/Lid/changelidsuser", send)
        .then(function (response) {
          self.search = "";
          self.$refs.radiogroup.lazyValue = null;
          self.selected = [];
          self.archSelected = [];
          // if (self.savedates == true) {
          //   self.disableuser = 0;
          // }

          self.getUsers();
          self.getLids3();
          // self.userid = null;
        })
        .catch(function (error) {
          console.log(error);
        });
      self.searchAll = "";
    },
    getUsers() {
      let self = this;
      // let get = self.$props.user.role_id == 1 ? "/api/users" : "/api/getusers";
      let get = "/api/getusers";
      axios
        .get(get)
        .then((res) => {
          self.users = res.data.map(
            ({
              name,
              id,
              role_id,
              fio,
              hmlids,
              group_id,
              order,
              statnew,
              pic,
              inp,
              cb,
              office_id,
            }) => ({
              name,
              id,
              role_id,
              fio,
              hmlids,
              pic,
              group_id,
              order,
              statnew,
              inp,
              cb,
              office_id,
            })
          );
          if (self.$props.user.role_id == 1 && self.filterOffices > 0) {
            self.users = self.users.filter(
              (f) => f.office_id == self.filterOffices
            );
          }
          if (self.$props.user.role_id != 1) {
            self.users = self.users.filter(
              (f) => f.group_id == self.$props.user.group_id
            );
          }
          self.group = self.getGroup();
        })
        .catch((error) => console.log(error));
    },
    getStatuses() {
      let self = this;
      axios
        .get("/api/statuses")
        .then((res) => {
          self.statuses = res.data.map(({ uname, name, id, color, order }) => ({
            uname,
            name,
            id,
            color,
            order,
          }));
          if (self.$props.user.role_id > 1) {
            self.filterstatuses = self.statuses.filter((e) => e.id != 8);
          } else {
            self.filterstatuses = [...self.statuses];
          }
          // self.statuses.unshift({ name: "Default", id: 0 });
        })
        .catch((error) => console.log(error));
    },
    getStatusLids(id) {
      let self = this;
      self.filterStatus = [];
      self.search = "";
      self.filtertel = "";
      const send = {};
      send.group_id = self.$props.user.group_id;
      send.role_id = self.$props.user.role_id;
      send.id = id;
      axios
        .post("/api/statuslids", send)
        .then((res) => {
          self.lids = Object.entries(res.data).map((e) => e[1]);
          self.lids.map(function (e) {
            e.date_created = e.created_at.substring(0, 10);
            if (e.updated_at) {
              e.date_updated = e.updated_at.substring(0, 10);
            }
            if (e.status_id)
              e.status = self.statuses.find((s) => s.id == e.status_id).name;
          });
          if (self.users.length) {
            e.user = self.users.find((u) => u.id == e.user_id).fio || "";
          }
          e.provider = self.getProviderName(e.provider_id);
          self.orderStatus();
          self.searchAll = "";
          if (localStorage.filterStatus) {
            self.filterStatus = localStorage.filterStatus
              .split()
              .map((el) => parseInt(el));
          }
        })
        .catch((error) => console.log(error));
    },
    getLocalDateTime(DateTime) {
      return new Date(
        new Date(DateTime).getTime() -
          new Date(DateTime).getTimezoneOffset() * 60 * 1000
      )
        .toJSON()
        .slice(0, 16)
        .replace("T", " ");
    },

    orderStatus() {
      const self = this;
      let stord = [];
      self.Statuses = [];
      stord = Object.entries(_.groupBy(self.lids, "status"));
      stord.map(function (i) {
        //i[0]//name
        //i[1]//array
        let el = self.statuses.find((s) => s.name == i[0]);
        self.Statuses.push({
          id: el.id,
          name: i[0],
          hm: i[1].length,
          order: el.order,
          color: el.color,
        });
      });
      self.Statuses = _.orderBy(self.Statuses, "order");
    },
    changeStatus() {
      const self = this;
      let send = {};
      if (this.selected.length && this.selectedStatus) {
        this.selected.map(function (e) {
          e.status_id = self.selectedStatus;
          e.status = self.statuses.find((s) => s.id == e.status_id).name;
        });
        send.data = this.selected.map((e) => e);
        this.changeLids(send);
      }
    },
    changeLids(send) {
      const self = this;
      axios
        .post("api/Lid/updatelids", send)
        .then(function (response) {
          self.afterUpdateLids();
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    afterUpdateLids() {
      const self = this;
      self.selected = [];
      self.selectedStatus = 0;
      // self.getLids(self.disableuser);
    },
    getProviders() {
      let self = this;
      axios
        .get("/api/provider")
        .then((res) => {
          self.providers = res.data.map(({ name, id }) => ({ name, id }));
          // self.providers.unshift({ name: "selection", id: 0 });
          // self.getLidsOnDate();
        })
        .catch((error) => console.log(error));
    },
  },
  components: {
    logtel,
  },
};
</script>

<style>
.scroll-y {
  max-height: 60vh;
  overflow: auto;
}

#tablids .v-data-table__wrapper {
  overflow: auto;
  max-height: 53vh;
}

#tablids .v-data-footer .v-data-footer__select,
#tablids .v-data-footer .v-data-footer__pagination,
#tablids .v-data-footer .v-data-footer__icons-before,
#tablids .v-data-footer .v-data-footer__icons-after {
  display: none;
}

.nn input {
  width: 3rem;
  border-bottom: 1px solid gray;
}
.v-application--is-ltr .v-data-footer__select {
  margin-top: -12px;
}

.wrp_group .row {
  gap: 0.7rem;
}
.wrp_group .img {
  height: 34px;
  width: 34px;
}
.v-input--is-label-active .img {
  border: 1px solid #2196f3;
  background: #fff;
  color: #2196f3;
}
.wrp__providers {
  display: flex;
  gap: 1rem;
}
.provider_wrp {
  display: flex;
  align-items: center;
  box-shadow: 0px 0px 9.5px 0.5px rgba(61, 95, 110, 0.2);
  border-radius: 30px;
  padding: 3px 5px;
}
#usersradiogroup .v-btn:not(.ml-3) {
  margin-left: 3px;
}
#usersradiogroup .v-btn {
  font-size: 1rem;
}
.v-btn::after {
  content: attr(data);
  position: absolute;
  left: 0px;
  font-weight: bold;
  z-index: 1;
  bottom: -4px;
  font-size: 0.7rem;
  box-shadow: none;
}
#usersradiogroup .v-btn[data="new"] {
  background: #e0e0e0;
}
.v-btn[data="new"]::after {
  color: #aaa;
}
#usersradiogroup .v-btn[data="inp"] {
  background: #b5d7898c;
}
.v-btn[data="inp"]::after {
  color: #4aaf5b;
}
#usersradiogroup .v-btn[data="cb"] {
  background: #9fc6f3;
}
.v-btn[data="cb"]::after {
  color: #7b80cc;
}

.v-btn__content {
  position: relative;
  z-index: 2;
  font-weight: bold;
}
.v-radio .v-label {
  font-weight: bold;
}
.searchradio {
  margin: 0;
  padding: 0;
  margin-bottom: -6px;
}
.searchradio .v-radio .v-label {
  font-size: 13px;
  font-weight: inherit;
}
</style>
