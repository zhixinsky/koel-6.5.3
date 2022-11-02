<template>
  <ContextMenuBase ref="base">
    <li data-testid="playlist-context-menu-create-simple" @click="onItemClicked('new-playlist')">新建播放列表</li>
    <li data-testid="playlist-context-menu-create-smart" @click="onItemClicked('new-smart-playlist')">
      新建智能播放列表
    </li>
    <li data-testid="playlist-context-menu-create-folder" @click="onItemClicked('new-folder')">新建文件夹</li>
  </ContextMenuBase>
</template>

<script lang="ts" setup>
import { useContextMenu } from '@/composables'
import { eventBus } from '@/utils'
import { EventName } from '@/config'
import { onMounted } from 'vue'

const { base, ContextMenuBase, open, trigger } = useContextMenu()

const emit = defineEmits(['itemClicked'])

const actionToEventMap: Record<string, EventName> = {
  'new-playlist': 'MODAL_SHOW_CREATE_PLAYLIST_FORM',
  'new-smart-playlist': 'MODAL_SHOW_CREATE_SMART_PLAYLIST_FORM',
  'new-folder': 'MODAL_SHOW_CREATE_PLAYLIST_FOLDER_FORM'
}

const onItemClicked = (key: keyof typeof actionToEventMap) => trigger(() => eventBus.emit(actionToEventMap[key]))

onMounted(() => {
  eventBus.on('CREATE_NEW_PLAYLIST_CONTEXT_MENU_REQUESTED', async (e: MouseEvent) => {
    await open(e.pageY, e.pageX)
  })
})
</script>
