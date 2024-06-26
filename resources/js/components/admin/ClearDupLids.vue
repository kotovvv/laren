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
                    first-day-of-week="1"
                    @input="
                      dateFrom = false;
                      getProvUserDate();
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
                      v-model="datetimeTo"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      label="To"
                    >
                    </v-text-field>
                  </template>
                  <v-date-picker
                    locale="en"
                    v-model="datetimeTo"
                    first-day-of-week="1"
                    @input="
                      dateTo = false;
                      getProvUserDate();
                    "
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
                    >
                    </v-text-field>
                  </template>
                  <v-date-picker
                    locale="en"
                    v-model="datetimeUpdated"
                    first-day-of-week="1"
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
            label="Suppliers"
            item-text="name"
            item-value="id"
            outlined
            multiple
            clearable
          >
            <template v-slot:selection="{ item, index }">
              <span v-if="index === 0">{{ item.name }} </span>
              <span v-if="index === 1" class="grey--text text-caption">
                (+{{ selectedProvider.length - 1 }} )
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
        <v-col cols="2">
          <v-autocomplete
            v-model="checkUser"
            :items="checkusers"
            label="check Users"
            item-text="name"
            item-value="id"
            outlined
            clearable
            multiple
          >
            <template v-slot:selection="{ item, index }">
              <span v-if="index === 0">{{ item.name }} </span>
              <span v-if="index === 1" class="grey--text text-caption">
                (+{{ checkUser.length - 1 }} )
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
          </v-autocomplete>
        </v-col>
        <v-col cols="2">
          <v-autocomplete
            v-model="selectedUser"
            :items="users"
            label="set on User"
            item-text="name"
            item-value="id"
            outlined
            clearable
          ></v-autocomplete>
        </v-col>
        <v-col>
          <v-btn class="btn primary" x-large @click="clearDupLids()"
            >Check duplicates</v-btn
          ></v-col
        >
      </v-row>
    </v-container>
    <v-progress-linear
      :active="loading"
      indeterminate
      color="blue"
    ></v-progress-linear>
    <v-container>
      <v-row>
        <v-col cols="12" class="my-3">
          Liads on period ({{ all }})
          <v-progress-linear
            :value="
              Math.ceil((all / Math.max(found, all)) * 100)
                ? Math.ceil((all / Math.max(found, all)) * 100)
                : 0
            "
            color="lime"
            height="25"
          >
            <strong
              >{{
                Math.ceil((all / Math.max(found, all)) * 100)
                  ? Math.ceil((all / Math.max(found, all)) * 100)
                  : 0
              }}%</strong
            >
          </v-progress-linear>
        </v-col>
        <v-col cols="12" class="my-3">
          Liads was found ({{ found }})
          <v-progress-linear
            :value="
              Math.ceil((found / Math.max(found, all)) * 100)
                ? Math.ceil((found / Math.max(found, all)) * 100)
                : 0
            "
            color="yellow"
            height="25"
          >
            <strong
              >{{
                Math.ceil((found / Math.max(found, all)) * 100)
                  ? Math.ceil((found / Math.max(found, all)) * 100)
                  : 0
              }}%</strong
            >
          </v-progress-linear>
        </v-col>
        <v-col cols="12" class="my-3">
          New ({{ newl }})
          <v-progress-linear
            :value="
              Math.ceil((newl / Math.max(found, all)) * 100)
                ? Math.ceil((newl / Math.max(found, all)) * 100)
                : 0
            "
            color="cyan"
            height="25"
          >
            <strong
              >{{
                Math.ceil((newl / Math.max(found, all)) * 100)
                  ? Math.ceil((newl / Math.max(found, all)) * 100)
                  : 0
              }}%</strong
            >
          </v-progress-linear>
        </v-col>
        <v-col cols="12" class="my-3">
          Duplicate ({{ dupl }})
          <v-progress-linear
            :value="
              Math.ceil((dupl / Math.max(found, all)) * 100)
                ? Math.ceil((dupl / Math.max(found, all)) * 100)
                : 0
            "
            color="pink"
            height="25"
          >
            <strong
              >{{
                Math.ceil((dupl / Math.max(found, all)) * 100)
                  ? Math.ceil((dupl / Math.max(found, all)) * 100)
                  : 0
              }}%</strong
            >
          </v-progress-linear>
        </v-col>
        <v-col cols="12" class="my-3" v-if="give">
          Transferred to user ({{ give }})
          <v-progress-linear
            :value="
              Math.ceil((give / Math.max(found, all)) * 100)
                ? Math.ceil((give / Math.max(found, all)) * 100)
                : 0
            "
            color="blue-grey"
            height="25"
          >
            <strong
              >{{
                Math.ceil((give / Math.max(found, all)) * 100)
                  ? Math.ceil((give / Math.max(found, all)) * 100)
                  : 0
              }}%</strong
            >
          </v-progress-linear>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      loading: false,
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
      selectedUser: 0,
      checkUser: [],
      checkusers: [],
      providers: [],
      selectedProvider: [],
      all: 0,
      found: 0,
      dupl: 0,
      newl: 0,
      give: 0,
    };
  },
  mounted() {
    this.getProvUserDate();
  },
  methods: {
    clearDupLids() {
      const self = this;
      let data = {};
      self.all = 0;
      self.found = 0;
      self.dupl = 0;
      self.newl = 0;
      self.give = 0;
      self.loading = true;
      data.from = self.datetimeFrom;
      data.to = self.datetimeTo;
      data.updated = self.datetimeUpdated;
      data.providers = self.selectedProvider;
      data.user_id = self.selectedUser;
      data.check_users = self.checkUser;
      axios
        .post("api/clearDupLids", data)
        .then(function (response) {
          self.all = response.data.all;
          self.found = response.data.found;
          self.dupl = response.data.dupl;
          self.newl = response.data.newl;
          self.give = response.data.give;
          self.loading = false;
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    getProvUserDate() {
      const self = this;
      let data = {};
      self.loading = true;
      data.from = self.datetimeFrom;
      data.to = self.datetimeTo;

      //   data.providers = self.selectedProvider;
      //   data.user_id = self.selectedUser;
      axios
        .post("api/getProvUserDate", data)
        .then(function (response) {
          console.log(response);
          self.users = response.data.users;
          self.checkusers = response.data.checkusers;
          self.providers = response.data.providers;
          self.loading = false;
        })
        .catch(function (error) {
          console.log(error);
        });
    },
  },
};
</script>
