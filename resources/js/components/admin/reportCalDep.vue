<template>
  <div>
    <v-tabs v-model="tab">
      <v-tab>Call Dep</v-tab>
      <v-tab>Users report</v-tab>
      <v-tab>Period report</v-tab>
    </v-tabs>
    <v-progress-linear
      :active="loading"
      :indeterminate="loading"
      color="blue accent-4"
    ></v-progress-linear>
    <v-tabs-items v-model="tab">
      <!-- Call Dep -->
      <v-tab-item>
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
                          v-model="dateTimeFrom"
                          readonly
                          v-bind="attrs"
                          v-on="on"
                          outlined
                          label="From Date"
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="dateTimeFrom"
                        @input="
                          dateFrom = false;
                          reportCalDep();
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
                          reportCalDep();
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
            :indeterminate="loading"
            color="blue accent-4"
          ></v-progress-linear>
          <v-row>
            <v-col>
              <div class="wrp__statuses">
                <template>
                  <div class="status_wrp" v-for="(i, x) in statuses" :key="x">
                    <b
                      :style="{
                        background: i.color,
                        outline: '1px solid' + i.color,
                      }"
                      >{{ i.hm }}</b
                    >
                    <span>{{ i.name }}</span>
                    <!-- <v-btn
                v-if="filterStatus.length > 0"
                icon
                x-small
                @click="changeFilterStatus(i.id)"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn> -->
                  </div>
                </template>
              </div>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <div class="wrp__statuses">
                <template>
                  <div class="status_wrp" v-for="(i, x) in telcod" :key="x">
                    <b
                      :style="{
                        background: '#999',
                        outline: '1px solid #555',
                      }"
                      >{{ parseInt((i.hm * 100) / all) }}%</b
                    >
                    <span>{{ telcodcoun[i.telcod] ?? i.telcod }}</span>
                  </div>
                </template>
              </div>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <table width="100%" id="tableCalDep" class="mt-3">
                <tr>
                  <th>Date</th>
                  <th>Provider</th>
                  <th>
                    <v-btn text x-small @click="orderDates('date')"
                      ><b>Leads</b> <v-icon>mdi-menu-down</v-icon></v-btn
                    ><br />
                    <v-btn text x-small @click="orderDates('cal')"
                      ><b>CallBack</b> <v-icon>mdi-menu-down</v-icon></v-btn
                    ><br />
                    <v-btn text x-small @click="orderDates('dp')"
                      ><b>Deposit</b> <v-icon>mdi-menu-down</v-icon></v-btn
                    >
                  </th>
                  <th style="width: 70vw">Data</th>
                </tr>
                <tr v-for="(item, ix) in dates" :key="ix">
                  <td>{{ item.date }}</td>
                  <td>
                    <div>
                      <b>{{ item.provider }}</b>
                    </div>
                    <div>&nbsp;&nbsp;CallBack</div>
                    <div>&nbsp;&nbsp;Deposit</div>
                  </td>
                  <td style="padding-left: 0.5rem">
                    <div>{{ item.hm }}</div>
                    <div>{{ item.cal }}</div>
                    <div>{{ item.dp }}</div>
                  </td>
                  <td>
                    <div>total calls statistics {{ item.percent }}%</div>
                    <div
                      style="width: 100%; height: 2.7rem; background: #b3c6e7"
                    >
                      <div
                        :style="{
                          background: '#305493',
                          height: '1.3rem',
                          width: (item.cal * 100) / item.hm + '%',
                        }"
                      ></div>
                      <div
                        :style="{
                          background: '#305493',
                          height: '1.3rem',
                          width: (item.dp * 100) / item.hm + '%',
                        }"
                      ></div>
                    </div>
                  </td>
                </tr>
              </table>
            </v-col>
          </v-row>
        </v-container>
      </v-tab-item>
      <!-- Users report -->
      <v-tab-item> <h2>Users report</h2></v-tab-item>
      <!-- Period report -->
      <v-tab-item>
        <h2>Period report</h2>
        <PeriodTier></PeriodTier>
      </v-tab-item>
    </v-tabs-items>
  </div>
</template>

<script>
import axios from "axios";
import _ from "lodash";
import PeriodTier from "./PeriodTier";

export default {
  components: {
    PeriodTier,
  },
  data: () => ({
    tab: 0,
    loading: false,
    dateFrom: false,
    dateTo: false,
    dateTimeFrom: new Date(new Date().setDate(new Date().getDate() - 7))
      .toISOString()
      .substring(0, 10),
    dateTimeTo: new Date().toISOString().substring(0, 10),
    dates: {},
    selectedOrder: 1,
    statuses: [],
    telcod: [],
    all: 0,
    telcodcoun: {
      61: "Австралия",
      43: "Австрия",
      994: "Азербайджан",
      355: "Албания",
      213: "Алжир",
      244: "Ангола",
      376: "Андорра",
      1268: "Антигуа и Барбуда",
      54: "Аргентина",
      374: "Армения",
      93: "Афганистан",
      1242: "Багамы",
      880: "Бангладеш",
      1246: "Барбадос",
      973: "Бахрейн",
      375: "Беларусь",
      501: "Белиз",
      32: "Бельгия",
      229: "Бенин",
      359: "Болгария",
      591: "Боливия",
      387: "Босния и Герцеговина",
      267: "Ботсвана",
      55: "Бразилия",
      673: "Бруней",
      226: "Буркина Фасо",
      257: "Бурунди",
      975: "Бутан",
      678: "Вануату",
      39: "Ватикан",
      44: "Великобритания",
      36: "Венгрия",
      58: "Венесуэла",
      670: "Восточный Тимор",
      84: "Вьетнам",
      241: "Габон",
      509: "Гаити",
      592: "Гайана",
      220: "Гамбия",
      233: "Гана",
      502: "Гватемала",
      224: "Гвинея",
      245: "Гвинея-Бисау",
      49: "Германия",
      504: "Гондурас",
      1473: "Гренада",
      30: "Греция",
      995: "Грузия",
      45: "Дания",
      253: "Джибути",
      1767: "Доминика",
      1809: "Доминиканская Республика",
      20: "Египет",
      260: "Замбия",
      263: "Зимбабве",
      972: "Израиль",
      91: "Индия",
      62: "Индонезия",
      962: "Иордания",
      964: "Ирак",
      98: "Иран",
      353: "Ирландия",
      354: "Исландия",
      34: "Испания",
      39: "Италия",
      967: "Йемен",
      238: "Кабо-Верде",
      77: "Казахстан",
      855: "Камбоджа",
      237: "Камерун",
      1: "Канада",
      974: "Катар",
      254: "Кения",
      357: "Кипр",
      996: "Киргизия",
      686: "Кирибати",
      86: "Китай",
      57: "Колумбия",
      269: "Коморы",
      243: "демократическая республика Конго",
      242: "республика Конго",
      506: "Коста-Рика",
      225: "Кот-д’Ивуар",
      53: "Куба",
      965: "Кувейт",
      856: "Лаос",
      371: "Латвия",
      266: "Лесото",
      231: "Либерия",
      961: "Ливан",
      218: "Ливия",
      370: "Литва",
      423: "Лихтенштейн",
      352: "Люксембург",
      230: "Маврикий",
      222: "Мавритания",
      261: "Мадагаскар",
      389: "Македония",
      265: "Малави",
      60: "Малайзия",
      223: "Мали",
      960: "Мальдивы",
      356: "Мальта",
      212: "Марокко",
      692: "Маршалловы Острова",
      52: "Мексика",
      259: "Мозамбик",
      373: "Молдавия",
      377: "Монако",
      976: "Монголия",
      95: "Мьянма",
      264: "Намибия",
      674: "Науру",
      977: "Непал",
      227: "Нигер",
      234: "Нигерия",
      31: "Нидерланды",
      505: "Никарагуа",
      64: "Новая Зеландия",
      47: "Норвегия",
      971: "Объединенные Арабские Эмираты",
      968: "Оман",
      92: "Пакистан",
      680: "Палау",
      507: "Панама",
      675: "Папуа — Новая Гвинея",
      595: "Парагвай",
      51: "Перу",
      48: "Польша",
      351: "Португалия",
      7: "Россия",
      250: "Руанда",
      40: "Румыния",
      503: "Сальвадор",
      685: "Самоа",
      378: "Сан-Марино",
      239: "Сан-Томе и Принсипи",
      966: "Саудовская Аравия",
      268: "Свазиленд",
      850: "Северная Корея",
      248: "Сейшелы",
      221: "Сенегал",
      1784: "Сент-Винсент и Гренадины",
      1869: "Сент-Китс и Невис",
      1758: "Сент-Люсия",
      381: "Сербия",
      65: "Сингапур",
      963: "Сирия",
      421: "Словакия",
      986: "Словения",
      1: "Соединенные Штаты Америки",
      677: "Соломоновы Острова",
      252: "Сомали",
      249: "Судан",
      597: "Суринам",
      232: "Сьерра-Леоне",
      992: "Таджикистан",
      66: "Таиланд",
      255: "Танзания",
      228: "Того",
      676: "Тонга",
      1868: "Тринидад и Тобаго",
      688: "Тувалу",
      216: "Тунис",
      993: "Туркмения",
      90: "Турция",
      256: "Уганда",
      998: "Узбекистан",
      380: "Украина",
      598: "Уругвай",
      691: "Федеративные штаты Микронезии",
      679: "Фиджи",
      63: "Филиппины",
      358: "Финляндия",
      33: "Франция",
      385: "Хорватия",
      236: "Центрально-Африканская Республика",
      235: "Чад",
      381: "Черногория",
      420: "Чехия",
      56: "Чили",
      41: "Швейцария",
      46: "Швеция",
      94: "Шри-Ланка",
      593: "Эквадор",
      240: "Экваториальная Гвинея",
      291: "Эритрея",
      372: "Эстония",
      251: "Эфиопия",
      82: "Южная Корея",
      27: "Южно-Африканская Республика",
      1876: "Ямайка",
      81: "Япония",
    },
  }),
  mounted: function () {
    this.reportCalDep();
  },
  methods: {
    orderDates(on) {
      this.dates = _.orderBy(this.dates, on, on == "date" ? "asc" : "desc");
    },
    reportCalDep() {
      let self = this;
      this.loading = true;
      if (this.datetimeFrom == "")
        this.datetimeFrom = new Date(
          new Date().setDate(new Date().getDate() - 7)
        )
          .toISOString()
          .substring(0, 10);
      if (this.datetimeTo == "")
        this.datetimeTo = new Date().toISOString().substring(0, 10);

      axios
        .get(
          `/api/reportCalDep?dateFrom=${this.dateTimeFrom}&dateTo=${this.dateTimeTo}`
        )
        .then((res) => {
          self.loading = false;
          self.dates = res.data.dates;
          self.statuses = res.data.statuses;
          self.telcod = res.data.telcod;
          self.all = res.data.all;
        })

        .catch((error) => console.log(error));
    },
  },
};
</script>

<style >
#tableCalDep tr {
  margin: 1rem 0;
  border-bottom: 1px solid grey;
}
</style>
