<template>
  <div>
    <v-row>
      <v-col v-if="!lead.docs_сompl">
        <v-sheet outlined class="pa-3">
          <v-text-field label="Description" v-model="desc"></v-text-field>
          <v-file-input
            label="File input"
            @change="onFilePicked"
            v-model="file"
          ></v-file-input>
          <v-btn block :disabled="filedesc" @click="uploadFile">Upload</v-btn>
        </v-sheet>
      </v-col>
      <v-col>
        <v-list two-line max-height="200" class="overflow-y-auto">
          <v-list-item two-line v-for="doc in docs" :key="doc.id">
            <v-list-item-content>
              <v-list-item-title>{{ doc.file_name }}</v-list-item-title>
              <v-list-item-subtitle>{{ doc.description }}</v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-icon>
              <v-icon @click="delDoc(doc.id)">mdi-delete</v-icon>
            </v-list-item-icon>
            <v-list-item-icon>
              <v-icon @click="downloadDoc(doc)">mdi-download</v-icon>
            </v-list-item-icon>
          </v-list-item>
        </v-list>
      </v-col>
    </v-row>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "Tierdoc",
  props: ["lead"],
  data() {
    return {
      file: null,
      desc: "",
      fileType: "",
      fileName: "",
      fileSize: "",
      docs: [],
    };
  },

  mounted() {
    this.getDocs();
  },
  computed: {
    filedesc() {
      return this.file == null || this.desc == "" ? true : false;
    },
  },
  methods: {
    pickFile() {
      this.$refs.tier.click();
    },
    onFilePicked(e) {
      if (!this.file) {
        this.data = "No File Chosen";
      }
      const fr = new FileReader();
      fr.readAsDataURL(this.file);
      fr.addEventListener("load", () => {
        this.fileSize = this.file.size;
        this.fileName = this.file.name;
        this.fileType = this.file.type;
      });
    },
    downloadDoc(doc) {
      axios
        .post("/api/downloadDoc/", doc)
        .then((res) => {})
        .catch((err) => {});
    },
    delDoc(doc_id) {
      const self = this;

      axios
        .get("/api/delDoc/" + doc_id)
        .then((res) => {
          if (res.status == 200) {
            self.getDocs();
          }
        })
        .catch((err) => {});
    },
    getDocs() {
      const self = this;

      axios
        .get("/api/getDocs/" + self.lead.id)
        .then((res) => {
          if (res.status == 200 && res.data.length > 0) {
            self.docs = res.data;
          } else {
            self.docs = [];
          }
        })
        .catch((err) => {});
    },
    uploadFile() {
      const self = this;
      let formData = new FormData();
      formData.append("file", this.file);
      formData.append("fileSize", this.file.size);
      formData.append("fileName", this.file.name);
      formData.append("fileType", this.file.type);
      formData.append("lead_id", this.lead.id);
      formData.append("desc", this.desc);
      axios
        .post("/api/uploadDoc", formData)
        .then((res) => {
          if (res.status == 200) {
            self.file = null;
            self.desc = "";
            self.fileType = "";
            self.fileName = "";
            self.fileSize = "";
            self.getDocs();
          }
        })
        .catch((err) => {});
    },
  },
};
</script>
