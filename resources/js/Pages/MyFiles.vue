<template>
  <AuthenticatedLayout>
    <nav class="flex justify-between">
      <ol class="flex items-center mb-3">
        <li v-if="ancestors" v-for="ancestor in ancestors.data" :key="ancestor.id" class="inline-flex items-center">
          <Link v-if="!ancestor.parent_id" :href="route('myFiles')"
                class="inline-flex items-center text-sm font-medium text-gray-800 dark:text-gray-600 dark:hover:text-black dark:hover:font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="w-6 h-6 mr-1 text-gray-950">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
            </svg>
            My Files
          </Link>
          <div v-else class="flex items-center">
            <svg aria-hidden="true" class="w-6 h-6 text-gray-950" fill="currentColor" viewBox="0 0 20 20"
                 xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"></path>
            </svg>
            <Link :href="route('myFiles', {folder: ancestor.path})"
                  class="ml-1 text-sm font-medium text-gray-800 md:ml-2 dark:text-gray-600 dark:hover:text-black dark:hover:font-semibold">
              {{ ancestor.name }}
            </Link>
          </div>
        </li>
      </ol>
      <div class="flex mb-3 justify-end mr-8 pt-1">
        <DownloadFilesButton :all="allSelected" :ids="selectedIds"/>
        <DeleteFilesButton :delete-all="allSelected" :delete-ids="selectedIds" @delete="onDelete"/>
      </div>
    </nav>
    <div class="flex-1 overflow-auto">
      <table class="min-w-full">
        <thead class="border border-b-black">
        <tr>
          <th class="font-semibold border border-r-black border-b-black p-3">
            <Checkbox v-model:checked="allSelected" @change="onAllSelectedChange"
                      class="cursor-pointer p-3"></Checkbox>
          </th>
          <th class=" border border-b-black border-r-black"></th>
          <th class="font-semibold border border-r-black border-b-black">Name</th>
          <th class="font-semibold border border-r-black border-b-black">Owner</th>
          <th class="font-semibold border border-r-black border-b-black">Size</th>
          <th class="font-semibold border border-r-black border-b-black">Last Modified</th>
          <th class="font-semibold border border-r-black border-b-black">Created</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="file in allFiles.data" @keyup.shift="selectMultipleCheckbox"
            :class="selected[file.id] || allSelected ? 'bg-blue-200' : file.is_folder === 1 ? 'bg-amber-100' : ''"
            @dblclick.prevent="openFolder(file)" @click="toggleFileSelect($event, file)"
            @change="onSelectedCheckboxChange(file)"
            class="cursor-pointer transition duration-300 ease-in-out hover:bg-gray-300" :key="file.id">
          <td class="font-medium border border-r-black border-b-black relative">
            <Checkbox :checked="selected[file.id] || allSelected" v-model="selected[file.id]"
                      @change="onSelectedCheckboxChange(file)"
                      class="cursor-pointer absolute top-[40%] left-[40%] p-2">
            </Checkbox>
          </td>
          <td class=" border border-r-black border-b-black text-sm font-medium text-gray-900 text-yellow-500">
            <div @click.stop.prevent="toggleFavourite(file)"  class="p-2 flex items-center justify-center">
              <svg v-if="!file.is_favorite" xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24" stroke-width="1.5"
                   stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                   class="w-6 h-6">
                <path fill-rule="evenodd"
                      d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                      clip-rule="evenodd"/>
              </svg>
            </div>
          </td>
          <td class="cursor-pointer p-2 font-medium border border-r-black  border-b-black"><span
              class="flex items-center"><FileIcon :file="file" class="pl-3"/><span class="pl-4">{{
              file.name
            }}</span></span>
          </td>
          <td class="p-2 font-medium border border-r-black  border-b-black">{{ file.owner }}</td>

          <td class="p-2 font-medium border border-r-black  border-b-black">{{ file.size }}</td>
          <td class="p-2 font-medium border border-r-black  border-b-black">{{ file.updated_at }}</td>
          <td class="p-2 font-medium border border-r-black  border-b-black">{{ file.created_at }}</td>
        </tr>
        </tbody>
      </table>
      <div v-if="!allFiles.data.length" class="py-8 text-center font-semibold text-4xl text-gray-400">
        <h1>There no data in laravel driver</h1>
      </div>
      <div ref="loadMoreIntersect">

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
//imports
import AuthenticatedLayout from "../Layouts/AuthenticatedLayout.vue";
import {Link, router, useForm} from "@inertiajs/vue3";
import FileIcon from "../Components/app/FileIcon.vue";
import {computed, onMounted, onUpdated, ref} from "vue";
import {httpGet} from "../Helper/http-helper.js";
import Checkbox from "../Components/Checkbox.vue";
import DeleteFilesButton from "../Components/app/DeleteFilesButton.vue";
import DownloadFilesButton from "../Components/app/DownloadFilesButton.vue";
import {showErrorNotification, showSuccessNotification} from "../event-mitt.js";

//refs
const allSelected = ref(false)
const selected = ref({})
const loadMoreIntersect = ref(null)

const allFiles = ref({
  data: props.files.data,
  next: props.files.links.next
})
//props & emits
const props = defineProps({
  files: Object,
  folder: Object,
  ancestors: Object
})

//methods
function selectMultipleCheckbox() {
  console.log(selected)
}


function toggleFavourite(file) {
  const removeForm = useForm({
    id: file.id,
  });

  removeForm.post(route('file.add-favorites'), {
    onSuccess: () => {
      showSuccessNotification('Action with file was successful')
    },
    onError: error => {
      showErrorNotification(error)
    }
  })
}

function multiFileSelect(files) {
  if (files.length > 1) {
    allFiles.value.data.forEach(file => {
      if (!file.is_folder) {
        if (file.id >= Math.min(...files) && file.id <= Math.max(...files)) {
          selected.value[file.id] = true;
        }
      }
    })
  }
}

function multiFolderSelect(folders) {
  if (folders.length > 1) {
    allFiles.value.data.forEach(folder => {
      if (folder.is_folder) {
        if (folder.id >= Math.min(...folders) && folder.id <= Math.max(...folders)) {
          selected.value[folder.id] = true;
        }
      }
    })
  }
}

function multiFileAndFolderSelect(files, folders) {
  if (folders.length > 0 && files.length > 0) {
    allFiles.value.data.forEach(file => {
      if (file.is_folder) {
        if (file.id > 0 && file.id <= Math.max(...folders)) {
          selected.value[file.id] = true;
        }
      }
      if (!file.is_folder) {
        if (file.id >= Math.min(...files)) {
          selected.value[file.id] = true;
        }
      }
    })
  }

}

function toggleFileSelect(event, file) {
  if (event.shiftKey) {
    selected.value[file.id] = !selected.value[file.id];
    let folders = [];
    let files = [];
    allFiles.value.data.forEach(file => {
      for (let id of Object.keys(selected.value)) {
        if (file.id == id && file.is_folder) {
          folders.push(file.id);
        } else if (file.id == id && !file.is_folder) {
          files.push(file.id);
        }

      }
    })

    // multi file select
    multiFileSelect(files)

    // multi folder select
    multiFolderSelect(folders)

    // multi file + folder select
    multiFileAndFolderSelect(files, folders)

    if (files.length > 2 || folders.length > 2) {
      selected.value = {};
      files.length = 0;
      folders.length = 0;
    }

  } else selected.value[file.id] = !selected.value[file.id];
}

function onAllSelectedChange() {
  allFiles.value.data.forEach(file => {
    selected.value[file.id] = allSelected.value;
  })
}

function onSelectedCheckboxChange(file) {
  if (!selected.value[file.id]) allSelected.value = false;
  else {
    let check = true;
    allFiles.value.data.forEach(file => {
      if (!selected.value[file.id]) {
        check = false;
      }
    })
    allSelected.value = check;
  }
}

function loadMoreFiles() {

  if (allFiles.value.next === null) {
    return;
  }
  httpGet(allFiles.value.next)
      .then(res => {
        allFiles.value.data = [...allFiles.value.data, ...res.data]
        allFiles.value.next = res.links.next;
      })
      .catch(error => {
        console.error(error); // Handle errors that occur during the request
      });
}

function openFolder(file) {
  if (!file.is_folder) {
    return;
  }
  router.visit(route('myFiles', {folder: file.path}))
}

function onDelete() {
  selected.value = {};
  allSelected.value = false;
}

//computed
const selectedIds = computed(() => {
  return Object.keys(selected.value).filter(fileId => selected.value[fileId]);
});

// hooks

onUpdated(() => {

    allFiles.value = {
      data: props.files.data,
      next: props.files.links.next
    }
})
onMounted(() => {
  const observer = new IntersectionObserver((entries) => entries.forEach(entry => entry.isIntersecting && loadMoreFiles()), {
    rootMargin: "-250px 0px 0px 0px"
  })
  observer.observe(loadMoreIntersect.value)
})

</script>
