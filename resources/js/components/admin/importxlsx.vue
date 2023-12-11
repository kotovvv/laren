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
            label="load Excel"
            show-size
            truncate-length="24"
            @change="onFileChange"
          ></v-file-input>
        </v-col>
        <v-col cols="3">
          <v-select
            v-model="selectedStatus"
            :items="statuses"
            label="Status"
            item-text="name"
            item-value="id"
          ></v-select>
        </v-col>
        <v-col cols="4">
          <v-textarea v-model="message" label="message" rows="1"></v-textarea>
        </v-col>
      </v-row>
      <v-progress-linear
        :active="loading"
        indeterminate
        color="purple"
      ></v-progress-linear>
      <v-row v-if="table.length && files">
        <v-col cols="9">
          <v-simple-table id="loadedTable">
            <template v-slot:default>
              <thead>
                <tr>
                  <th v-for="el in table[0].length" :key="el">
                    <v-select
                      :items="[
                        '',
                        'name',
                        'lastname',
                        'email',
                        'TelCode',
                        'tel',
                        'afilyator',
                        'text',
                      ]"
                      outlined
                      @change="makeHeader"
                    >
                    </v-select>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, ix) in table" :key="ix">
                  <td v-for="(it, i) in item" :key="i">{{ it }}</td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-col>
        <v-col cols="3" v-if="header.length > 2">
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
                rounded
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
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import XLSX from "xlsx";
import axios from "axios";

export default {
  props: ["user"],
  data: () => ({
    loading: false,
    message: "",
    snackbar: false,
    providers: [],
    selectedProvider: 0,
    users: [],
    statuses: [],
    selectedStatus: 0,
    files: null,
    table: [],
    header: [],
    userid: null,
    related_user: [],
    tab: 0,
    akkvalue: [],
    group: null,
    selectedUser: {},
    disableuser: null,
  }),

  mounted() {
    this.getProviders();
    this.getStatuses();
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
  methods: {
    getheader() {
      setTimeout(() => {
        this.header = document
          .querySelector("#loadedTable table")
          .tHead.innerText.split("\t")
          .map((i) => i.replaceAll(/[\W_]+/g, ""));
      }, 300);
    },
    makeHeader() {
      this.getheader();
    },
    onFileChange(f) {
      if (f == null) return;
      const ftype = [
        "application/vnd.ms-excel",
        "application/vnd-xls",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "application/xls",
        "application/x-xls",
        "application/vnd.ms-excel",
        "application/msexcel",
        "application/x-msexcel",
        "application/x-ms-excel",
        "application/x-excel",
        "application/x-dos_ms_excel",
        "application/excel",
      ];
      if (ftype.indexOf(f.type) >= 0) {
        this.createInput(f);
      }
    },
    createInput(f) {
      let vm = this;
      var reader = new FileReader();
      reader.onload = function (e) {
        var data = e.target.result,
          fixedData = vm.fixdata(data),
          workbook = XLSX.read(btoa(fixedData), { type: "base64" }),
          firstSheetName = workbook.SheetNames[0],
          worksheet = workbook.Sheets[firstSheetName];
        vm.table = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
      };
      reader.readAsArrayBuffer(f);
    },
    fixdata(data) {
      var o = "",
        l = 0,
        w = 10240;
      for (; l < data.byteLength / w; ++l)
        o += String.fromCharCode.apply(
          null,
          new Uint8Array(data.slice(l * w, l * w + w))
        );
      o += String.fromCharCode.apply(null, new Uint8Array(data.slice(l * w)));
      return o;
    },
    putSelectedLidsDB() {
      this.loading = true;

      let json = {};
      //make json from header and body
      json = this.table.map((_, row) =>
        this.header.reduce(
          (json, key, col) => ({
            ...json,
            [key]: this.table[row][col] ?? "",
          }),
          {}
        )
      );
      //remove empty columns
      json = Object.entries(json).map(
        (e) =>
          (e[1] = Object.fromEntries(
            Object.entries(e[1]).filter((el) => el[0])
          ))
      );

      let start = new Date().toJSON().slice(0, 19).replace("T", " ");
      let self = this;
      self.loading = true;
      let send = {};
      send.user_id = this.userid;
      send.provider_id = this.selectedProvider;
      if (this.selectedStatus !== 0) {
        send.status_id = this.selectedStatus;
      }
      send.data = json;
      axios
        .post("api/Lid/newlids", send)
        .then(function (response) {
          self.getUsers();
          self.loading = false;
          self.files = [];
          self.table = [];
        })
        .catch(function (error) {
          console.log(error);
        });
      // save to imports db
      //======================
      let info = {};

      info.start = start;
      info.end = new Date().toJSON().slice(0, 19).replace("T", " ");
      info.provider_id = self.selectedProvider;
      info.user_id = self.$props.user.id;
      info.message = self.message;

      axios
        .post("api/imports", info)
        .then(function (response) {})
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
    getGroup() {
      return _.filter(this.users, function (o) {
        return o.group_id == o.id;
      });
    },
    usercolor(user) {
      return user.role_id == 2 ? "green" : "blue";
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
  },
};
</script>
