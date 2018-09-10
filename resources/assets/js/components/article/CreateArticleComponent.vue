<template>
  <div style="margin-top: 60px;">
    <div v-transfer-dom>
      <loading :show="show1" :text="text1"></loading>
    </div>
    <form name="form" ref="form">
    <group :title="$t('创建爆文')" label-align="justify">
        <x-input v-model='form.link' :placeholder="$t('长按粘贴文章链接到此处')"  :required="true" :is-type="validlink"></x-input>

    <box gap="70px 10px 20px">
    <x-button type="primary" action-type="button" @click.native="submit('form')" headers="uploadHeaders">在文章中嵌入我的名片</x-button>
    </box>
    </group>
    </form>
  </div>
</template>



<script>
import {  Group, XInput,XButton,Box,AlertPlugin,Loading,TransferDomDirective as TransferDom   } from 'vux'

export default {
  directives: {
    TransferDom
  },
  components: {
    Group,
    XInput,
    XButton,
    Box,
    AlertPlugin,
    Loading,
  },
  data () {
        return {
            show1: false,
            text1: 'Processing',
            form :{
                link: '',
            },
            validlink: function (value) {
                return {
                    valid: /(http:\/\/|https:\/\/)?mp.weixin.qq.com([^/]*)/i.test(value),
                    msg: '文章链接地址不正确，目前只支持公众号文章'
                }
            },
            uploadHeaders: {
                'X-CSRF-TOKEN': window.laravelCsrfToken
            },
        }
  },
  methods: {
    showLoading () {
      this.$vux.loading.show({
        text: 'Loading'
      })
      setTimeout(() => {
        this.$vux.loading.hide()
      }, 2000)
    },
    onEvent (event) {
      console.log('on', event) 
    },
     getFormValues () {
        // alert(1)
        // this.form.link = this.$refs.link.value
    },
    submit(formName) {
            let _this = this;
            //let valid = valid();
            //_this.$refs[formName].validate((valid) => {
            console.log('form'+this.form.link);
            let valid = this.validlink(this.form.link);
            console.log(valid.valid)
            if (valid.valid) {
                axios.post('/articles', { 'data': this.form, 'onDownloadProgress':this.onEvent() }).then(response => {
                    let {status, data, message} = response.data;
                    if (!status) {
                        this.$vux.alert.show({
                            // title: 'Vux is Cool',
                            content: '操作失败',
                        })
                        return false;
                    }
                    this.$vux.alert.show({
                        // title: 'Vux is Cool',
                        content: '操作成功',
                    })
                    _this.$router.push({ path: '/footmark' });
                });
            } else {
                this.$vux.alert.show({
                    // title: 'Vux is Cool',
                    content: valid.msg,
                })
                return false;
            }
            //});
        },
  }
}
</script>