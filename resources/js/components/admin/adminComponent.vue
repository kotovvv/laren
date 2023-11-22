<template>
  <v-app id="inspire">
    <v-app-bar app>
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

          <v-icon>{{ item.icon }}</v-icon>
        </v-btn>
      </v-bottom-navigation>

      <v-spacer></v-spacer>
      <div class="align-center mr-2">{{ $props.user.fio }}</div>

      <v-btn @click="$emit('login', {})">Logout</v-btn>
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
const providers = () => import("./providers");
const mlids = () => import("../manager/mlids");

const calls = () => import("../crmanager/calls");
const lids3 = () => import("../crmanager/lids3");
const lids4 = () => import("../crmanager/lids4");
const report = () => import("./report");
const reportPie = () => import("./reportPie");
const reportBTC = () => import("./reportBTC");
const reportCalDep = () => import("./reportCalDep");

export default {
  props: ["user"],
  data: () => ({
    drawer: null,
    selectedItem: 0,

    items: [
      {
        text: "Import CSV",
        name: "importcsv",
        icon: "mdi-arrow-down-bold-box-outline",
      },
      { text: "Users", name: "users", icon: "mdi-account" },
      {
        text: "Status leads",
        name: "statusLid",
        icon: "mdi-format-list-checks",
      },
      {
        text: "Providers",
        name: "providers",
        icon: "mdi-library",
      },

      { text: "Distribution", name: "lids3", icon: "mdi-sitemap" },
      { text: "Distribution2", name: "lids4", icon: "mdi-source-branch" },
      { text: "Calls", name: "calls", icon: "mdi-headset-dock" },
      { text: "Management", name: "mlids", icon: "mdi-phone-log-outline" },
      { text: "Report", name: "report", icon: "mdi-receipt" },
      { text: "Reports", name: "reportPie", icon: "mdi-timetable" },
      { text: "Report BTC", name: "reportBTC", icon: "mdi-cash" },
      {
        text: "Report CalDep",
        name: "reportCalDep",
        icon: "mdi-format-align-left",
      },
    ],
    adminMenu: "",
  }),
  computed: {
    adminComponent() {
      if (this.adminMenu == "importcsv") return importcsv;
      if (this.adminMenu == "users") return users;
      if (this.adminMenu == "statusLid") return statusLid;
      // if (this.adminMenu == "workPlaces") return workPlaces;
      if (this.adminMenu == "providers") return providers;
      if (this.adminMenu == "mlids") return mlids;
      if (this.adminMenu == "lids3") return lids3;
      if (this.adminMenu == "lids4") return lids4;
      if (this.adminMenu == "calls") return calls;
      if (this.adminMenu == "report") return report;
      if (this.adminMenu == "reportPie") return reportPie;
      if (this.adminMenu == "reportBTC") return reportBTC;
      if (this.adminMenu == "reportCalDep") return reportCalDep;
    },
  },
  mounted: function () {
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
  },
  methods: {},
};
</script>

<style>
</style>
