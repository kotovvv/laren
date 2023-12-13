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
    <v-tabs v-model="tab" background-color="primary" dark>
      <v-tab> Provider </v-tab>
      <v-tab> XLSX </v-tab>
      <v-tab v-if="$attrs.user.role_id == 1 && $attrs.user.group_id == 0">
        ВТС
      </v-tab>
      <v-tab>CHECK DUBLIKATE MAIL</v-tab>
    </v-tabs>
    <v-tabs-items v-model="tab">
      <v-tab-item>
        <v-row>
          <v-col cols="2">
            <v-select
              v-model="selectedProvider"
              :items="providers"
              label="Provider"
              item-text="name"
              item-value="id"
            ></v-select>
          </v-col>
          <v-col cols="3" v-if="selectedProvider">
            <v-file-input
              v-model="files"
              ref="fileupload"
              label="Upload CSV"
              show-size
              truncate-length="24"
              @change="onFileChange"
            ></v-file-input>
          </v-col>
          <v-col cols="3" v-if="parse_csv.length">
            <v-select
              v-model="selectedStatus"
              :items="statuses"
              label="Status"
              item-text="name"
              item-value="id"
            ></v-select>
          </v-col>
          <v-col cols="4" v-if="parse_csv.length">
            <v-textarea v-model="message" label="Message" rows="1"></v-textarea>
          </v-col>
        </v-row>

        <v-main v-if="parse_csv.length && files">
          <v-row>
            <v-col cols="8">
              <v-card>
                <v-container>
                  <v-row>
                    <v-col cols="4">
                      <v-card-title>
                        <v-text-field
                          v-model="search"
                          append-icon="mdi-magnify"
                          label="Search"
                          single-line
                          hide-details
                        ></v-text-field>
                      </v-card-title>
                    </v-col>
                    <v-col cols="3">
                      <v-card-title>
                        <v-text-field
                          v-model.lazy.trim="filtertel"
                          append-icon="mdi-phone"
                          label="Initial digits"
                          single-line
                          hide-details
                        ></v-text-field>
                      </v-card-title>
                    </v-col>
                  </v-row>
                </v-container>
                <v-data-table
                  v-model.lazy.trim="selected"
                  :headers="headers"
                  :search="search"
                  :single-select="false"
                  item-key="tel+afilyator"
                  show-select
                  :items="filteredItems"
                  ref="datatable"
                  :loading="loading"
                ></v-data-table>
              </v-card>
            </v-col>
            <v-col cols="4">
              <v-card height="100%" class="pa-5">
                Specify the user for leads
                <div class="pa-5 w-100 border wrp_users">
                  <div class="my-3">User Search</div>
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
                  ></v-autocomplete>

                  <div class="scroll-y">
                    <v-list>
                      <v-radio-group
                        id="usersradiogroup"
                        ref="radiogroup"
                        v-model="userid"
                        v-bind="users"
                        @change="putSelectedLidsDB"
                      >
                        <v-expansion-panels ref="akk" v-model="akkvalue">
                          <v-expansion-panel
                            v-for="(item, i) in group"
                            :key="i"
                          >
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
                                <v-btn
                                  data="new"
                                  v-if="user.statnew"
                                  label
                                  small
                                >
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
              </v-card>
            </v-col>
          </v-row>
        </v-main>
        <v-row v-else>
          <v-col cols="12" v-if="leads.length">
            <v-row>
              <v-col>
                <div class="wrp__statuses" id="wrp_stat">
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
                    </div>
                  </template>
                </div>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12">
            <v-progress-linear
              :active="loading"
              indeterminate
              color="purple"
            ></v-progress-linear>
          </v-col>
          <v-col cols="12">
            <v-data-table
              :headers="import_headers"
              item-key="id"
              :items="imports"
              ref="importtable"
              @click:row="clickrow"
            >
              <template v-slot:item.id="{ item }"> </template>
            </v-data-table>
          </v-col>
          <v-col cols="12" v-if="leads.length">
            <div class="border pa-4">
              <v-data-table
                id="tablids"
                :headers="headers_leads"
                item-key="id"
                :items="leads"
                :footer-props="{
                  'items-per-page-options': [],
                  'items-per-page-text': '',
                }"
                :disable-items-per-page="true"
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
                    <!-- <v-spacer></v-spacer> -->
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
              </v-data-table>
            </div>
          </v-col>
        </v-row>
      </v-tab-item>
      <v-tab-item>
        <importxlsx :user="$attrs.user"></importxlsx>
      </v-tab-item>
      <v-tab-item v-if="$attrs.user.role_id == 1 && $attrs.user.group_id == 0">
        <importBTC></importBTC>
      </v-tab-item>
      <v-tab-item>
        <v-container>
          <v-row>
            <v-col>
              <v-radio-group v-model="email_tel" row>
                <v-radio label="email" value="email"></v-radio>
                <v-radio label="telephone" value="tel"></v-radio>
              </v-radio-group>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="6">
              <v-textarea
                outline
                class="pa-3"
                v-model="list_email"
                label="Email addresses or telephone numbers "
              ></v-textarea>
            </v-col>
            <v-col cols="6">
              <v-file-input
                v-model="file_emails"
                label="upload txt"
                show-size
                truncate-length="24"
                @change="onFileChange"
              ></v-file-input>
              <v-btn @click="checkEmails" v-if="list_email" class="primary"
                >Check it out<v-progress-circular
                  v-if="loading"
                  indeterminate
                  color="amber"
                ></v-progress-circular
              ></v-btn>
            </v-col>
            <v-col cols="12" v-if="duplicate_leads.length">
              <v-btn outlined @click="exportXlsx" class="border">
                <v-icon left> mdi-file-excel </v-icon>
                Download the table
              </v-btn>
              <v-data-table
                :headers="duplicate_leads_headers"
                item-key="id"
                :items="duplicate_leads"
                ref="duplicatetable"
              >
              </v-data-table>
            </v-col>
          </v-row>
        </v-container>
      </v-tab-item>
    </v-tabs-items>
  </div>
</template>

<script>
import XLSX from "xlsx";
import axios from "axios";
import importBTC from "./importBTC";
import importxlsx from "./importxlsx";
import _ from "lodash";
export default {
  data: () => ({
    list_email: "",
    file_emails: [],
    in_db: [],
    out_db: [],
    message: "",
    snackbar: false,
    loading: false,
    userid: null,
    users: [],
    selectedUser: {},
    group: null,
    providers: [],
    statuses: [],
    imports: [],
    selectedStatus: 8,
    selectedProvider: 0,
    related_user: [],
    selected: [],
    files: [],
    search: "",
    filtertel: "",
    headers_leads: [
      { text: "Name", value: "name" },
      { text: "Email", value: "email" },
      { text: "Phone.", align: "start", value: "tel" },
      { text: "Status", value: "status" },
    ],
    headers: [
      { text: "Name", value: "name" },
      { text: "Email", value: "email" },
      { text: "Phone.", align: "start", value: "tel" },
      { text: "Afilator", value: "afilyator" },
    ],
    import_headers: [
      { text: "", value: "id" },
      { text: "Date time start", value: "start" },
      { text: "Date time end", value: "end" },
      { text: "Supplier", value: "provider" },
      { text: "Who imported", value: "user" },
      { text: "Comment", value: "message" },
    ],
    duplicate_leads_headers: [
      { text: "Afilator", value: "afilyator" },
      { text: "Емаил", value: "email" },
      { text: "Тфьу", value: "name" },
      { text: "Office", value: "office_name" },
      { text: "Provider", value: "provider_name" },
      { text: "Status", value: "status_name" },
      { text: "Phone", value: "tel" },
      { text: "Operator", value: "user_name" },
      { text: "Established", value: "created" },
    ],
    duplicate_leads: [],
    parse_header: [],
    parse_csv: [],
    sortOrders: {},
    sortKey: "tel",
    tab: 0,
    Statuses: [],
    leads: [],
    email_tel: "email",
  }),
  watch: {
    selectedUser(user) {
      if (user == {}) {
        this.userid = null;
        return;
      }
      //this.disableuser = user.id;
      this.akkvalue = _.findIndex(this.group, { group_id: user.group_id });
    },
    selectedProvider: function (newval) {
      this.users = [];
      this.related_user = [];
      let rel_user = this.providers.find(
        (p) => p.id == newval
      ).related_users_id;
      if (rel_user.length > 2) {
        this.related_user = JSON.parse(rel_user);
        this.getUsers();
      }
    },
  },
  mounted() {
    this.getImports();
    this.getProviders();
    // this.getUsers();
    this.getStatuses();
  },
  computed: {
    filteredItems() {
      let reg = new RegExp("^" + this.filtertel);
      return this.parse_csv.filter((i) => {
        return !this.filtertel || reg.test(i.tel);
      });
    },
  },
  methods: {
    getGroup() {
      return _.filter(this.users, function (o) {
        return o.group_id == o.id;
      });
    },
    exportXlsx() {
      const self = this;
      const obj = _.groupBy(self.filteredItems, "status");
      const lidsByStatus = Array.from(Object.keys(obj), (k) => [
        `${k}`,
        obj[k],
      ]);

      var wb = XLSX.utils.book_new(); // make Workbook of Excel
      window["list"] = XLSX.utils.json_to_sheet(self.duplicate_leads);
      XLSX.utils.book_append_sheet(wb, window["list"], "duplicate_emailes");
      const unique = self.out_db.map((i) => ({ email: i }));
      window["unique"] = XLSX.utils.json_to_sheet(unique);
      XLSX.utils.book_append_sheet(wb, window["unique"], "unique");

      // export Excel file
      XLSX.writeFile(
        wb,
        "dupl_" + self.email_tel + new Date().toDateString() + ".xlsx"
      ); // name of the file is 'book.xlsx'
    },
    checkEmails() {
      let vm = this;
      vm.loading = true;
      vm.snackbar = false;
      vm.message = "";
      vm.duplicate_leads = [];
      vm.in_db = [];
      vm.out_db = [];
      let data = {};
      data.emails = vm.list_email
        .replace(/[\r]/gm, "")
        .replaceAll(" ", "")
        .split("\n");
      data.check = 1;
      data.email_tel = vm.email_tel;
      axios
        .post("api/checkEmails", data)
        .then(function (res) {
          vm.in_db = res.data.emails.filter((n) => n);

          vm.out_db = [
            ...new Set(data.emails.filter((i) => !vm.in_db.includes(i))),
          ];
          vm.message =
            "Unique: " +
            vm.out_db.length +
            "<br>Duplicates: " +
            vm.in_db.length;
          vm.snackbar = true;
          vm.duplicate_leads = res.data.leads;

          vm.loading = false;
          vm.list_email = "";
          vm.files = [];
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    filterStatuses() {
      const self = this;
      self.Statuses = [];
      let stord = this.leads;
      stord = Object.entries(_.groupBy(stord, "status"));
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
    usercolor(user) {
      return user.role_id == 2 ? "green" : "blue";
    },
    putSelectedLidsDB() {
      let start = new Date().toJSON().slice(0, 19).replace("T", " ");
      let self = this;
      self.loading = true;
      let send = {};
      send.user_id = this.userid;
      send.provider_id = this.selectedProvider;
      if (this.selectedStatus !== 0) {
        send.status_id = this.selectedStatus;
      }
      if (this.$refs.datatable.items.length > 0) {
        if (this.selected.legth) send.data = this.selected;
        else send.data = this.$refs.datatable.items;
        axios
          .post("api/Lid/newlids", send)
          .then(function (response) {
            // console.log(response);
            if (self.selected.length) {
              self.parse_csv = self.parse_csv.filter(
                (ar) => !self.selected.find((rm) => rm.tel === ar.tel)
              );
            } else {
              self.parse_csv = [];
            }
            self.selected = [];
            self.getUsers();
            self.loading = false;
            self.files = [];
            // save to imports db
            //======================
            let info = {};

            info.start = start;
            info.end = new Date().toJSON().slice(0, 19).replace("T", " ");
            info.provider_id = self.selectedProvider;
            info.user_id = self.$attrs.user.id;
            info.message = self.message;

            axios
              .post("api/imports", info)
              .then(function (response) {})
              .catch(function (error) {
                console.log(error);
              });
          })
          .catch(function (error) {
            console.log(error);
          });
      } else if (
        (this.search !== "" || this.filtertel !== "") &&
        this.$refs.datatable.$children[0].filteredItems.length > 0
      ) {
        send.data = this.$refs.datatable.$children[0].filteredItems;
        axios
          .post("api/Lid/newlids", send)
          .then(function (response) {
            self.parse_csv = self.parse_csv.filter(
              (ar) =>
                !self.$refs.datatable.$children[0].filteredItems.find(
                  (rm) => rm.tel === ar.tel
                )
            );
            self.getUsers();
            self.search = "";
            self.filtertel = "";
            self.loading = false;
            // save to imports db
            //======================
            let info = {};

            info.start = response.data.date_start;
            info.end = response.data.date_end;
            info.provider_id = self.selectedProvider;
            info.user_id = self.$attrs.user.id;
            info.message = self.message;

            axios
              .post("api/imports", info)
              .then(function (response) {
                // console.log(response);
              })
              .catch(function (error) {
                console.log(error);
              });
          })
          .catch(function (error) {
            console.log(error);
          });
      }
      if (this.parse_csv.length == 0) {
        this.files = [];
        this.selectedProvider = "";
      }
      this.userid = null;
      this.$refs.radiogroup.lazyValue = null;
      this.getUsers();
    },
    clickrow(item) {
      console.log(item);
      let self = this;
      let data = {};
      self.loading = true;
      data.provider_id = item.provider_id;
      data.start = item.start;
      data.end = item.end;

      axios
        .post("api/getlidsImportedProvider", data)
        .then(function (response) {
          self.leads = response.data;
          self.leads.map(function (e) {
            e.date_created = e.created_at.substring(0, 10);
            if (e.status_id)
              e.status = self.statuses.find((s) => s.id == e.status_id).name;
          });
          self.filterStatuses();
          self.loading = false;
          const el = document.getElementById("wrp_stat");
          el.scrollIntoView({ behavior: "smooth" });
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    getProviders() {
      let self = this;
      axios
        .get("/api/provider")
        .then((res) => {
          self.providers = res.data.map(({ name, id, related_users_id }) => ({
            name,
            id,
            related_users_id,
          }));
        })
        .catch((error) => console.log(error));
    },
    getImports() {
      let self = this;
      axios
        .get("/api/imports")
        .then((res) => {
          self.imports = res.data.map(
            ({ id, start, end, provider, provider_id, user, message }) => ({
              id,
              start,
              end,
              provider,
              provider_id,
              user,
              message,
            })
          );
        })
        .catch((error) => console.log(error));
    },
    getUsers() {
      let self = this;

      axios
        .post("/api/getusers", self.related_user)
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
          if (self.$attrs.user.role_id == 1 && self.filterOffices > 0) {
            self.users = self.users.filter(
              (f) => f.office_id == self.filterOffices
            );
          }
          if (self.$attrs.user.role_id != 1) {
            self.users = self.users.filter(
              (f) => f.group_id == self.$attrs.user.group_id
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
        })
        .catch((error) => console.log(error));
    },
    onFileChange(f) {
      if (f == null) return;
      const ftype = [
        "text/comma-separated-values",
        "text/csv",
        "application/csv",
        "application/excel",
        "application/vnd.ms-excel",
        "application/vnd.msexcel",
        "text/anytext",
        "text/plain",
      ];
      if (ftype.indexOf(f.type) >= 0) {
        this.createInput(f);
      } else {
        this.files = [];
      }
    },
    txt2Array(txt) {
      let vm = this;
      vm.list_email = txt;
    },
    csvJSON(csv) {
      var vm = this;
      var lines = csv.split("\n");
      var result = [];
      var headers = lines[0].split(";");
      headers = ["name", "email", "tel", "afilyator"];
      // vm.parse_header = lines[0].split(",");
      // lines[0].split(",").forEach(function (key) {
      //   vm.sortOrders[key] = 1;
      // });

      lines.map(function (line, indexLine) {
        if (indexLine < 1) return; // Jump header line

        var obj = {};
        line = line.trim();
        var currentline = line.split(";");

        headers.map(function (header, indexHeader) {
          obj[header] = currentline[indexHeader];
        });

        result.push(obj);
      });

      result.pop(); // remove the last item because undefined values

      return result; // JavaScript object
    },
    createInput(file) {
      let promise = new Promise((resolve, reject) => {
        var reader = new FileReader();
        var vm = this;
        reader.onload = (e) => {
          resolve((vm.fileinput = reader.result));
        };
        reader.readAsText(file);
      });

      promise.then(
        (result) => {
          let vm = this;
          console.log(vm.tab);
          if (vm.tab == 2) {
            vm.parse_txt_emails = vm.txt2Array(vm.fileinput);
          } else {
            vm.parse_csv = vm
              .csvJSON(this.fileinput)
              .filter(
                (v, i, a) =>
                  a.findIndex(
                    (t) => t.afilyator + t.tel == v.afilyator + v.tel
                  ) === i
              );
          }
        },
        (error) => {
          /* handle an error */
          console.log(error);
        }
      );
    },
  },
  components: {
    importBTC,
    importxlsx,
  },
};
</script>

<style>
#usersradiogroup .img,
.wrp_group .img {
  height: 60px;
  width: 60px;
  background: #2196f3;
  border-radius: 16px;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #fff;
  font-weight: bold;
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
</style>
