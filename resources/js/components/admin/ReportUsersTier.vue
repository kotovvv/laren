<template>
  <div>
    <v-container fluid>
      <v-row>
        <v-col cols="9" class="mt-7">
          <v-data-table
            v-model.lazy.trim="selected"
            id="tablids"
            :headers="headers"
            :single-select="false"
            item-key="id"
            show-select
            :items="lids"
            ref="datatable"
            :loading="loading"
            loading-text="Uploading... Stand by."
            class="elevation-1"
          >
          </v-data-table>
        </v-col>

        <v-col cols="3">
          <div class="w-100 wrp_users">
            <div class="scroll-y">
              <v-list>
                <v-radio-group
                  id="usersradiogroup"
                  ref="radiogroup"
                  v-model="userid"
                  v-bind="users"
                  @change="
                    selected.length > 0
                      ? changeUserTiers()
                      : changeUserAllLids()
                  "
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
                              getTiersUser();
                            "
                            :value="user.hm"
                            :disabled="disableuser == user.id"
                            >{{ user.hm }}</v-btn
                          >
                          <v-btn
                            disabled
                            data="new"
                            v-if="user.hm_docs"
                            label
                            small
                          >
                            {{ user.hm_docs }}
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
    </v-container>
    <ConfirmDlg ref="confirm" />
  </div>
</template>

<script>
import axios from "axios";
import _ from "lodash";
export default {
  name: "ReportUsersTier",
  components: { ConfirmDlg: () => import("./ConfirmDlg") },
  data() {
    return {
      users: [],
      userid: null,
      selectedUser: {},
      disableuser: 0,
      akkvalue: null,
      group: null,
      filterGroups: [],
      lids: [],
      selected: [],
      headers: [
        { text: "Name", value: "name" },
        { text: "Email", value: "email" },
        { text: "Tel.", align: "start", value: "tel" },
        { text: "Afilator", value: "afilyator" },
        // { text: "Supplier", value: "provider" },
        { text: "Manager", value: "user" },
        { text: "Created", value: "date_created" },
        { text: "Changed", value: "date_updated" },
        { text: "Status", value: "status" },
        // { text: "Depozit", value: "depozit" },
        { text: "Message", value: "text" },
        // { text: "Calls", value: "qtytel" },
        // { text: "CALLING.", value: "ontime" },
      ],
      page: 0,
      limit: 100,
      loading: false,
    };
  },

  mounted() {
    this.getUsersTier();
  },
  watch: {
    selectedUser(user) {
      if (user == {}) {
        this.userid = null;
        return;
      }
      this.akkvalue = _.findIndex(this.group, { group_id: user.group_id });
    },
  },
  methods: {
    async changeUserAllLids() {
      if (
        await this.$refs.confirm.open(
          "Confirm",
          "You definitely want to change the user of all records?"
        )
      ) {
        this.selected = this.lids;
        this.changeUserTiers();
      }
    },

    changeUserTiers() {
      const self = this;
      let data = {};

      self.loading = true;
      data.user_id = self.userid;
      data.lids = self.selected.map(({ id, status_id }) => ({ id, status_id }));
      data.message = "Change user";
      axios
        .post("/api/changeUserTiers", data)
        .then((res) => {
          self.getTiersUser();
          self.getUsersTier();
          self.loading = false;
          self.selected = [];
        })
        .catch((error) => console.log(error));
    },
    getTiersUser() {
      const self = this;
      axios
        .get("/api/getTiersUser/" + self.disableuser)
        .then((res) => {
          self.lids = res.data;
          self.lids = self.lids.map((l) => {
            l.user = self.users.find((u) => u.id == l.user_id).fio;
            l.date_created = l.created_at.substring(0, 10);
            if (l.updated_at) {
              l.date_updated = l.updated_at.substring(0, 10);
            }
            return l;
          });
        })
        .catch((error) => console.log(error));
    },
    getUsersTier() {
      const self = this;
      axios
        .get("/api/getUsersTier")
        .then((res) => {
          self.users = res.data.users;
          self.group = res.data.group;
        })
        .catch((error) => console.log(error));
    },
    usercolor(user) {
      return user.role_id == 2 ? "green" : "blue";
    },
  },
};
</script>
