<template>
  <v-app id="inspire">
    <v-app-bar app class="">
      <v-bottom-navigation
        color="primary"
        background-color="transparent"
        :value="adminMenu"
        style="box-shadow: none"
      >
        <v-btn
          :value="item.name"
          v-for="(item, i) in items"
          :key="i"
          @click="adminMenu = item.name"
        >
          <span>{{ item.text }}</span>

          <v-img
            :src="item.icon"
            width="42px"
            height="40px"
            :alt="item.text"
          ></v-img>
        </v-btn>
      </v-bottom-navigation>

      <v-spacer></v-spacer>
      <v-btn v-if="$props.user.id == 1" @click="adminMenu = 'reportBTC'">
        {{ $props.user.fio }}
      </v-btn>

      <v-btn @click="$emit('login', {})">Logout</v-btn>
      <v-flex xs12 md2 ml-3>
        <v-switch v-model="themeDark" label="Dark" hide-details></v-switch
      ></v-flex>
    </v-app-bar>

    <v-main class="">
      <!-- grey lighten-2 -->
      <v-container fluid>
        <!-- <v-row> -->
        <!-- table -->
        <component :user="$props.user" :is="adminComponent" />
        <!-- <tablenewlid></tablenewlid> -->
        <!-- </v-row> -->
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
const users = () => import("./users");
const importcsv = () => import("./importcsv");
const statusLid = () => import("./statusLid");
// const workPlaces = () => import("./workPlaces");
// const providers = () => import("./providers");
const mlids = () => import("../manager/mlids");

// const calls = () => import("../crmanager/calls");
const lids3 = () => import("../crmanager/lids3");
// const report = () => import("./report");
// const reportPie = () => import("./reportPie");
const reportBTC = () => import("./reportBTC");
const reportCalDep = () => import("./reportCalDep");

export default {
  props: ["user"],
  data: () => ({
    drawer: null,
    selectedItem: 0,

    items: [
      {
        text: "Import Exel/Csv",
        name: "importcsv",
        icon: "/img/ico/exel.png",
      },
      { text: "Users", name: "users", icon: "/img/ico/user.png" },
      {
        text: "Status leads",
        name: "statusLid",
        icon: "/img/ico/status.png",
      },
      //   {
      //     text: "Providers",
      //     name: "providers",
      //     icon: "/img/ico/",
      //   },

      {
        text: "Distribution",
        name: "lids3",
        icon: "/img/ico/distribution.png",
      },
      //{ text: "Calls", name: "calls", icon: "mdi-headset-dock" },
      { text: "Management", name: "mlids", icon: "/img/ico/management.png" },
      //{ text: "Report", name: "report", icon: "mdi-receipt" },
      //{ text: "Reports", name: "reportPie", icon: "mdi-timetable" },
      //   {
      //     text: "Report BTC",
      //     name: "reportBTC",
      //     icon: "/img/ico/reprortsbtc.png",
      //   },
      {
        text: "Report CalDep",
        name: "reportCalDep",
        icon: "/img/ico/reprortsbtc.png",
      },
    ],
    adminMenu: "",
    themeDark: false,
  }),
  computed: {
    adminComponent() {
      if (this.adminMenu == "importcsv") return importcsv;
      if (this.adminMenu == "users") return users;
      if (this.adminMenu == "statusLid") return statusLid;
      // if (this.adminMenu == "workPlaces") return workPlaces;
      //   if (this.adminMenu == "providers") return providers;
      if (this.adminMenu == "mlids") return mlids;
      if (this.adminMenu == "lids3") return lids3;
      //   if (this.adminMenu == "calls") return calls;
      //   if (this.adminMenu == "report") return report;
      //   if (this.adminMenu == "reportPie") return reportPie;
      if (this.adminMenu == "reportBTC") return reportBTC;
      if (this.adminMenu == "reportCalDep") return reportCalDep;
    },
  },
  mounted: function () {
    if (localStorage.themeDark) {
      if (localStorage.themeDark == "true") {
        this.$vuetify.theme.dark = true;
        this.themeDark = true;
      } else {
        this.$vuetify.theme.dark = false;
        this.themeDark = false;
      }
    }
    if (localStorage.adminMenu) {
      this.adminMenu = localStorage.adminMenu;
      this.selectedItem = this.items
        .map((i) => i.name)
        .indexOf(localStorage.adminMenu);
    }
  },
  watch: {
    adminMenu(newName) {
      localStorage.adminMenu = newName;
    },
    themeDark(newName) {
      localStorage.themeDark = newName;
      this.$vuetify.theme.dark = newName;
    },
  },
  methods: {},
};
</script>

<style>
</style>
