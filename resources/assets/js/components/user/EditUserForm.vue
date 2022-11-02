<template>
  <div class="edit-user" @keydown.esc="maybeClose">
    <SoundBars v-if="loading"/>
    <form v-else class="user-edit" data-testid="edit-user-form" @submit.prevent="submit">
      <header>
        <h1>编辑用户</h1>
      </header>

      <main>
        <div class="form-row">
          <label>
            用户名
            <input v-model="updateData.name" v-koel-focus name="name" required title="Name" type="text">
          </label>
        </div>
        <div class="form-row">
          <label>
            邮箱
            <input v-model="updateData.email" name="email" required title="Email" type="email">
          </label>
        </div>
        <div class="form-row">
          <label>
            密码
            <input
              v-model="updateData.password"
              autocomplete="new-password"
              name="password"
              placeholder="Leave blank for no changes"
              type="password"
            >
          </label>
          <p class="help">最小10个字符,包含字符、数字和符号的混合。</p>
        </div>
        <div class="form-row">
          <label>
            <CheckBox v-model="updateData.is_admin" name="is_admin"/>
            设置成管理员
            <TooltipIcon title="管理员可以执行管理任务，例如管理用户和上传歌曲。"/>
          </label>
        </div>
      </main>

      <footer>
        <Btn class="btn-update" type="submit">更新</Btn>
        <Btn class="btn-cancel" white @click.prevent="maybeClose">取消</Btn>
      </footer>
    </form>
  </div>
</template>

<script lang="ts" setup>
import { isEqual } from 'lodash'
import { reactive, ref, watch } from 'vue'
import { logger, parseValidationError, requireInjection } from '@/utils'
import { UpdateUserData, userStore } from '@/stores'
import { DialogBoxKey, MessageToasterKey, UserKey } from '@/symbols'

import Btn from '@/components/ui/Btn.vue'
import SoundBars from '@/components/ui/SoundBars.vue'
import TooltipIcon from '@/components/ui/TooltipIcon.vue'
import CheckBox from '@/components/ui/CheckBox.vue'

const toaster = requireInjection(MessageToasterKey)
const dialog = requireInjection(DialogBoxKey)
const [user] = requireInjection(UserKey)

let originalData: UpdateUserData
let updateData: UpdateUserData

watch(user, () => {
  originalData = {
    name: user.value.name,
    email: user.value.email,
    is_admin: user.value.is_admin
  }

  updateData = reactive(Object.assign({}, originalData))
}, { immediate: true })

const loading = ref(false)

const submit = async () => {
  loading.value = true

  try {
    await userStore.update(user.value, updateData)
    toaster.value.success('User profile updated.')
    close()
  } catch (error: any) {
    const msg = error.response.status === 422 ? parseValidationError(error.response.data)[0] : 'Unknown error.'
    dialog.value.error(msg, 'Error')
    logger.error(error)
  } finally {
    loading.value = false
  }
}

const emit = defineEmits(['close'])

const close = () => emit('close')

const maybeClose = async () => {
  if (isEqual(originalData, updateData)) {
    close()
    return
  }

  await dialog.value.confirm('Discard all changes?') && close()
}
</script>

<style lang="scss" scoped>
.help {
  margin-top: .75rem;
}
</style>
