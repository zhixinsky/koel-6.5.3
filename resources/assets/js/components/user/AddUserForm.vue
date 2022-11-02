<template>
  <div @keydown.esc="maybeClose">
    <SoundBars v-if="loading"/>
    <form v-else data-testid="add-user-form" @submit.prevent="submit">
      <header>
        <h1>添加新用户</h1>
      </header>

      <main>
        <div class="form-row">
          <label>
            用户名
            <input v-model="newUser.name" v-koel-focus name="name" required title="Name" type="text">
          </label>
        </div>
        <div class="form-row">
          <label>
            邮箱
            <input v-model="newUser.email" name="email" required title="Email" type="email">
          </label>
        </div>
        <div class="form-row">
          <label>
            密码
            <input
              v-model="newUser.password"
              autocomplete="new-password"
              name="password"
              required
              title="Password"
              type="password"
            >
          </label>
          <p class="help">最小10个字符,包含字符、数字和符号的混合。</p>
        </div>
        <div class="form-row">
          <label>
            <CheckBox name="is_admin" v-model="newUser.is_admin"/>
            设置成管理员
            <TooltipIcon title="管理员可以执行管理任务，例如管理用户和上传歌曲。"/>
          </label>
        </div>
      </main>

      <footer>
        <Btn class="btn-add" type="submit">保存</Btn>
        <Btn class="btn-cancel" white @click.prevent="maybeClose">取消</Btn>
      </footer>
    </form>
  </div>
</template>

<script lang="ts" setup>
import { isEqual } from 'lodash'
import { reactive, ref } from 'vue'
import { CreateUserData, userStore } from '@/stores'
import { parseValidationError, requireInjection } from '@/utils'
import { DialogBoxKey, MessageToasterKey } from '@/symbols'

import Btn from '@/components/ui/Btn.vue'
import SoundBars from '@/components/ui/SoundBars.vue'
import TooltipIcon from '@/components/ui/TooltipIcon.vue'
import CheckBox from '@/components/ui/CheckBox.vue'

const toaster = requireInjection(MessageToasterKey)
const dialog = requireInjection(DialogBoxKey)

const loading = ref(false)

const emptyUserData: CreateUserData = {
  name: '',
  email: '',
  password: '',
  is_admin: false
}

const newUser = reactive<CreateUserData>(Object.assign({}, emptyUserData))

const submit = async () => {
  loading.value = true

  try {
    await userStore.store(newUser)
    toaster.value.success(`New user "${newUser.name}" created.`)
    close()
  } catch (err: any) {
    const msg = err.response.status === 422 ? parseValidationError(err.response.data)[0] : 'Unknown error.'
    dialog.value.error(msg, 'Error')
  } finally {
    loading.value = false
  }
}

const emit = defineEmits(['close'])

const close = () => emit('close')

const maybeClose = async () => {
  if (isEqual(newUser, emptyUserData)) {
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
