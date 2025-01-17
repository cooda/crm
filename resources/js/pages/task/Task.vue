<template>
    <pane class="single-task" v-if="task" :key="task.id" strech>
      <template #header>
        <div class="single-task__header">
          <el-button icon="el-icon-close" plain @click="close" class="border-0"></el-button>
          <el-select
            v-model="status"
            placeholder="Статус"
            size="small"
            :clearable="true"
            class="task-status"
            value-key="id"
          >
            <el-option
              v-for="status in statuses"
              :key="status.id"
              :label="status.name"
              :value="status"
            ></el-option>
          </el-select>
          <div class="single-task__header__tools">
            <el-button type="danger" icon="el-icon-delete" plain circle @click="remove" size="mini"></el-button>
          </div>
        </div>
      </template>
      
      <div class="single-task__name-input">
        <div class="single-task__name-input__inner">
          <div class="single-task__name-input__shadow">{{ name }}|</div>
          <textarea
            class="single-task__name-input__textarea simple-textarea"
            placeholder="Название задачи"
            v-model="name"
            @keydown.enter.exact.prevent
          ></textarea>
        </div>
      </div>
      <!--/ ssingle-task__name__input -->
      <el-tabs v-model="activeTab">
        <el-tab-pane label="Задача" name="task">
          <div class="date_asigne_row">
            <user-picker v-model="assignee" :users="users" @clear="resetUserPicker"></user-picker>
            <el-date-picker
              v-model="due_time"
              type="date"
              placeholder="Укажите дату"
              :format="dateFormat"
              value-format="timestamp"
              :class="{'red-time' : red_time}"
            ></el-date-picker>
          </div>

          <!-- single-task__description -->
          <div class="single-task__description">
            <editor v-model="description"></editor>
          </div>
          <div>Создан: {{ created_time }}</div>
          <!-- /single-task__description -->
        </el-tab-pane>
        <el-tab-pane label="Комментарии" name="comments">Комментарии</el-tab-pane>
        <el-tab-pane label="Документы" name="docs">Документы</el-tab-pane>
      </el-tabs>

      <template #footer>{{ red_time }}</template>
    </pane>
</template>

<script>
import _ from "lodash";
import { taskMixins } from "@/mixins";
import { mapGetters } from "vuex";
import API from "@/API";

import UserPicker from "@/components/user/UserPicker";

export default {
  data() {
    return {
      activeTab: "task",
      statuses: []
    };
  },
  computed: {
    due_time: {
      get() {
        return this.task.due_time;
      },
      set(due_time) {
        this.updateTask("due_time", due_time);
      }
    },
    name: {
      get() {
        return this.task.name;
      },
      set(name) {
        this.updateTask("name", name);
      }
    },
    description: {
      get() {
        return this.task.description;
      },
      set(description) {
        this.updateTask("description", description);
      }
    },
    assignee: {
      get() {
        return this.task.assignee;
      },
      set(assignee) {
        this.$store.dispatch("setAssignee", {
          task_id: this.task.id,
          assignee: assignee
        });
      }
    },
    status: {
      get() {
        return this.task.status;
      },
      set(status) {
        this.$store.dispatch("setStatus", {
          task_id: this.task.id,
          status: status
        });
      }
    },
    created_time() {
      let created_time = new Date(this.task.created_time);
      return created_time.toLocaleDateString();
    },
    red_time() {
      return this.isRedTime(this.task.due_time);
    },
    ...mapGetters(["project", "task", "users"])
  },
  methods: {
    remove() {
      this.$store.dispatch("removeTask", { task_id: this.task.id });
      this.successMsg("Задача удалена!");
      this.close();
    },

    close() {
      this.$store.commit("resetTask");
      this.$router.push({
        name: "tasks",
        params: { project_id: this.project.id }
      });
    },

    updateTask(key, value) {
      this.$store.dispatch("updateTask", {
        task_id: this.task.id,
        [key]: value
      });
    },

    fetchTask(task_id) {
      this.$store.dispatch("fetchTask", task_id);
    },

    fetchUsers() {
      this.$store.dispatch("fetchUsers");
    },

    fetchTaskStatuses() {
      API.fetchTaskStatuses().then(response => {
        this.statuses = response.data;
      });
    },

    resetUserPicker() {
      this.$store.dispatch("removeAssignee", { task_id: this.task.id });
    }
  },
  created() {
    this.fetchTask(this.$route.params.task_id);
    this.fetchUsers();
    this.fetchTaskStatuses();
  },
  components: {
    UserPicker
  },
  mixins: [taskMixins]
};
</script>