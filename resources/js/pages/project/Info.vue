<template>
  <div class="project-info" v-if="project">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-8">
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Название</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" v-model="name">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Сотрудник</label>
            <div class="col-sm-10">
              <select name="user" class="form-control" v-model="user_id">
                <option :value="null">Не выбран</option>
                <option v-for="user in users" v-bind:value="user.id" :key="user.id">{{ user.name }}</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Описание</label>
            <div class="col-sm-10">
              <editor v-model="description"></editor>
            </div>
          </div>
          <button type="submit" class="btn btn-primary mr-4" @click="saveProject">Сохранить</button>
          <a class="text-danger" @click="removeProject">Удалить проект</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import API from "@/API";

export default {
  data() {
    return {
      users: null
    };
  },
  computed: {
    user_id: {
      get() {
        return this.project.user_id;
      },
      set(user_id) {
        this.$store.commit("setProject", { user_id });
      }
    },
    name: {
      get() {
        return this.project.name;
      },
      set(name) {
        this.$store.commit("setProject", { name });
      }
    },
    description: {
      get() {
        return this.project.description;
      },
      set(description) {
        this.$store.commit("setProject", { description });
      }
    },
    project() {
      return this.$store.getters.project;
    }
  },
  methods: {
    saveProject() {
      this.$store.dispatch("updateProject").then(() => {
        this.$store.dispatch("fetchProject", this.project.id );
        this.$message({
          message: "Проект обновлен!",
          type: "success"
        });
      });
    },
    removeProject() {
      let is_remove = confirm("Вы точно хотите удалить проект?");
      if (is_remove) {
        this.$store.dispatch("removeProject").then(() => {
          this.$router.push({ name: "projects" });
        });
      }
    },
    fetchUsers() {
      API.fetchUsers().then(respose => {
        this.users = respose.data.data;
      });
    }
  },
  created() {
    this.fetchUsers();
  }
};
</script>

<style lang="scss">
//
</style>