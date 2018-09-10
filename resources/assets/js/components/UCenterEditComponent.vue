<template>

<div>
<form name="form" ref="form">
<group>
<x-input title="姓名" v-model="user.name" name="username" placeholder="请输入姓名" is-type="china-name"></x-input>
</group>

<group>
<x-input title="手机号码" v-model="user.mobile" name="mobile" placeholder="请输入手机号码" keyboard="number" is-type="china-mobile"></x-input>
</group>
<group>
    <v-distpicker  wrapper="address-wrapper" :province="user.province" :city="user.city" :area="user.district"  @province="selectProvince" @city="selectCity" @area="selectArea"></v-distpicker>
</group>

<group>
    <selector placeholder="请选择从业公司" v-model="user.company" title="从业公司" name="company" :options="list" ></selector>
</group>

<group title="选填(以下信息会显示在名片上，让用户更了解你)"></group> 
<group>
    <cell title="入行日期">
        <datetime type="date"  v-model="user.work_date" ></datetime>
    </cell>
</group>
<group>
<x-input title="职称" v-model="user.positional" name="positional" placeholder="请输入职称" ></x-input>
</group>
<group>
<cell title="个人微信二维码" link="/user-figureurl" value="请上传"></cell>
</group>

<group>
<x-textarea title="个人简介" v-model="user.summary"></x-textarea>
</group>

<box gap="10px 10px 20px">
<x-button type="primary" action-type="button" @click.native="submit('form')" headers="uploadHeaders">保存</x-button>
</box>
</form>
</div>

</template>
<script>
import {  XInput,Group, GroupTitle, Cell, Selector,XTextarea,Box,XButton,AlertPlugin } from 'vux'
import VDistpicker from 'v-distpicker' 
import { Datetime } from 'vue-datetime'
import 'vue-datetime/dist/vue-datetime.css'
export default {
  components: {
    XInput,
    Group,    
    GroupTitle, 
    VDistpicker, 
    Cell,
    Selector,
    Datetime,
    XTextarea,
    Box,
    AlertPlugin,
    XButton
  },
  data () {
        return {
            list: [
                '平安',
                '太平洋',
                '中国人寿'
            ],
           data : {
           },
           user: {
                id: 0,
                mobile : '',
                name: '',
                figureurl:'/images/default.jpg',
                sex:0,
                vip_level :0,
                vip_time :'',
                article_count : 0,
                coin_count : 0,
                customer_count : 0, 
                company : '',
                summary : '',
                positional : '',
                province: '', // 可以通过名字，不需要填写完整
                city: '', // 也可以通过 (number) code
                district: '', // 填写错误的信息默认不处理
                work_date: '',
                
            },
            uploadHeaders: {
                'X-CSRF-TOKEN': window.laravelCsrfToken
            },
            valid: function (value) {
                return {
                    valid: true,
                    msg: '参数错误'
                }
            },
        }
  },
  mounted() {
        this.getUserInfo();
  },
  methods: {
    getUserInfo () {
        let _this = this;
        // let paramsData = { 'data': { 'searchForm': _this.searchForm } };
        axios.get('/api/profiles?base=1').then(response => {
            let { status, data, message } = response.data;
            _this.user = data;
            // _this.options = data.options;
            // _this.$refs.pagination.pageData.per_page = parseInt(data.lists.per_page);
            // _this.$refs.pagination.pageData.current_page = parseInt(data.lists.current_page);
            // _this.$refs.pagination.pageData.total = parseInt(data.lists.total);
        })
    },
    onItemClick () {
      console.log('on')
    },
    onImgError (item, $event) {
      console.log(item, $event)
    },
    onEvent (event) {
      console.log('on', event)
    },
    change (value) {
        console.log(value)
        
    },
    selectProvince(value) {
      this.user.province = value.value
      console.log(value);
    },
    selectCity(value) {
      this.user.city = value.value
      console.log(value);
    },
    selectArea(value) {
      this.user.district = value.value
      console.log(value);
    },
    submit(formName) {
            let _this = this;
            let valid = this.valid(this.form);
            if (valid.valid) {
                axios.put('/api/profiles', { 'data': this.user, 'onDownloadProgress':this.onEvent() }).then(response => {
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
                    _this.$router.push({ path: '/ucenter' });
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
<style lang="less">
    .weui-grid {
        padding: 0px;
    }
    
    .vdatetime-input {
        padding: 8px 10px;
        font-size: 16px;
        border: solid 1px #ddd;
        color: #444;
    }
</style>